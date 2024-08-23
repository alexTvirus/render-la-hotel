<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pagination Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the paginator library to build
    | the simple pagination links. You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */
    'title'=>'Team List',
    'id'=>'Team ID',
    'name'=>'Team Name',
    'po'=>'PO Name',
    'members'=>'Members Name',
    'number_of_member'=>'Number of Member',
    'msg_fails' => 'msg_fail',
    'msg_success' => 'msg_success',
    'msg_error' => 'msg_error',
    'confirm_add_team' => 'You want add new team ?',
    'msg_content' => [
        'msg_dupe_po_member' => 'PO not duplicate Member !!',
        'msg_dupe_member' => 'Member not duplicate !!',
        'msg_team_name_already' => 'Team name has already use !!',
        'msg_add_success'=>'Team successfully added!!!',
        'msg_add_fail'=>'Add team fail!!!',
        'msg_error_add_team'=> 'Has error in process',
        'msg_edit_success'=>'Team successfully edited!!!',
        'msg_edit_fail'=>'Edit team fail!!!',
        'msg_add_member1'=>'Member matches with PO, Please select another member !!!',
        'msg_add_member2'=>'Error!!! Member already exist !!!'
    ],
    'title_header'=>[
        'add_team'=>'Add team',
        'edit_team'=>'Edit team',
        'detail'=>'Team detail',
    ],
    'path'=>[
        'team'=>'Teams',
    ],
    'team'=>[
        'team_name'=>'Team name',
        'po_name'=>'PO',
        'member'=>'Member',
        'employee_id'=>'Employee ID',
        'role'=>'Role',
        'doing_project'=>'Doing projects',
        'email'=>'Email',
        'phone'=>'phone',
    ],
    'add_team'=>[
        'id'=>'ID',
        'team_name'=>'Team',
        'role'=>'Role',
        'name'=>'Name',
        'remove'=>'Remove'
    ],
    'view_team_list_project'=>[
        'title'=>'Project Name Of:',
        'id'=>'ID',
        'name'=>'Name',
        'status'=>'Status',
        'close'=>'Close'
    ],
    'submit'=>[
        'reset'=>'Do you want to reset?',
    ],
    'team_service'=>[
        'msg_fail1'=>'Edit failed!!! PO is not exit!!!',
        'msg_fail2'=>'Edit failed!!! Employee is not exit!!!'
    ],
];
