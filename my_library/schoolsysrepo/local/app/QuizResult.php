<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;
use App\Scopes\DeleteScope;
use App\Scopes\BranchScope;
// use Spatie\Activitylog\LogsActivityInterface;
// use Spatie\Activitylog\Traits\LogsActivity;

class QuizResult extends Model  
{
    protected $table = 'quizresults';
    
    public static function getRecordWithSlug($slug)
    {
        return QuizResult::where('slug', '=', $slug)->first();
    }


    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new DeleteScope);
        static::addGlobalScope(new BranchScope);
    }

	/**
	 * Returns the history of exam attempts based on the current logged in user
	 * @return [type] [description]
	 */
    public function getHistory()
    {
    	return QuizResult::where('user_id', '=', Auth::user()->id)->orderBy('id', 'DESC')->limit(5)->get();
    }

    /**
     * Returns the list of toppers based on the highest 
     * percentage scored and the current quiz
     * @param  string $quiz_id [description]
     * @return [type]          [description]
     */
    public function getToppersList($quiz_id='')
    {
    	$list = array();
    	if($quiz_id=='')
    		return $list;
 
    	return QuizResult::
                         where('quiz_id', '=', $quiz_id)
    					->where('exam_status', '=', 'pass')
    					->orderBy('percentage', 'DESC')->limit(5)
    					->groupBy('user_id')
    					->get();
    }
 	
 	/**
 	 * Returns the current result quiz record
 	 * @return [type] [description]
 	 */
    public function quizName()
    {
    	return $this->belongsTo('App\Quiz', 'quiz_id');
    }

    /**
     * Returns the current quiz user record
     * @return [type] [description]
     */
    public function getUser()
    {
    	return $this->belongsTo('App\User', 'user_id');
    }

    public function getOverallSubjectsReport($user)
    {


       $student_course = Student::where('user_id',$user->id)->pluck('course_parent_id')->first();

       $overallSubjectAnalysis = [];
            $records = Quiz::join('quizresults', 'quizzes.id', '=', 'quizresults.quiz_id')
            ->select(['subject_analysis','quizresults.user_id'])
            ->where('quizresults.user_id', '=', $user->id)
            ->where('quizresults.academic_id','=',default_year())
            ->where('quizresults.semister','=',default_sem(default_year()))
            ->where('quizresults.course_parent_id','=',$student_course)
            ->get();



       

       foreach ($records as $result) {
        if(!isset($result->subject_analysis))
            continue;
         foreach(json_decode($result->subject_analysis) as $subject)
         {

            $subject_id = $subject->subject_id;
          if(!array_key_exists($subject_id, $overallSubjectAnalysis)){
            $overallSubjectAnalysis[$subject_id]['subject_name'] = Subject::where('id','=',$subject_id)->first()->subject_title;
            $overallSubjectAnalysis[$subject_id]['correct_answers'] = 0;
            $overallSubjectAnalysis[$subject_id]['wrong_answers']   = 0;
            $overallSubjectAnalysis[$subject_id]['not_answered']    = 0;
            $overallSubjectAnalysis[$subject_id]['time_to_spend']   = 0;
            $overallSubjectAnalysis[$subject_id]['time_spent']      = 0;
            $overallSubjectAnalysis[$subject_id]['time_spent_on_correct_answers']    = 0;
            $overallSubjectAnalysis[$subject_id]['time_spent_on_wrong_answers']      = 0;
            $overallSubjectAnalysis[$subject_id]['time_spent_on_wrong_answers']      = 0;
            $overallSubjectAnalysis[$subject_id]['time_spent_on_wrong_answers']      = 0;
          }
            $overallSubjectAnalysis[$subject_id]['correct_answers'] += $subject->correct_answers;
            $overallSubjectAnalysis[$subject_id]['wrong_answers']   += $subject->wrong_answers;
            $overallSubjectAnalysis[$subject_id]['not_answered']    += $subject->not_answered;
            $overallSubjectAnalysis[$subject_id]['time_to_spend']   += $subject->time_to_spend;
            $overallSubjectAnalysis[$subject_id]['time_spent']      +=  $subject->time_spent;
            $overallSubjectAnalysis[$subject_id]['time_spent_on_correct_answers']    +=  $subject->time_spent_correct_answers;
            $overallSubjectAnalysis[$subject_id]['time_spent_on_wrong_answers']      +=  $subject->time_spent_wrong_answers;
         }
       }

       return $overallSubjectAnalysis;
    }

    /**
     * Returns the overall performanance of the user
     * @param  [type] $user [description]
     * @return [type]       [description]
     */
    public function getOverallQuizPerformance($user)
    {
        $overallQuizPerformance = [];
            $records = Quiz::join('quizresults', 'quizzes.id', '=', 'quizresults.quiz_id')
            ->select(['quiz_id', 'quizzes.title',DB::raw('Max(percentage) as percentage'), 'quizresults.user_id'])
            ->where('quizresults.user_id', '=', $user->id)
            ->groupBy('quizresults.quiz_id')

            ->get();
        return $records;
    }


    public function getQuizzesUsage($type='', $user_id='', $year='')
    {
         $query = 'select count(qr.quiz_id) as total, q.title as quiz_title from quizzes q, quizresults qr where qr.quiz_id = q.id group by qr.quiz_id';

         if($type=='paid')
         {
              $query = 'select count(qr.quiz_id) as total, q.title as quiz_title from quizzes q, quizresults qr where qr.quiz_id = q.id and q.is_paid=1 group by qr.quiz_id';              
         }
         $result = DB::select($query);
 
         return $result;
    }

    public static function latestQuizAttempts($limit = 5)
    {
        $role_id = getRoleData('student');
        $users = QuizResult::join('users', 'users.id','=','quizresults.user_id')
                            ->join('quizzes','quizzes.id','=','quizresults.quiz_id')
                            ->where('quizzes.type','=','online')
                            ->where('users.role_id','=',$role_id)
                            ->orderBy('quizresults.id','desc')
                            ->select(['users.slug as slug','users.id as user_id', 'users.name','percentage', 'image','quizzes.title'])
                            ->limit($limit)
                            ->groupBy('users.id')
                            ->get();
        return $users;
    }


    
}


