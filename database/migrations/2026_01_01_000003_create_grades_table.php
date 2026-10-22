<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            // Xóa sinh viên thì xóa luôn điểm của sinh viên đó.
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('subject_name', 100);
            $table->decimal('score', 4, 2);
            $table->timestamps();

            $table->unique(['student_id', 'subject_name']);
        });

        // Ràng buộc ở mức CSDL (MySQL 8.0.16+ thực thi CHECK).
        DB::statement('ALTER TABLE grades ADD CONSTRAINT chk_grades_score CHECK (score >= 0 AND score <= 10)');
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
