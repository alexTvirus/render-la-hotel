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
                'invalid'=>' Xin lỗi. Tập tin CSV của bạn bị lỗi. Vui lòng kiểm tra lại các cột.',
                'row'=>'Hàng',
                'has'=>'có',
                'missing'=>'đang thiếu',
                'column'=>'Cột',
            ],
            'check_Email'=>[
                'repeated'=>'đã tồn tại trước đó'
            ],
            'checkFileEmployee'=>[
                'email_required'=>'Vui lòng nhập email.',
                'email_exist'=>'Email đã tồn tại.',
                'email_valid'=>'Email chưa đúng định dạng',
                'name_required'=>'Vui lòng nhập tên.',
                'birthday_required'=>'Trường ngày sinh bắt buộc phải nhập.',
                'birthday_format'=>'Ngày sinh không hợp lệ. Ví dụ: 22-02-2000.',
                'birthday_before'=>'Ngày sinh phải trước ngày hiện tại.',
                'gender_required'=>'Vui lòng chọn giới tính.',
                'gender_values'=>'Giới tính gồm nam, nữ hoặc khác.',
                'mobile_required'=>'Vui lòng nhập số điện thoại.',
                'mobile_number'=>'Số điện thoại phải là số đếm.',
                'address_required'=>'Vui lòng nhập địa chỉ.',
                'marital_status_required'=>'Vui lòng nhập trạng thái hôn nhân.',
                'marital_status_values'=>'Trạng thái hôn nhân bao gồm độc thân, đã cưới, ly thân or ly hôn.',
                'startwork_required'=>'Vui lòng nhập ngày bắt đầu.',
                'startwork_end_format'=>'Ngày bắt đầu không hợp lệ. Ví dụ: 22-02-2000.',
                'endwork_end_format'=>'Ngày kết thúc không hợp lệ. Ví dụ: 22-02-2000.',
                'endwork_required'=>'Vui lòng nhập ngày kết thúc.',
                'endwork_smaller'=>'Ngày bắt đầu phải bé hơn ngày kết thúc.',
                'employee_type_required'=>'Vui lòng nhập loại nhân viên.',
                'employee_type_exist'=>'Loại nhân viên không hợp lệ.',
                'team_required'=>'Vui lòng nhập tên team.',
                'team_exist'=>'Team đã tồn tại.',
                'role_required'=>'Vui lòng nhập chức vụ.',
                'role_exist'=>'Chức vụ không tồn tại.',
                'company_required'=>'Vui lòng nhập tên công ty.',
            ]
        ],
];