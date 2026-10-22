<?php

// Thông báo kiểm tra dữ liệu tiếng Việt cho các rule được dùng trong ứng dụng.
// Rule nào không có ở đây sẽ dùng thông báo tiếng Anh mặc định của Laravel.
return [
    'after' => ':attribute phải là ngày sau :date.',
    'before' => ':attribute phải là ngày trước :date.',
    'between' => [
        'numeric' => ':attribute phải nằm trong khoảng từ :min đến :max.',
    ],
    'date' => ':attribute không phải là ngày hợp lệ.',
    'decimal' => ':attribute chỉ được có tối đa :decimal chữ số thập phân.',
    'email' => ':attribute phải là địa chỉ email hợp lệ.',
    'exists' => ':attribute đã chọn không hợp lệ.',
    'integer' => ':attribute phải là số nguyên.',
    'max' => [
        'numeric' => ':attribute không được lớn hơn :max.',
        'string' => ':attribute không được vượt quá :max ký tự.',
    ],
    'numeric' => ':attribute phải là số.',
    'regex' => 'Định dạng :attribute không hợp lệ.',
    'required' => 'Vui lòng nhập :attribute.',
    'string' => ':attribute phải là chuỗi ký tự.',
    'unique' => ':attribute đã tồn tại.',

    'custom' => [
        'subject_name' => [
            'unique' => 'Sinh viên này đã có điểm môn học này.',
        ],
    ],

    'attributes' => [
        'student_code' => 'mã sinh viên',
        'full_name' => 'họ và tên',
        'email' => 'email',
        'date_of_birth' => 'ngày sinh',
        'class_id' => 'lớp',
        'class_name' => 'tên lớp',
        'school_year' => 'niên khóa',
        'student_id' => 'sinh viên',
        'subject_name' => 'môn học',
        'score' => 'điểm',
    ],
];
