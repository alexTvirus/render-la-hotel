<?php
/**
 * Created by PhpStorm.
 * User: Ngoc Quy
 * Date: 6/15/2018
 * Time: 3:17 PM
 */

return
    [
        'import_file'=>[
            'check_col'=>[
                'invalid'=>'Invalid csv file. Please check the correct number of columns with the sample file.',
                'row'=>'Row',
                'has'=>'has',
                'missing'=>'is missing',
                'column'=>'columns',
            ],
            'check_Email'=>[
                'repeated'=>'has been repeated'
            ],
            'checkFileEmployee'=>[
                'email_required'=>'The Email field is required',
                'email_exist'=>'Email already exists!!!',
                'email_valid'=>'The Email must be a valid email address.',
                'name_required'=>'The Name field is required.',
                'birthday_required'=>'The Birthday field is required.',
                'birthday_format'=>'Birthday is incorrect format. Example: 22-02-2000.',
                'birthday_before'=>'The Birthday must be a date before Today.',
                'gender_required'=>'The Gender field is required.',
                'gender_values'=>'Gender only receives values Female, Male or N/A.',
                'mobile_required'=>'The Mobile field is required.',
                'mobile_number'=>'Mobile only number.',
                'address_required'=>'The Address field is required.',
                'marital_status_required'=>'The marital_status field is required.',
                'marital_status_values'=>'Marital status only receives values Single, Married, Separated or Devorce.',
                'startwork_required'=>'The Startwork_date field is required.',
                'startwork_end_format'=>'Startwork_date is incorrect format. Example: 22-02-2000.',
                'endwork_end_format'=>'Endwork_date is incorrect format. Example: 22-02-2000.',
                'endwork_required'=>'The Endwork_date field is required.',
                'endwork_smaller'=>'Startwork_date must be smaller than Endwork_date.',
                'employee_type_required'=>'Employee Type field is required.',
                'employee_type_exist'=>'Employee_type does not exist.',
                'team_required'=>'Team field is required.',
                'team_exist'=>'Team does not exist.',
                'role_required'=>'Role field is required.',
                'role_exist'=>'Role does not exist.',
                'company_required'=>'Company field is required.',
            ]
        ],
];