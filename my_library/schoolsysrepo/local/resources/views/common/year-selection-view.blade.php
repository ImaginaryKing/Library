<?php

$user_record = null;
$academic_id = null;
$course_parent_id = null;
$course_id = null;
$current_year = 1;
$current_semister = 0;
$custom_class = '';
if (isset($class)) {
    $custom_class = $class;
}

if (isset($user_slug)) {
    if (getRole() == 'student') {
        $user_record = getUserSession();
    } else {
        $user_record = prepareStudentSessionRecord($user_slug);
    }

    if ($user_record) {

        $academic_id = $user_record->student->academic_id;
        $course_parent_id = $user_record->student->course_parent_id;
        $course_id = $user_record->student->course_id;
        $year = $user_record->student->current_year;
        $semister = $user_record->student->current_semister;
    } else {
        $academic_id=new App\Academic();
        $academic_id=$academic_id->getCurrentAcademic()->id;
        //$course_parent_id = getDefaultParentCourseId();
        $course_parent_id = new App\AcademicSemester();
        $course_parent_id = $course_parent_id->getCurrentSemeterOfAcademicYear($currentAcademic)->sem_num;
        $course_id = '';
        $year = '';
        $semister = '';
    }

} else {
    $academic_id=new App\Academic();
    $academic_id=$academic_id->getCurrentAcademic()->id;
    $course_parent_id = new App\AcademicSemester();
    $course_parent_id = $course_parent_id->getCurrentSemeterOfAcademicYear($currentAcademic)->sem_num;
    $course_id = '';
    $year = '';
    $semister = '';
}

?>

<div class="row {{$custom_class}}">
    <div class="col-md-12">

            <fieldset class="form-group"
                      ng-init="setPreSelectedData('{{$academic_id}}','{{$course_parent_id}}','{{$course_id}}', '{{$year}}','{{$semister}}')">
                {{ Form::label ('academic_year', getphrase('academic_year')) }}
                {{ Form::select('academic_id', $academic_years, null,
                [   'class'     => 'form-control',
                    "id"        => "select_academic_year",
                    "ng-model"  => "academic_year",
                    "ng-change" => "getAcadmicSemester(academic_year)"
                ])}}
            </fieldset>

            <fieldset  class="form-group" ng-show="graduation_page">
                <label for="course_parent_id">{{getPhrase('branch')}}</label>
                <select
                        name="course_parent_id"
                        id="course_parent_id"
                        class="form-control"
                        ng-model="course_parent_id"
                        ng-change="getChildCourses(academic_year, course_parent_id)"
                        ng-options="option.id as option.course_title for option in parent_courses | filter: { graduated_course: 1  } track by option.id ">
                    <option value="">{{getPhrase('select')}}</option>
                </select>
            </fieldset>
             <fieldset ng-if="have_semisters" class="form-group">

            <label for="semister">{{getPhrase('semester')}}</label>

            <select
                    name="current_semister"
                    class="form-control"
                    ng-model="semisters.current_semister"
                    ng-options="v for v in semisters.values track by v"
                    ng-change="semisterChanged(semisters.current_semister)"
            >
            </select>

        </fieldset>
            <fieldset  class="form-group" ng-show="!graduation_page">
                <label for="course_parent_id">{{getPhrase('branch')}}</label>
                <select
                        name="course_parent_id"
                        id="course_parent_id"
                        class="form-control"
                        ng-model="course_parent_id"
                        ng-change="getChildCourses(academic_year, course_parent_id)"
                        ng-options="option.id as option.course_title for option in parent_courses  track by option.id ">
                    <option value="">{{getPhrase('select')}}</option>
                </select>
            </fieldset>

        {{--?????????? ??????????????--}}
        <fieldset ng-if="selected_course_parent_id" class="form-group">
            <label for="course_id">{{getPhrase('course')}}</label>
            <select
                    name="course_id"
                    id="course_id"
                    class="form-control"
                    ng-model="course_id"
                    ng-change="prepareYears(course_id)"

                    ng-options="option.id as option.course_title for option in courses track by option.id">
                <option value="">{{getPhrase('select')}}</option>
            </select>
        </fieldset>

        <fieldset ng-if="years.current_year" class="form-group">

            <label for="year">{{getPhrase('year')}}</label>

            <select
                    name="current_year"
                    class="form-control"
                    ng-model="years.current_year"
                    ng-options="v for v in years.values track by v"
                    ng-change="yearChanged(years.current_year)"
            >
            </select>
        </fieldset>


       

    </div>

</div>