<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private const CLASSES = [
        ['class_name' => 'CNTT01', 'school_year' => '2023-2027'],
        ['class_name' => 'CNTT02', 'school_year' => '2023-2027'],
        ['class_name' => 'KTPM01', 'school_year' => '2024-2028'],
        ['class_name' => 'ATTT01', 'school_year' => '2024-2028'],
    ];

    private const LAST_NAMES = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Vũ', 'Đặng', 'Bùi'];
    private const MIDDLE_NAMES = ['Văn', 'Thị', 'Minh', 'Ngọc', 'Đức', 'Thu', 'Quang', 'Hải'];
    private const FIRST_NAMES = ['An', 'Bình', 'Châu', 'Dũng', 'Giang', 'Hà', 'Khánh', 'Linh', 'Nam', 'Phương', 'Quân', 'Trang'];
    private const SUBJECTS = ['Lập trình Web', 'Cơ sở dữ liệu', 'Mạng máy tính', 'Điện toán đám mây'];

    public function run(): void
    {
        // Cố định seed để dữ liệu mẫu giống nhau mỗi lần chạy.
        mt_srand(2026);

        $classes = collect(self::CLASSES)->map(
            fn (array $row) => SchoolClass::updateOrCreate(['class_name' => $row['class_name']], $row)
        );

        for ($i = 1; $i <= 32; $i++) {
            $code = sprintf('SV%d%03d', 2023, $i);
            $name = self::LAST_NAMES[mt_rand(0, count(self::LAST_NAMES) - 1)].' '
                .self::MIDDLE_NAMES[mt_rand(0, count(self::MIDDLE_NAMES) - 1)].' '
                .self::FIRST_NAMES[mt_rand(0, count(self::FIRST_NAMES) - 1)];

            $student = Student::updateOrCreate(['student_code' => $code], [
                'full_name' => $name,
                'email' => strtolower($code).'@student.edu.vn',
                'date_of_birth' => sprintf('%d-%02d-%02d', mt_rand(2003, 2006), mt_rand(1, 12), mt_rand(1, 28)),
                'class_id' => $classes[($i - 1) % $classes->count()]->id,
            ]);

            foreach (self::SUBJECTS as $subject) {
                Grade::updateOrCreate(
                    ['student_id' => $student->id, 'subject_name' => $subject],
                    ['score' => mt_rand(35, 100) / 10]
                );
            }
        }
    }
}
