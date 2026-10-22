<?php

// Chỉ ghi đè các khóa cần thay đổi; các khóa còn lại dùng mặc định của Laravel.
return [
    'name' => env('APP_NAME', 'Student Management'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'https://localhost'),
    'timezone' => 'Asia/Ho_Chi_Minh',
    'locale' => 'vi',
    'fallback_locale' => 'en',
    'faker_locale' => 'vi_VN',
];
