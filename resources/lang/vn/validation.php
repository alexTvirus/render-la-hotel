<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages.
    |
    */
    'accepted'             => 'Trường :attribute phải được chấp nhận.',
    'active_url'           => 'Trường :attribute không phải là một URL hợp lệ.',
    'after'                => 'Trường :attribute phải là một ngày sau ngày :date.',
    'after_or_equal'       => 'Trường :attribute phải là thời gian bắt đầu sau :date.',
    'alpha'                => 'Trường :attribute chỉ có thể chứa các chữ cái.',
    'alpha_dash'           => 'Trường :attribute chỉ có thể chứa chữ cái, số và dấu gạch ngang.',
    'alpha_num'            => 'Trường :attribute chỉ có thể chứa chữ cái và số.',
    'array'                => 'Trường :attribute phải là dạng mảng.',
    'before'               => 'Trường :attribute phải là một ngày trước ngày :date.',
    'before_or_equal'      => 'Trường :attribute phải là thời gian bắt đầu trước :date.',
    'between'              => [
        'numeric' => 'Trường :attribute phải nằm trong khoảng :min - :max.',
        'file'    => 'Dung lượng tập tin trong trường :attribute phải từ :min - :max kB.',
        'string'  => 'Trường :attribute phải từ :min - :max ký tự.',
        'array'   => 'Trường :attribute phải có từ :min - :max phần tử.',
    ],
    'boolean'              => 'Trường :attribute phải là true hoặc false.',
    'confirmed'            => 'Giá trị xác nhận trong trường :attribute không khớp.',
    'date'                 => 'Trường :attribute không phải là định dạng của ngày-tháng.',
    'date_format'          => 'Trường :attribute không giống với định dạng :format.',
    'different'            => 'Trường :attribute và :other phải khác nhau.',
    'digits'               => 'Độ dài của trường :attribute phải gồm :digits chữ số.',
    'digits_between'       => 'Độ dài của trường :attribute phải nằm trong khoảng :min and :max chữ số.',
    'dimensions'           => 'Trường :attribute có kích thước không hợp lệ.',
    'distinct'             => 'Trường :attribute có giá trị trùng lặp.',
    'email'                => 'Trường :attribute phải là một địa chỉ email hợp lệ.',
    'exists'               => 'Giá trị đã chọn trong trường :attribute không hợp lệ.',
    'file'                 => 'Trường :attribute phải là một tệp tin.',
    'filled'               => 'Trường :attribute không được bỏ trống.',
    'gt'                   => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file'    => 'The :attribute must be greater than :value kilobytes.',
        'string'  => 'The :attribute must be greater than :value characters.',
        'array'   => 'The :attribute must have more than :value items.',
    ],
    'gte'                  => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file'    => 'The :attribute must be greater than or equal :value kilobytes.',
        'string'  => 'The :attribute must be greater than or equal :value characters.',
        'array'   => 'The :attribute must have :value items or more.',
    ],
    'image'                => 'Trường :attribute phải là định dạng hình ảnh.',
    'in'                   => 'Giá trị đã chọn trong trường :attribute không hợp lệ.',
    'in_array'             => 'Trường :attribute phải thuộc tập cho phép: :other.',
    'integer'              => 'Trường :attribute phải là một số nguyên.',
    'ip'                   => 'Trường :attribute phải là một địa chỉ IP.',
    'ipv4'                 => 'Trường :attribute phải là một địa chỉ IPv4.',
    'ipv6'                 => 'Trường :attribute phải là một địa chỉ IPv6.',
    'json'                 => 'Trường :attribute phải là một chuỗi JSON.',
    'lt'                   => [
        'numeric' => 'The :attribute must be less than :value.',
        'file'    => 'The :attribute must be less than :value kilobytes.',
        'string'  => 'The :attribute must be less than :value characters.',
        'array'   => 'The :attribute must have less than :value items.',
    ],
    'lte'                  => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file'    => 'The :attribute must be less than or equal :value kilobytes.',
        'string'  => 'The :attribute must be less than or equal :value characters.',
        'array'   => 'The :attribute must not have more than :value items.',
    ],
    'max'                  => [
        'numeric' => 'Trường :attribute không được lớn hơn :max.',
        'file'    => 'Dung lượng tập tin trong trường :attribute không được lớn hơn :max kB.',
        'string'  => 'Trường :attribute không được lớn hơn :max ký tự.',
        'array'   => 'Trường :attribute không được lớn hơn :max phần tử.',
    ],
    'mimes'                => 'Trường :attribute phải là một tập tin có định dạng: :values.',
    'mimetypes'            => 'Trường :attribute phải là một tập tin có định dạng: :values.',
    'min'                  => [
        'numeric' => 'Trường :attribute phải tối thiểu là :min.',
        'file'    => 'Dung lượng tập tin trong trường :attribute phải tối thiểu :min kB.',
        'string'  => 'Trường :attribute phải có tối thiểu :min ký tự.',
        'array'   => 'Trường :attribute phải có tối thiểu :min phần tử.',
    ],
    'not_in'               => 'Giá trị đã chọn trong trường :attribute không hợp lệ.',
    'not_regex'            => 'Trường :attribute có định dạng không hợp lệ.',
    'numeric'              => 'Trường :attribute phải là một số.',
    'present'              => 'Trường :attribute phải được cung cấp.',
    'regex'                => 'Trường :attribute có định dạng không hợp lệ.',
    'required'             => 'Trường :attribute không được bỏ trống.',
    'required_if'          => 'Trường :attribute không được bỏ trống khi trường :other là :value.',
    'required_unless'      => 'Trường :attribute không được bỏ trống trừ khi :other là :values.',
    'required_with'        => 'Trường :attribute không được bỏ trống khi một trong :values có giá trị.',
    'required_with_all'    => 'Trường :attribute không được bỏ trống khi tất cả :values có giá trị.',
    'required_without'     => 'Trường :attribute không được bỏ trống khi một trong :values không có giá trị.',
    'required_without_all' => 'Trường :attribute không được bỏ trống khi tất cả :values không có giá trị.',
    'same'                 => 'Trường :attribute và :other phải giống nhau.',
    'size'                 => [
        'numeric' => 'Trường :attribute phải bằng :size.',
        'file'    => 'Dung lượng tập tin trong trường :attribute phải bằng :size kB.',
        'string'  => 'Trường :attribute phải chứa :size ký tự.',
        'array'   => 'Trường :attribute phải chứa :size phần tử.',
    ],
    'string'               => 'Trường :attribute phải là một chuỗi ký tự.',
    'timezone'             => 'Trường :attribute phải là một múi giờ hợp lệ.',
    'unique'               => 'Trường :attribute đã có trong cơ sở dữ liệu.',
    'uploaded'             => 'Trường :attribute tải lên thất bại.',
    'url'                  => 'Trường :attribute không giống với định dạng một URL.',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
    'custom' => [
        'man_power'=>[
            'total_man_power' => 'Tổng công số của nhân viên :employee ( id : :employee_id ) = :totalManPower lớn hơn 1',
            'available_processes' => 'Bạn có thể xem thông tin đề xuất cho nhân viên này',
        ],
        'employee'=>[
            'invalid_id'=>'Nhân viên được chọn có mã nhân viên không đúng .',
            'error_duplicate_member' => 'Nhân viên :employee_name_selected ( id: :value ) không thể được thêm vào bởi vì ' .
                ' từ: :start_date_process đến: :end_date_process nhân viên này đã được thêm vào project',

        ],
        'role'=>[
            "at_least_one_po"=>'Dự án phải có ít nhất 1 PO ',
            'error_P0_process' => 'Nhân viên :employee_name_selected không thể làm PO bởi vì ' .
                ' từ: :start_date_process đến: :end_date_process đã có người đảm nhận vị trí PO là :employee_name',
        ],
        'end_date_project'=>[
            'real_start_null_end_not_null'=>'Ngày thực tế Bắt đầu  chưa được chọn ,Bạn không thể chọn ngày thực tế Kết thúc  .',
            'start_date_end_date_process_must_between_real_start_end'
            =>"Ngày bắt đầu và Ngày kết thúc phải nằm giữa Ngày thực tế bắt đầu và Ngày thực tế kết thúc Dự án .",
            'start_date_end_date_process_must_between_real_start_estimate_end'
            =>"Ngày bắt đầu và Ngày kết thúc phải nằm giữa Ngày Thực tế bắt đầu và Ngày dự kiến kết thúc Dự án .",
            'start_date_end_date_process_must_between_estimate_start_estimate_end'
            =>"Ngày bắt đầu và Ngày kết thúc phải nằm giữa Ngày dự kiến bắt đầu và Ngày dự kiến kết thúc Dự án ."
        ],
        'status'=>[
            'must_be_planning'=>'Ngày thực tế Bắt đầu chưa được chọn , Tình trạng của dự án bắt buộc là planning',
            'cant_be_planning'=>"Ngày thực tế Bắt đầu đã được chọn ,Tình trạng của dự án không thể là planning",
            'cant_be_complete'=>"Ngày thực tế Kết thúc chưa được chọn ,Tình trạng của dự án không thể là complete",
            'must_be_complete'=>"Ngày thực tế Kết thúc đã được chọn ,Tình trạng của dự án bắt buộc là complete",
        ],
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */
    'attributes' => [
    ],
];