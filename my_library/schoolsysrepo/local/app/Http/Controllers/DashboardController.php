<?php

namespace App\Http\Controllers;

use App;
use Artisan;
use Auth;
use Charts;
use DB;
use SMS;
use App\User;
// use Codedge\Fpdf\Facades\Fpdf;

// use Illuminate\Support\Facades\App;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {


        $data['active_class'] = 'dashboard';
        /**
         * Check the role of user
         * Redirect the user as per the eligiblity
         */
        $obj['name'] = "John";

        $user = getUserRecord();

        $data['layout'] = getLayout();
        $data['title'] = getPhrase('dashboard');
        $role = getRoleData($user->role_id);

        if ($role == 'admin' || $role == 'owner' || $role == 'staff'
            || $role == 'librarian' || $role == 'assistant_librarian' || $role == 'educational_supervisor' || $role == 'secondary_parent' || $role == 'student_guide') {


            if ($role == 'librarian' || $role == 'assistant_librarian') {
                $records = (object)App\LibraryMaster::getAssetsCount();
                $asset_names = $records->names;
                $assets_count = $records->count;


                $chart = Charts::create('pie', 'chartjs')
                    ->title(getPhrase('assets_information'))
                    ->labels($asset_names)
                    ->values($assets_count)
                    ->responsive(false)
                    ->height(300)
                    ->width(0);
                $data['asset_charts'] = $chart;

            }

            if ($role == 'admin' || $role == 'owner') {

                Artisan::call('view:clear');
                $roles = App\Role::get()->pluck('id');

                $dataset = [];
                $labels = [];
                $bgcolor = [];
                $border_color = [];
                foreach ($roles as $key => $value) {
                    $color_number = rand(0, 999);
                    $labels[] = ucfirst(getRoleData($value));
                    $dataset[] = App\User::where('role_id', '=', $value)->get()->count();
                    $bgcolor[] = getColor('', $color_number);
                    $border_color[] = getColor('background', $color_number);
                }


                $dataset_label[] = 'lbl';

                $chart_data['type'] = 'pie';
                //horizontalBar, bar, polarArea, line, doughnut, pie
                // $chart_data['title'] = getphrase('overall_users');

                $chart_data['data'] = (object)array(
                    'labels' => $labels,
                    'dataset' => $dataset,
                    'dataset_label' => $dataset_label,
                    'bgcolor' => $bgcolor,
                    'border_color' => $border_color
                );

                $data['chart_data'][] = (object)$chart_data;
                $data['chart_heading'] = getPhrase('user_statistics');


                $data['layout'] = getLayout();

                $data['right_bar'] = true;

                $data['right_bar_path'] = 'common.right-bar-chart';
                $data['right_bar_data'] = array('chart_data' => $data['chart_data']);
                $data['ids'] = array('myChart0');
                $view_page = 'admin.dashboard';
                $data['module_helper'] = getModuleHelper('owner-dashboard');
            }
            if ($role == 'staff' || $role == 'educational_supervisor') {
                $view_page = 'staff.dashboard';

                if ($role == 'educational_supervisor')
                {
                    $user = getUserWithSlug();
                    $allocated_staff = User::join('supervisors_staff', 'supervisors_staff.staff_id', '=', 'users.id')
                    ->where('supervisors_staff.supervisor_id', $user->id)
                    ->where('supervisors_staff.record_status','!=', '3')->get([
                        'users.id',
                        'users.name',
                        'users.username',
                        'users.slug'
                    ]);
                    $data['allocated_staff']=$allocated_staff;
                    $data['role']='educational_supervisor';
                }

                if ($role == 'student_guide')
                {
                    $data['role']='student_guide';
                }
                if ($role == 'staff')
                {
                    $data['role']='staff';
                    $data['right_bar'] = true;
                    $data['right_bar_path'] = 'staff.right-bar';
                    $data['right_bar_data'] = array();
                }

            }
            if ($role == 'student_guide')
            {
                $view_page = 'student_guide.dashboard';
                $data['role']='student_guide';
            }

            if ($role == 'assistant_librarian' || $role == 'librarian') {
                $view_page = 'library.librarydashboard.dashboard';

            }
            if ($role == 'secondary_parent') {
              $view_page = 'users.secondary-parent.dashboard';
              $data['role'] = $role;
            }


            return view($view_page, $data);
        } else {
            if ($role == 'parent') {
                $user = getUserWithSlug();
                $data['user'] = $user;
                $data['latest_quizzes'] = $this->getLatestQuizzes();
                $data['latest_series'] = $this->getLatestLmsSeries();
                return view('parent.dashboard', $data);
            } else {
                if ($role == 'student') {

                    $user = Auth::user();
                    //Overall performance Report
                    $resultObject = new App\QuizResult();
                    $records = $resultObject->getOverallSubjectsReport($user);
                    $color_correct = getColor('background', rand(0, 999));
                    $color_wrong = getColor('background', rand(0, 999));
                    $color_not_attempted = getColor('background', rand(0, 999));
                    $correct_answers = 0;
                    $wrong_answers = 0;
                    $not_answered = 0;

                    $data['right_bar']            = TRUE;

                    $data['right_bar_path']       = 'search.student.dashboard-right-bar';
                    $data['right_bar_data']       = array('chart_data' =>'' );

                    foreach ($records as $record) {
                        $record = (object)$record;
                        $correct_answers += $record->correct_answers;
                        $wrong_answers += $record->wrong_answers;
                        $not_answered += $record->not_answered;

                    }

                    $labels = [getPhrase('correct'), getPhrase('wrong'), getPhrase('not_answered')];
                    $dataset = [$correct_answers, $wrong_answers, $not_answered];
                    $dataset_label[] = 'lbl';
                    $bgcolor = [$color_correct, $color_wrong, $color_not_attempted];
                    $border_color = [$color_correct, $color_wrong, $color_not_attempted];
                    $chart_data['type'] = 'pie';
                    //horizontalBar, bar, polarArea, line, doughnut, pie
                    $chart_data['title'] = getphrase('overall_performance');

                    $chart_data['data'] = (object)array(
                        'labels' => $labels,
                        'dataset' => $dataset,
                        'dataset_label' => $dataset_label,
                        'bgcolor' => $bgcolor,
                        'border_color' => $border_color
                    );

                    $data['chart_data'][] = (object)$chart_data;

                    //Best scores in each quizzes
                    $records = $resultObject->getOverallQuizPerformance($user);
                    $labels = [];
                    $dataset = [];
                    $bgcolor = [];
                    $bordercolor = [];

                    foreach ($records as $record) {
                        $color_number = rand(0, 999);
                        $record = (object)$record;
                        $labels[] = $record->title;
                        $dataset[] = $record->percentage;
                        $bgcolor[] = getColor('background', $color_number);
                        $bordercolor[] = getColor('border', $color_number);
                    }

                    $labels = $labels;
                    $dataset = $dataset;
                    $dataset_label = getPhrase('performance');
                    $bgcolor = $bgcolor;
                    $border_color = $bordercolor;
                    $chart_data['type'] = 'bar';
                    //horizontalBar, bar, polarArea, line, doughnut, pie
                    $chart_data['title'] = getPhrase('best_performance_in_all_quizzes');

                    $chart_data['data'] = (object)array(
                        'labels' => $labels,
                        'dataset' => $dataset,
                        'dataset_label' => $dataset_label,
                        'bgcolor' => $bgcolor,
                        'border_color' => $border_color
                    );

                    $data['chart_data'][] = (object)$chart_data;


                    return view('student.dashboard', $data);
                }
            }
        }

    }

    public function getLatestQuizzes()
    {
        $user = Auth::user();
        $interested_categories = null;
        if ($user->settings) {
            $interested_categories = json_decode($user->settings)->user_preferences;
        }
        $quizzes = [];

        if ($interested_categories) {
            if (count($interested_categories->quiz_categories)) {
                $quizzes = App\Quiz::whereIn('category_id',
                    (array)$interested_categories->quiz_categories)
                    ->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            }
        } else {
            $quizzes = App\Quiz::orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        return $quizzes;
    }

    public function getLatestLmsSeries()
    {
        $user = Auth::user();
        $interested_categories = null;
        if ($user->settings) {
            $interested_categories = json_decode($user->settings)->user_preferences;
        }
        $series = [];

        if ($interested_categories) {
            if (count($interested_categories->lms_categories)) {
                $series = App\LmsSeries::whereIn('lms_category_id',
                    (array)$interested_categories->lms_categories)
                    ->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            }
        } else {
            $series = App\LmsSeries::orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        return $series;
    }


    public function getPaymentStats()
    {
        $paymentObject = new App\Payment();
        $payment_data = (object)$paymentObject->getSuccessFailedCount();


        $payment_dataset = [$payment_data->success_count, $payment_data->cancelled_count, $payment_data->pending_count];
        $payment_labels = [getPhrase('success'), getPhrase('cancelled'), getPhrase('pending')];
        $payment_dataset_labels = [getPhrase('total')];

        $payment_bgcolor = [getColor('', 4), getColor('', 9), getColor('', 18)];
        $payment_border_color = [getColor('background', 4), getColor('background', 9), getColor('background', 18)];

        $payments_stats['data'] = (object)array(
            'labels' => $payment_labels,
            'dataset' => $payment_dataset,
            'dataset_label' => $payment_dataset_labels,
            'bgcolor' => $payment_bgcolor,
            'border_color' => $payment_border_color
        );
        $payments_stats['type'] = 'bar';
        $payments_stats['title'] = getPhrase('overall_statistics');

        return $payments_stats;
    }

    /**
     * This method returns the overall monthly summary of the payments made with status success
     * @return [type] [description]
     */
    public function getPaymentMonthlyStats()
    {

        $paymentObject = new App\Payment();
        $payment_data = (object)$paymentObject->getSuccessMonthlyData();


        $payment_dataset = [];
        $payment_labels = [];
        $payment_dataset_labels = [getPhrase('total')];
        $payment_bgcolor = [];
        $payment_border_color = [];


        foreach ($payment_data as $record) {
            $color_number = rand(0, 999);;
            $payment_dataset[] = $record->total;
            $payment_labels[] = $record->month;
            $payment_bgcolor[] = getColor('', $color_number);
            $payment_border_color[] = getColor('background', $color_number);

        }

        $payments_stats['data'] = (object)array(
            'labels' => $payment_labels,
            'dataset' => $payment_dataset,
            'dataset_label' => $payment_dataset_labels,
            'bgcolor' => $payment_bgcolor,
            'border_color' => $payment_border_color
        );
        $payments_stats['type'] = 'line';
        $payments_stats['title'] = getPhrase('payments_reports_in') . ' ' . getCurrencyCode();

        return $payments_stats;
    }

    public function getDemandingQuizzes($type = '')
    {
        $quizResultObject = new App\QuizResult();
        $usage = $quizResultObject->getQuizzesUsage($type);

        $summary_dataset = [];
        $summary_labels = [];
        $summary_dataset_labels = [getPhrase('total')];
        $summary_bgcolor = [];
        $summary_border_color = [];


        foreach ($usage as $record) {
            $color_number = rand(0, 999);;
            $summary_dataset[] = $record->total;
            $summary_labels[] = $record->quiz_title;
            $summary_bgcolor[] = getColor('', $color_number);
            $summary_border_color[] = getColor('background', $color_number);

        }

        $quiz_stats['data'] = (object)array(
            'labels' => $summary_labels,
            'dataset' => $summary_dataset,
            'dataset_label' => $summary_dataset_labels,
            'bgcolor' => $summary_bgcolor,
            'border_color' => $summary_border_color
        );
        $quiz_stats['type'] = 'doughnut';
        $quiz_stats['title'] = getPhrase('demanding_quizzes');
        if ($type != '') {
            $quiz_stats['title'] = getPhrase('demanding') . ' ' . $type . ' ' . getPhrase('quizzes');
        }

        return $quiz_stats;
    }

}
