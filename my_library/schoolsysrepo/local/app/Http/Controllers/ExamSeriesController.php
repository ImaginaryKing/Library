<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App;
use App\Http\Requests;
use App\Quiz;
use App\Subject;
use App\QuestionBank;
use App\QuizCategory;
use App\ExamSeries;
use Yajra\Datatables\Datatables;
use DB;
use Auth;
use Image;
use ImageSettings;
use File;
use Input;
use Exception;
use Illuminate\Support\Facades\Redirect;

class ExamSeriesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        if(!Module_state('exams')){
            prepareBlockUserMessage();
            return Redirect::to('/')->send();
        }

    }

    /**
     * Course listing method
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        if (!checkRole(getUserGrade(2))) {
            prepareBlockUserMessage();
            return back();
        }

        $data['active_class'] = 'exams';
        $data['title'] = getPhrase('exam_series');
        $data['module_helper'] = getModuleHelper('exam-series-list');
        return view('exams.examseries.list', $data);
    }


    /**
     * This method returns the datatables data to view
     * @return [type] [description]
     */
    public function getDatatable()
    {

        if (!checkRole(getUserGrade(3))) {
            prepareBlockUserMessage();
            return back();
        }

        $records = array();

        if(is_teacher()){
         //created_by_user
            $records = ExamSeries::join('quizcategories','examseries.category_id','=','quizcategories.id')
                ->join('courses','quizcategories.course_id','=','courses.id')
                ->select([
                'examseries.title',
                'quizcategories.category',
                'courses.course_title',
                'examseries.image',
                'examseries.is_paid',
                'examseries.cost',
                'examseries.validity',
                'examseries.total_exams',
                'examseries.total_questions',
                'examseries.slug',
                'examseries.id',
                'examseries.created_by_user',
                'examseries.updated_by_user',
                'examseries.created_by_ip',
                'examseries.updated_by_ip',
                'examseries.created_at',
                'examseries.updated_at'
            ])
                ->where('examseries.created_by_user','=',Auth::user()->id);
                // ->orderBy('updated_at', 'desc');
        }
        else {
            $records = ExamSeries::join('quizcategories','examseries.category_id','=','quizcategories.id')
                ->join('courses','quizcategories.course_id','=','courses.id')
                ->select([
                'examseries.title',
                'quizcategories.category',
                'courses.course_title',
                'examseries.image',
                'examseries.is_paid',
                'examseries.cost',
                'examseries.validity',
                'examseries.total_exams',
                'examseries.total_questions',
                'examseries.slug',
                'examseries.id',
                'examseries.created_by_user',
                'examseries.updated_by_user',
                'examseries.created_by_ip',
                'examseries.updated_by_ip',
                'examseries.created_at',
                'examseries.updated_at'
            ])
                ->where('courses.category_id',Auth::user()->category_id);
                // ->orderBy('updated_at', 'desc');
        }

        return Datatables::of($records)
            ->addColumn('action', function ($records) {

                $records->created_by_user_name = App\User::get_user_name($records->created_by_user);
                $records->updated_by_user_name = App\User::get_user_name($records->updated_by_user);
                $view = "<li><a onclick='pop_it($records)'><i class=\"fa fa-eye\"></i>" . getPhrase('view_record_history') . "</a></li>";

                $link_data = '<div class="dropdown more">
                        <a id="dLabel" type="button" class="more-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dLabel">
                           <li><a href="' . URL_EXAM_SERIES_UPDATE_SERIES . $records->slug . '"><i class="fa fa-spinner" aria-hidden="true"></i>' . getPhrase("update_quizzes") . '</a></li>
                            <li><a href="' . URL_EXAM_SERIES_EDIT . $records->slug . '"><i class="fa fa-pencil"></i>' . getPhrase("edit") . '</a></li>' . $view;

                $temp = '';
                if (checkRole(getUserGrade(1))) {
                    $temp .= ' <li><a href="javascript:void(0);" onclick="deleteRecord(\'' . $records->slug . '\');"><i class="fa fa-trash"></i>' . getPhrase("delete") . '</a></li>';
                }

                $temp .= '</ul></div>';


                $link_data .= $temp;
                return $link_data;
            })
            ->editColumn('title', function ($records) {
                return '<a href="' . URL_EXAM_SERIES_UPDATE_SERIES . $records->slug . '">' . $records->title . '</a>';
            })
            ->editColumn('cost', function ($records) {
                return ($records->is_paid) ? $records->cost : '-';
            })
            ->editColumn('validity', function ($records) {
                return ($records->is_paid) ? $records->validity : '-';
            })
            ->editColumn('image', function ($records) {
                $image_path = IMAGE_PATH_UPLOAD_LMS_DEFAULT;
                if ($records->image) {
                    $image_path = IMAGE_PATH_UPLOAD_SERIES . $records->image;
                }
                return '<img src="' . $image_path . '" height="60" width="60"  />';
            })
            ->editColumn('is_paid', function ($records) {
                return ($records->is_paid) ? '<span class="label label-primary">' . getPhrase('paid') . '</span>' : '<span class="label label-success">' . getPhrase('free') . '</span>';
            })
            ->removeColumn('created_by_user')
            ->removeColumn('updated_by_user')
            ->removeColumn('created_by_ip')
            ->removeColumn('updated_by_ip')
            ->removeColumn('created_at')
            ->removeColumn('updated_at')
            ->removeColumn('id')
            ->removeColumn('validity')
            ->removeColumn('slug')
            ->removeColumn('updated_at')
            ->make();
    }

    /**
     * This method loads the create view
     * @return void
     */
    public function create()
    {
        if (!checkRole(getUserGrade(3))) {
            prepareBlockUserMessage();
            return back();
        }
        $data['record'] = false;
        $data['categories'] = array_pluck(QuizCategory::all(), 'category', 'id');
        $data['active_class'] = 'exams';
        $data['title'] = getPhrase('add_exam_series');
        $current_year = default_year();
        $data['branches']   = array_pluck(getCourses($current_year), 'course_title', 'id');
        $data['is_paid'] = null;
        $data['recored_title']   = null;
        return view('exams.examseries.add-edit', $data);
    }

    /**
     * This method loads the edit view based on unique slug provided by user
     * @param  [string] $slug [unique slug of the record]
     * @return [view with record]
     */
    public function edit($slug)
    {
        if (!checkRole(getUserGrade(3))) {
            prepareBlockUserMessage();
            return back();
        }

        $record = ExamSeries::getRecordWithSlug($slug);

        if ($isValid = $this->isValidRecord($record)) {
            return redirect($isValid);
        }
        $data['record'] = $record;
        $data['active_class'] = 'exams';
        $data['settings'] = false;
        $data['categories'] = array_pluck(QuizCategory::all(), 'category', 'id');
        $current_year = default_year();
        $data['branches']   = array_pluck(getCourses($current_year), 'course_title', 'id');

        $data['is_paid'] = $record->is_paid;
        $data['recored_title']   = $record->title;

        $data['title'] = getPhrase('edit_series');
        return view('exams.examseries.add-edit', $data);
    }

    /**
     * Update record based on slug and reuqest
     * @param  Request $request [Request Object]
     * @param  [type]  $slug    [Unique Slug]
     * @return void
     */
    public function update(Request $request, $slug)
    {
        if (!checkRole(getUserGrade(3))) {
            prepareBlockUserMessage();
            return back();
        }

        $record = ExamSeries::getRecordWithSlug($slug);
        $rules = [
            'title' => 'bail|required|max:60',

        ];
        /**
         * Check if the title of the record is changed,
         * if changed update the slug value based on the new title
         */
        $name = $request->title;
        if ($name != $record->title) {
            $record->slug = $record->makeSlug($name);
        }

        //Validate the overall request
        $this->validate($request, $rules);
        $record->title = $name;
        $record->slug = $record->makeSlug($name);
        $record->is_paid = $request->is_paid;
        $record->category_id = $request->category_id;
        $record->validity = -1;
        $record->cost = 0;
        if ($request->is_paid) {
            $record->validity = $request->validity;
            $record->cost = $request->cost;
        }
        $record->total_exams = $request->total_exams;
        $record->total_questions = $request->total_questions;

        $record->short_description = $request->short_description;
        $record->description = $request->description;
        $record->start_date = $request->start_date;
        $record->end_date = $request->end_date;
        $record->record_updated_by = Auth::user()->id;
        $record->update_stamp($request);
        $record->save();
        $file_name = 'image';
        if ($request->hasFile($file_name)) {

            $rules = array($file_name => 'mimes:jpeg,jpg,png,gif|max:10000');
            $this->validate($request, $rules);
            $examSettings = getExamSettings();
            $path = $examSettings->seriesImagepath;
            $this->deleteFile($record->image, $path);
            $record->image = $this->processUpload($request, $record, $file_name);
            $record->update_stamp($request);
            $record->save();
        }
        flash(getPhrase('success'), getPhrase('record_updated_successfully'), 'success');
        return redirect(URL_EXAM_SERIES);
    }

    /**
     * This method adds record to DB
     * @param  Request $request [Request Object]
     * @return void
     */
    public function store(Request $request)
    {
        if (!checkRole(getUserGrade(3))) {
            prepareBlockUserMessage();
            return back();
        }

        $rules = [
            'title' => 'bail|required|max:60',
        ];
        // dd($request);
        $this->validate($request, $rules);
        $record = new ExamSeries();
        $name = $request->title;
        $record->title = $name;
        $record->slug = $record->makeSlug($name);
        $record->is_paid = $request->is_paid;
        $record->validity = -1;
        $record->cost = 0;
        if ($request->is_paid) {
            $record->validity = $request->validity;
            $record->cost = $request->cost;
        }

        $record->total_exams = $request->total_exams;
        $record->total_questions = $request->total_questions;
        $record->category_id = $request->category_id;

        $record->short_description = $request->short_description;
        $record->description = $request->description;
        $record->start_date = $request->start_date;
        $record->end_date = $request->end_date;
        $record->record_updated_by = Auth::user()->id;
        $record->user_stamp($request);
        $record->save();
        $file_name = 'image';
        if ($request->hasFile($file_name)) {

            $rules = array($file_name => 'mimes:jpeg,jpg,png,gif|max:10000');
            $this->validate($request, $rules);
            $examSettings = getExamSettings();
            $path = $examSettings->seriesImagepath;
            $this->deleteFile($record->image, $path);
            $record->image = $this->processUpload($request, $record, $file_name);
            $record->user_stamp($request);
            $record->save();
        }
        flash(getPhrase('success'), getPhrase('record_added_successfully'), 'success');
        return redirect(URL_EXAM_SERIES);
    }

    public function deleteFile($record, $path, $is_array = false)
    {
        if (!env('DEMO_MODE')) {
            $files = array();
            $files[] = $path . $record;
            File::delete($files);
        }
    }

    /**
     * This method process the image is being refferred
     * by getting the settings from ImageSettings Class
     * @param  Request $request [Request object from user]
     * @param  [type]  $record    [The saved record which contains the ID]
     * @param  [type]  $file_name [The Name of the file which need to upload]
     * @return [type]             [description]
     */
    public function processUpload(Request $request, $record, $file_name)
    {
        if (env('DEMO_MODE')) {
            return;
        }
        if ($request->hasFile($file_name)) {
            $examSettings = getExamSettings();

            $imageObject = new ImageSettings();

            $destinationPath = $examSettings->seriesImagepath;
            $destinationPathThumb = $examSettings->seriesThumbImagepath;

            $fileName = $record->id . '-' . $file_name . '.' . $request->$file_name->guessClientExtension();

            $request->file($file_name)->move($destinationPath, $fileName);

            //Save Normal Image with 300x300
            Image::make($destinationPath . $fileName)->fit($examSettings->imageSize)->save($destinationPath . $fileName);


            Image::make($destinationPath . $fileName)->fit($imageObject->getThumbnailSize())->save($destinationPathThumb . $fileName);
            return $fileName;

        }
    }

    /**
     * Delete Record based on the provided slug
     * @param  [string] $slug [unique slug]
     * @return Boolean
     */
    public function delete($slug)
    {
        if (!checkRole(getUserGrade(3))) {
            prepareBlockUserMessage();
            return back();
        }
        /**
         * Delete the questions associated with this quiz first
         * Delete the quiz
         * @var [type]
         */
        $record = ExamSeries::where('slug', $slug)->first();

        try {
            if (!env('DEMO_MODE')) {
                $record->delete();
            }
            $response['status'] = 1;
            $response['message'] = getPhrase('record_deleted_successfully');
        } catch (\Illuminate\Database\QueryException $e) {
            $response['status'] = 0;
            if (getSetting('show_foreign_key_constraint', 'module')) {
                $response['message'] = $e->errorInfo;
            } else {
                $response['message'] = getPhrase('this_record_is_in_use_in_other_modules');
            }
        }
        return json_encode($response);

    }

    public function isValidRecord($record)
    {
        if ($record === null) {

            flash(getPhrase('Ooops'), getPhrase("page_not_found"), 'error');
            return $this->getRedirectUrl();
        }

        return false;
    }

    public function getReturnUrl()
    {
        return URL_EXAM_SERIES;
    }


    /**
     * Returns the list of subjects based on the requested subject
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getExams(Request $request)
    {

        $category_id = $request->category_id;
        if(is_teacher()){
            $exams = Quiz::where('quizzes.category_id', '=', $category_id)
                ->join('subjectpreferences','quizzes.subject_id','=','subjectpreferences.subject_id')
                ->where('subjectpreferences.user_id','=',Auth::user()->id)
                ->where('quizzes.total_marks', '!=', '0')
                ->get();
        }else {
            $exams = Quiz::where('category_id', '=', $category_id)
                ->where('total_marks', '!=', '0')
                ->get();
        }
        return json_encode(array('exams' => $exams));
    }

    /**
     * Updates the questions in a selected quiz
     * @param  [type] $slug [description]
     * @return [type]       [description]
     */
    public function updateSeries($slug)
    {

        if (!checkRole(getUserGrade(3))) {
            prepareBlockUserMessage();
            return back();
        }

        /**
         * Get the Quiz Id with the slug
         * Get the available questions from questionbank_quizzes table
         * Load view with this data
         */
        $record = ExamSeries::getRecordWithSlug($slug);

        $data['record'] = $record;
        $data['active_class'] = 'exams';
        $data['right_bar'] = true;
        $data['right_bar_path'] = 'exams.examseries.right-bar-update-questions';

        $data['settings'] = false;
        $previous_records = array();
        if ($record->total_exams > 0) {
            $quizzes = DB::table('examseries_data')
                ->where('examseries_id', '=', $record->id)
                ->get();
            foreach ($quizzes as $quiz) {
                $temp = array();
                $temp['id'] = $quiz->quiz_id;
                $quiz_details = Quiz::where('id', '=', $quiz->quiz_id)->first();

                $temp['dueration'] = $quiz_details->dueration;
                $temp['total_marks'] = $quiz_details->total_marks;
                $temp['total_questions'] = $quiz_details->total_questions;
                $temp['title'] = $quiz_details->title;

                array_push($previous_records, $temp);
            }
            $settings['exams'] = $previous_records;
            $settings['total_questions'] = $record->total_questions;
            $data['settings'] = json_encode($settings);
        }


        $data['exam_categories'] = array_pluck(App\QuizCategory::all(),
            'category', 'id');

        $data['title'] = getPhrase('update_series_for') . ' ' . $record->title;
        $data['module_helper'] = getModuleHelper('update-quizzes-in-series');
        return view('exams.examseries.update-questions', $data);

    }

    public function storeSeries(Request $request, $slug)
    {

        if (!checkRole(getUserGrade(3))) {
            prepareBlockUserMessage();
            return back();
        }

        $exam_series = ExamSeries::getRecordWithSlug($slug);

        $series_id = $exam_series->id;
        $quizzes = json_decode($request->saved_series);
        $questions = 0;
        $exams = 0;
        $quizzes_to_update = array();
        foreach ($quizzes as $record) {
            $temp = array();
            $temp['quiz_id'] = $record->id;
            $temp['examseries_id'] = $series_id;
            array_push($quizzes_to_update, $temp);
            $questions += $record->total_questions;
        }
        $exam_series->total_questions = $questions;
        $exam_series->total_exams = count($quizzes);

        //Clear all previous questions
        if (!env('DEMO_MODE')) {
            DB::table('examseries_data')->where('examseries_id', '=', $series_id)->delete();
        }
        //Insert New Questions
        DB::table('examseries_data')->insert($quizzes_to_update);
        $exam_series->update_stamp($request);
        $exam_series->save();
        flash(getPhrase('success'), getPhrase('record_updated_successfully'), 'success');
        return redirect(URL_EXAM_SERIES);
    }

    /**
     * This method lists all the available exam series for students
     *
     * @return [type] [description]
     */
    public function listSeries()
    {
        if (!checkRole(getUserGrade(13))) {
            prepareBlockUserMessage();
            return back();
        }
        $data['active_class'] = 'exams';
        $data['title'] = getPhrase('exam_series');
        $data['series'] = [];
        $user = Auth::user();
        $interested_categories = null;
        if ($user->settings) {
            $interested_categories = json_decode($user->settings)->user_preferences;
        }

        if ($interested_categories) {
            if (count($interested_categories->quiz_categories)) {
                $data['series'] = ExamSeries::where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->whereIn('category_id', (array)$interested_categories->quiz_categories)
                    ->paginate(getRecordsPerPage());
            }
        }
        $data['layout'] = getLayout();
        $data['user'] = $user;
        return view('student.exams.exam-series-list', $data);
    }

    /**
     * This method displays all the details of selected exam series
     * @param  [type] $slug [description]
     * @return [type]       [description]
     */
    public function viewItem($slug)
    {
        $record = ExamSeries::getRecordWithSlug($slug);

        if ($isValid = $this->isValidRecord($record)) {
            return redirect($isValid);
        }

        $data['active_class'] = 'exams';
        $data['pay_by'] = '';
        $data['content_record'] = false;
        $data['title'] = $record->title;
        $data['item'] = $record;
        $data['right_bar'] = true;
        $data['right_bar_path'] = 'student.exams.exam-series-item-view-right-bar';
        $data['right_bar_data'] = array(
            'item' => $record,
        );

        $data['layout'] = getLayout();
        return view('student.exams.series.series-view-item', $data);
    }

}
