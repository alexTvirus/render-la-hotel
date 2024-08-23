<?php

return [
    'title'=>'Vendor Detail',
    'button'=>[
        'add_vendor' => 'ADD'
    ],
    'title_header'=>[
        'title_vendor'=>'Vendor',
        'add_vendor'=>'Add Vendor',
    ],
    'import'=>[
        'import'=>'IMPORT',
        'import-vendor'=>'IMPORT VENDOR',
        'select-file' => 'Select file csv',
        'error'=>[
            'upload_again'=>'Sorry. CSV file you are error. Please correct the errors below in the CSV file. Then upload the file again.',
            'note'=>'NOTE',
            'follow-role'=>'There are the following roles:',
            'follow-type'=>'There are the following employee type:',

        ],
        'message'=>[
            'upload_again'=>'Sorry. CSV file you are error. Please correct the errors below in the CSV file. Then upload the file again.',
            'note'=>'NOTE',
            'follow-role'=>'There are the following roles:',
            'follow-type'=>'There are the following employee type:',

        ]
    ],

    'export'=>"EXPORT",
    'template'=>"TEMPLATE",
    'label'=>[
        'avatar'=>'Avatar'
    ],
    'drop_box'=>[
        'placeholder-default' => 'Please Select',
        'gender-'.config('settings.Gender.male').'' => 'Male',
        'gender-'.config('settings.Gender.female').'' => 'Female',
        'gender-'.config('settings.Gender.n_a').'' => 'N/A'
    ],
    'marital_status'=>[
        'title'=>'Marital Status',
        config('settings.Married.single')=>'Single',
        config('settings.Married.married')=>'Married',
        config('settings.Married.separated')=>'Separated',
        config('settings.Married.devorce')=>'Divorced'
    ],
    'basic'=>'Basic',
    'profile_info'=>[
        'id'=>'Vendor ID',
        'title'=>'Profile Information',
        'name'=>'Name',
        'email'=>'Email',
        'status'=>'Status',
        'password'=>'Password',
        'confirm_password'=> 'Confirm password',
        'company'=>'Company',
        'cv'=>'CV',
        'married'=>'Married',
        'start_work_date'=>'Start work date',
        'end_work_date'=>'End work date',
        'work_status'=>[
            'active'=>'Active',
            'inactive'=>'Inactive',
        ],
        'gender'=>[
            'title'=>'Gender',
            'male'=>'Male',
            'female'=>'Female',
            'na'=>'N/A'
        ],
        'position'=>[
            'position'=>'Position'
        ],
        'birthday'=>'Birthday',
        'phone'=>'Phone',
        'address'=>'Address',
        'employee_type'=>[
            config('settings.EmployeeType.FullTime')=>'FullTime',
            config('settings.EmployeeType.PartTime')=>'PartTime',
            config('settings.EmployeeType.InterShip')=>'InterShip',
            config('settings.EmployeeType.Contractual Employee')=>'Contractual Employee'
        ],
        'team'=>'Team',
        'role'=>'Role',
        'status_children'=>[
            config('settings.employee_status.Active')=>'Active',
            config('settings.employee_status.Quited')=>'Quited',
            config('settings.employee_status.Expired')=>'Expired',
        ],
        'role_in_team'=>'Role',
        'policy_date'=>'Policy Date',
        'policy_status'=>[
            'title'=>'Policy Status',
            'unexpired'=>'Unexpired',
            'expired'=>'Expired'
        ],
    ],

    'msg_fails' => 'msg_fails',
    'msg_success' => 'msg_success',
    'msg_error' => 'msg_error',

    'msg_content' => [
        'msg_add_success'=>'Vendor successfully added',
        'msg_add_fail'=>'Add Vendor fail',
        'msg_error_add_team'=> 'Has error in process',
        'msg_download_template'=>'Do you want to download the Vendor Template?',
        'msg_download_vendor_list'=>'Do you want to download the Vendor List?'
    ],
    'msg_controller'=>[
        'update'=>[
            'success'=>'Account successfully edited',
            'false'=>'Edit failed'
        ],
        'edit_pass' =>[
            'old_pass'=>'Old password is incorrect',
            'conf_pass'=>'The confirm password and password must match',
            'least_character'=>'The Password must be at least 6 characters',
            'success_pass'=>'Password successfully edited!!!'
        ],
        'destroy_success'=>'Remove success',
        'post_file' =>[
            'max_size'=>'The selected file is too large. Maximum size is 5MB.',
            'format_error'=>'The file is not formatted correctly',
            'not_select'=>'File not selected'
        ],
        'import_success'=>'Import Vendors successfully.'
    ]
];
