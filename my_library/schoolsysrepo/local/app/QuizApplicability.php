<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QuizApplicability extends Model
{
    protected $table = "quizapplicability";


    public function updateApplicability($quiz_id, $applicable_list = array())
    {
         if(!env('DEMO_MODE')) {

             DB::statement("DELETE FROM quizapplicability WHERE quiz_id = '$quiz_id'");

             if ($applicable_list != null) {
                 foreach ($applicable_list as $list) {
                     $parameters = explode('_', $list);
                     $record = new QuizApplicability();
                     $record->quiz_id = $quiz_id;
                     $record->academic_id = $parameters[0];
                     $record->course_parent_id = $parameters[1];
                     $record->course_id = $parameters[2];
                     $record->year = $parameters[3];
                     $record->semister = $parameters[4];
                     $record->save();
                 }
             }
         }
    }
}
