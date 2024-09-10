@component('mail::message')
## CÔNG TY CỔ PHẦN CÔNG NGHỆ THIẾT BỊ TÂN PHÁT gửi bảng công từ ngày {{$timesheet_month_summary->start_date}} đến ngày {{$timesheet_month_summary->end_date}}:</h3>

Họ tên: {{$employee_info->fullname}}<br>
Mã nhân viên: {{$employee_info->code}}<br>
Mã chấm công: {{$employee_info->ssn}}<br>

## BẢNG CÔNG TỔNG HỢP
@component('mail::table')
| Tổng công tính lương       |   {{$timesheet_summary_detail->total_working_after_add}}       |
| ------------- |:-------------:|
| Công định mức     | {{$timesheet_month_summary->standard}}      |
| Công làm việc     | {{$timesheet_summary_detail->working_general}} |
| Nghỉ lễ hưởng lương     | {{$timesheet_summary_detail->holiday}} |
| Nghỉ chế độ     | {{$timesheet_summary_detail->work_day_che_do}} |
| Nghỉ phép     | {{$timesheet_summary_detail->work_day_phep}} |
| Nghỉ ốm     | {{$timesheet_summary_detail->work_day_om}} |
| Nghỉ không lý do     | {{$timesheet_summary_detail->work_day_khong_ly_do}} |
| Nghỉ không lương    | {{$timesheet_summary_detail->work_day_khong_luong}} |
| Số lần đi muộn về sớm     | {{$timesheet_summary_detail->num_of_in_out}} |
| Số phút đi muộn về sớm     | {{$timesheet_summary_detail->total_minutes_late}} |
| Thời gian làm thêm bù công chuẩn (giờ) | {{$timesheet_summary_detail->overtime_add_standard}} |
| Thời gian làm thêm hưởng lương (giờ) | {{$timesheet_summary_detail->total_overtime_minutes_after}} |
| Trừ thời gian đi muộn/về sớm (công) | {{$timesheet_summary_detail->punishment_rule}} |
@endcomponent

{!! $generalRegulation && $generalRegulation->note_email ? $generalRegulation->note_email : '' !!}

@endcomponent
