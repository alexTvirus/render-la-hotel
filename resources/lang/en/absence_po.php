<?php
/**
 * Created by PhpStorm.
 * User: Ngoc Quy
 * Date: 6/18/2018
 * Time: 2:27 PM
 */

return [
    'list_po' => [
        'path'=>'Absence List',
        'modal'=>[
            'reason'=>'Reason',
            'done'=>'Done',
            'close'=>'Close',
            'send'=>'Send',
            'cancel'=>'Cancel'
        ],
        'profile_info'=>[
            'start_date'=>'Start date',
            'end_date'=>'End date',
            'type' => 'Type',
            'reason'=>'Cause',
            'note'=>'Note',
            'action'=>'Action',
            'status'=>'Status',
            'note_po'=>'Reject Cause'
        ],
        'note'=>[
            'absence_new'=>'Absence',
            'absence_deny'=>'Deny'
        ],
        'status'=>[
            config('settings.status_common.absence.waiting')=>'Waiting',
            config('settings.status_common.absence.accepted')=>'Accepted',
            config('settings.status_common.absence.rejected')=>'Rejected',
            'accepted_done'=>'Done accepted',
            'accepted_deny'=>'Done deny',
            'no_accepted_done'=>'Not accepted',
            'no_accepted_deny'=>'Not deny',
            'just_watching'=>'Just Watching',
            'absence_accepted'=>'Absence Accepted',
            'absence_rejected'=>'Absence Rejected',
        ],
        'type'=>[
            config('settings.status_common.absence_type.non_salary_date')=>'Non salary date',
            config('settings.status_common.absence_type.salary_date')=>'Salary date',
            config('settings.status_common.absence_type.subtract_salary_date')=>'Subtract salary date',
            config('settings.status_common.absence_type.insurance_date')=>'Insurance date',
            config('settings.status_common.absence_type.absented_date')=>'Absented date',
        ],
        'msg'=>[
            'confirm_export'=>'Do you want to export this confirmation list?'
        ],
        'message'=>[
            'export_msg'=>'Do you want to download Absence List?'
        ]
    ]
];