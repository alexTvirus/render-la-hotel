<?php
/**
 * Created by PhpStorm.
 * User: Ngoc Quy
 * Date: 6/18/2018
 * Time: 2:28 PM
 */

return [
    'list_po' => [
        'path'=>'Danh sách xin vắng làm',
        'modal'=>[
            'reason'=>'Lý do từ chối',
            'done'=>'Đồng Ý',
            'close'=>'Đóng',
            'send'=>'Gửi',
            'cancel'=>'Từ Chối'
        ],
        'profile_info'=>[
            'start_date'=>'Ngày bắt đầu',
            'end_date'=>'Ngày kết thúc',
            'type' => 'Loại',
            'reason'=>'Lý do',
            'action'=>'Hành động',
            'note'=>'Ghi chú',
            'status'=>'Tình trạng',
            'note_po'=>'Ghi chú của PO'
        ],
        'note'=>[
            'absence_new'=>'Xin nghỉ',
            'absence_deny'=>'Xin hủy'
        ],
        'status'=>[
            config('settings.status_common.absence.waiting')=>'Đang xử lý',
            config('settings.status_common.absence.accepted')=>'Được Nghỉ',
            config('settings.status_common.absence.rejected')=>'Không Được Nghỉ',
            'accepted_done'=>'Đồng ý nghỉ',
//            'accepted_deny'=>'Đồng ý hủy',
            'no_accepted_done'=>'Không được nghỉ',
            'no_accepted_deny'=>'Không được hủy',
            'just_watching'=>'Chỉ được xem',
            'absence_accepted'=>'Được Nghỉ',
            'absence_rejected'=>'Không Được Nghỉ',
//            'no_accepted_deny'=>'Không được hủy',
            'just_watching'=>'Chỉ được xem'
        ],
        'type'=>[
            config('settings.status_common.absence_type.non_salary_date')=>'Nghỉ không lương',
            config('settings.status_common.absence_type.salary_date')=>'Nghỉ phép',
            config('settings.status_common.absence_type.subtract_salary_date')=>'Nghỉ trừ lương',
            config('settings.status_common.absence_type.insurance_date')=>'Nghỉ theo bảo hiểm',
            config('settings.status_common.absence_type.absented_date')=>'Nghỉ phép',
        ],
        'msg'=>[
            'confirm_export'=>'Bạn có muốn tải về danh sách xác nhận vắng nghỉ không?'
        ],
        'message'=>[
            'export_msg'=>'Bạn có muốn tải xuống danh sách xin vắng làm?'
        ]
    ]
];