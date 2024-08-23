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
    'title'=>'Danh sách Comics',
    'id'=>'ID Chapter',
    'chapter_name'=>'Tên Chapter',
    'link_small_icon'=>'Hình icon chapter',
    'chapter_number'=> 'Chapter thứ mấy',
    'publish_at' => 'Ngày phát hành chapter',
    'status' => 'Trạng thái (free/waiting)',
    'content_images_link_img'=>'Hình nội dung chapter',
    'next_chapter_id'=> 'ID Chapter tiếp theo',
    'prv_chapter_id' =>'ID Chapter trước',

    'po'=>'Tên PO',
    'members'=>'Tên Thành Viên',
    'number_of_member'=>'Số Lượng Thành Viên',
    'msg_fails' => 'msg_fail',
    'msg_success' => 'msg_success',
    'msg_error' => 'msg_error',
    'confirm_add_team' => 'Bạn có muốn thêm một Team mới không?',
    'msg_content' => [
        'msg_add_success'=>'Thêm Hashtag thành công!!',
        'msg_edit_success'=>'Chỉnh sửa Hashtag thành công!!',
        'msg_delete_fail'=>'Xóa Hashtag thất bại!!',

        'msg_dupe_po_member' => 'PO này đã tồn tại trong Team !!',

        'msg_dupe_member' => 'Nhân viên này đã tồn tại trong Team !!',
        'msg_team_name_already' => 'Tên Team đã tồn tại !!',
        'msg_add_fail'=>'Thêm Team không thành công!!',
        'msg_error_add_team'=> 'Có lỗi',
        'msg_edit_fail'=>'Chỉnh sửa Team thất bại!!',
        'msg_add_member1'=>'Thành viên trùng với PO, Vui lòng chọn thành viên khác !!!',
        'msg_add_member2'=>'Lỗi!!! Thành viên đã tồn tại!!!'
    ],
    'title_header'=>[
        'add_comic'=>'Thêm comic',
        'edit_comic'=>'Sửa comic',
        'detail'=>'Chi tiết comic',
    ],
    'path'=>[
        'comic'=>'Comics',
    ],
    'team'=>[
        'team_name'=>'Tên team',
        'po_name'=>'PO',
        'member'=>'Thành viên',
        'employee_id'=>'ID thành viên',
        'role'=>'Vị trí',
        'doing_project'=>'Dự án tham gia',
        'email'=>'Email',
        'phone'=>'Số điện thoại',
    ],
    'add_team'=>[
        'id'=>'ID',
        'team_name'=>'Team',
        'role'=>'Vị trí',
        'name'=>'Họ tên',
        'remove'=>'Xóa'
    ],
    'view_team_list_project'=>[
        'title'=>'Danh sách dự án của:',
        'id'=>'ID',
        'name'=>'Họ tên',
        'status'=>'Trạng thái',
        'close'=>'Đóng'
    ],
    'submit'=>[
        'reset'=>'Bạn có muốn đặt lại không?',
    ],
    'team_service'=>[
        'msg_fail1'=>'Sữa không thành công!!! PO không tồn tại!!!',
        'msg_fail2'=>'Sữa không thành công!!! Thành viên không tồn tại!!!'
    ],
];
