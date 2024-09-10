@component('mail::message')
## PHÒNG NSHC GỬI TỚI CBNV BẢNG TẠM ỨNG LƯƠNG THÁNG {{$month}}:</h3>

Họ tên: {{$employee_info->fullname}}<br>
Mã nhân viên: {{$employee_info->code}}<br>
Mã chấm công: {{$employee_info->ssn}}<br>

@component('mail::table')
|    Số tiền tạm ứng   |   {{number_format($salary_advance_employee->value, 0, ".", ",")}}      |
| ------------- |:-------------:|
| Lương P1    |     {{number_format($salary_advance_employee->p1, 0, ".", ",")}}   |
| Lương P2    |     {{number_format($salary_advance_employee->p2, 0, ".", ",")}}   |
| Lương P3    |     {{number_format($salary_advance_employee->p3, 0, ".", ",")}}   |
| Phụ cấp thâm niên   |     {{number_format($salary_advance_employee->seniority_salary, 0, ".", ",")}}   |
| Phụ cấp điện thoại   |     {{number_format($salary_advance_employee->telephone_expenses, 0, ".", ",")}}   |
| Phụ cấp xăng xe   |     {{number_format($salary_advance_employee->moving_expenses, 0, ".", ",")}}   |
| Phụ cấp khác   |     {{number_format($salary_advance_employee->other_allowances, 0, ".", ",")}}   |
| Tổng lương  |     {{number_format($salary_advance_employee->total_salary, 0, ".", ",")}}   |
| % tạm ứng  |     {{number_format($salary_advance_employee->percent, 0, ".", ",")}}   |
@endcomponent
@endcomponent
