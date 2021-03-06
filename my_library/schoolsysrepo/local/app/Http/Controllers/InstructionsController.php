<?php

namespace App\Http\Controllers;

use App;
use App\Instruction;
use Auth;
use DB;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class InstructionsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Course listing method
     */
    public function index()
    {

        if (!checkRole(getUserGrade(3))) {
            prepareBlockUserMessage();
            return back();
        }

        $data['active_class'] = 'exams';
        $data['layout'] = getLayout();
        $data['title'] = getPhrase('instructions');
        $data['module_helper'] = getModuleHelper('exams-instructions-list');
        return view('exams.instructions.list', $data);
    }

    /**
     * This method returns the datatables data to view
     * @return [type] [description]
     */
    public function getDatatable($slug = '')
    {

        if (!checkRole(getUserGrade(3))) {
            prepareBlockUserMessage();
            return back();
        }

        $records = array();


        $records = Instruction::select(['title', 'content', 'id', 'slug','created_by_user','updated_by_user','created_by_ip','updated_by_ip','created_at','updated_at']);
            // ->orderBy('updated_at', 'desc');


        return Datatables::of($records)
            ->addColumn('action', function ($records) {
                $records->created_by_user_name = App\User::get_user_name($records->created_by_user);
                $records->updated_by_user_name = App\User::get_user_name($records->updated_by_user);
                $view = "<li><a onclick='pop_it($records)'><i class=\"fa fa-eye\"></i>".getPhrase('view_record_history')."</a></li>";

                $link_data = '<div class="dropdown more">
                        <a id="dLabel" type="button" class="more-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dLabel">
                           <li><a href="' . URL_INSTRUCTIONS_EDIT . $records->slug . '"><i class="fa fa-pencil"></i>' . getPhrase("edit") . '</a></li>'.$view;

                $temp = '';
                if (checkRole(getUserGrade(1))) {
                    $temp .= ' <li><a href="javascript:void(0);" onclick="deleteRecord(\'' . $records->slug . '\');"><i class="fa fa-trash"></i>' . getPhrase("delete") . '</a></li>';
                }

                $temp .= '</ul></div>';


                $link_data .= $temp;
                return $link_data;
            })

            ->removeColumn('id')
            ->removeColumn('slug')
            ->removeColumn('updated_at')

            ->removeColumn('created_by_user')
            ->removeColumn('updated_by_user')
            ->removeColumn('created_by_ip')
            ->removeColumn('updated_by_ip')
            ->removeColumn('created_at')
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
        $data['layout'] = getLayout();
        $data['active_class'] = 'exams';
        $data['title'] = getPhrase('add_instructions');
        $data['module_helper'] = getModuleHelper('add-instructions');
        return view('exams.instructions.add-edit', $data);
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

        $record = Instruction::getRecordWithSlug($slug);
        if ($isValid = $this->isValidRecord($record)) {
            return redirect($isValid);
        }

        $data['record'] = $record;
        $data['active_class'] = 'exams';
        $data['layout'] = getLayout();
        $data['title'] = getPhrase('edit_instruction');
        $data['module_helper'] = getModuleHelper('exams-instructions-list');
        return view('exams.instructions.add-edit', $data);
    }

    public function isValidRecord($record)
    {
        if ($record === null) {

            flash(getPhrase('Ooops'), getPhrase("page_not_found"), 'error');
            return $this->getRedirectUrl();
        }

        return false;
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

        $record = Instruction::getRecordWithSlug($slug);
        $rules = [
            'title' => 'bail|required|max:40',
            'content' => 'bail|required',
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


        $record->content = $request->content;
        $record->update_stamp($request);
        $record->save();
        flash(getPhrase('success'), getPhrase('record_updated_successfully'), 'success');
        return redirect(URL_INSTRUCTIONS);
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
            'title' => 'bail|required|max:40',
            'content' => 'bail|required'
        ];
        $this->validate($request, $rules);
        $record = new Instruction();
        $name = $request->title;
        $record->title = $name;
        $record->slug = $record->makeSlug($name);

        $record->content = $request->content;
        $record->user_stamp($request);
        $record->save();
        flash(getPhrase('success'), getPhrase('record_added_successfully'), 'success');
        return redirect(URL_INSTRUCTIONS);
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
         * Check if any quizzes are associated with this instructions page,
         * if not delete
         * @var [type]
         */
        $record = Instruction::where('slug', $slug)->first();
        $response = [];
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

    public function getReturnUrl()
    {
        return URL_INSTRUCTIONS;
    }

}
