<?php

return [
    'title'=>'Chi Tiết Nhân Viên Ngoài Công Ty',
    'button'=>[
        'add_vendor' => 'THÊM'
    ],
    'title_header'=>[
        'title_vendor'=>'Nhân Viên Ngoài',
        'add_vendor'=>'Thêm Nhân Viên Ngoài',
    ],

    'import'=>[
        'import'=>'THÊM NHIỀU',
        'import-vendor'=>'THÊM NHÂN VIÊN NGOÀI ',
        'select-file' => 'chọn tập tin csv',
        'error'=>[
            'upload_again'=>'Xin lỗi. Tập tin CSV của bạn bị lỗi. Vui lòng kiểm tra và tải lên lại',
            'note'=>'GHI CHÚ',
            'follow-role'=>'Bạn cần phải theo các chức vụ sau:',
            'follow-type'=>'Bạn cần phải theo các loại sau:',

        ]
    ],
    'export'=>"XUẤT",
    'template'=>"BẢN MẪU",
    'label'=>[
        'avatar'=>'Ảnh đại diện'
    ],

    'drop_box'=>[
        'placeholder-default' => 'Chọn',
        'gender-'.config('settings.Gender.male').'' => 'Nam',
        'gender-'.config('settings.Gender.female').'' => 'Nữ',
        'gender-N/A'.config('settings.Gender.n_a').'' => 'Khác'
    ],
    'marital_status'=>[
        'title'=>'Tình Trạng Hôn Nhân',
        config('settings.Married.single')=>'Độc Thân',
        config('settings.Married.married')=>'Đã Kết Hôn',
        config('settings.Married.separated')=>'Ly Thân',
        config('settings.Married.devorce')=>'Đã Ly Hôn'
    ],
    'basic'=>'Thông Tin Cơ Bản',
    'profile_info'=>[
        'id'=>'ID',
        'title'=>'Thông Tin Nhân Thân',
        'name'=>'Tên',
        'email'=>'Email',
        'status'=>'Tình Trạng',
        'password'=>'Mật Khẩu',
        'confirm_password'=> 'Xác Nhận Mật Khẩu',
        'company'=>'Công Ty',
        'cv'=>'CV',
        'married'=>'Tình Trạng Hôn Nhân',
        'start_work_date'=>'Ngày Bắt Đầu Làm Việc',
        'end_work_date'=>'Ngày Kết Thúc Làm Việc',
        'work_status'=>[
            'active'=>'Đang làm',
            'inactive'=>'Nghỉ việc',
        ],
        'gender'=>[
            'title'=>'Giới Tính',
            'male'=>'Nam',
            'female'=>'Nữ',
            'na'=>'N/A'
        ],
        'position'=>[
            'position'=>'Vị Trí'
        ],
        'birthday'=>'Ngày Sinh',
        'phone'=>'Số Điện Thoại',
        'address'=>'Địa Chỉ',
        'employee_type'=>[
            config('settings.EmployeeType.FullTime')=>'Làm đủ giờ',
            config('settings.EmployeeType.PartTime')=>'Làm bán thời gian',
            config('settings.EmployeeType.InterShip')=>'Thực tập',
            config('settings.EmployeeType.Contractual Employee')=>'Làm theo hợp đồng'
        ],
        'team'=>'Team',
        'role'=>'Chức vụ',
        'status_children'=>[
            config('settings.employee_status.Active')=>'Đang hợp đồng',
            config('settings.employee_status.Quited')=>'Hết làm',
            config('settings.employee_status.Expired')=>'Chưa gia hạn',
        ],
        'role_in_team'=>'Chức vụ',
        'policy_date'=>'Hạn Hợp Đồng',
        'policy_status'=>[
            'title'=>'TÌnh Trạng Hợp Đồng',
            'unexpired'=>'Chưa Hết Hạn',
            'expired'=>'Đã Hết Hạn'
        ],
    ],

    'msg_fails' => 'msg_fails',
    'msg_success' => 'msg_success',
    'msg_error' => 'msg_error',

    'msg_content' => [
        'msg_add_success'=>'Thêm nhân viên ngoài thành công',
        'msg_add_fail'=>'Thêm nhân viên ngoài không thành công',
        'msg_error_add_team'=> 'Có lỗi',
        'msg_download_template'=>'Bạn có muốn tải về mẫu danh sách nhân viên ngoài không?',
        'msg_download_vendor_list'=>'Bạn có muốn tải về danh sách nhân viên ngoài không?'
    ],
    'msg_controller'=>[
        'update'=>[
            'success'=>'Bạn đã chỉnh sửa thành công',
            'false'=>'Chỉnh sửa sai'
        ],
        'edit_pass' =>[
            'old_pass'=>'Mật khẩu cũ không đúng',
            'conf_pass'=>'Mật khẩu xác nhận chưa chích xác',
            'least_character'=>'Mật khẩu phải nhiều hơn 6 ký tự',
            'success_pass'=>'Mật khẩu đã được chỉnh sửa thành công'
        ],
        'destroy_success'=>'Xóa thành công.',
        'post_file' =>[
            'max_size'=>'Tập tin của bạn quá lớn. Dung lượng tối đa là 5MB.',
            'format_error'=>'Tập tin của bạn định không đúng chuẩn.',
            'not_select'=>'Tập tin không được xác định.'
        ],
    ]
];
