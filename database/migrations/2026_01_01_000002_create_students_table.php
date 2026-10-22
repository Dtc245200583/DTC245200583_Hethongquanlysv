<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_code', 20)->unique();
            $table->string('full_name', 100)->index();
            $table->string('email', 150)->unique();
            $table->date('date_of_birth')->nullable();
            // Không cho xóa lớp khi lớp còn sinh viên.
            $table->foreignId('class_id')->constrained('classes')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
