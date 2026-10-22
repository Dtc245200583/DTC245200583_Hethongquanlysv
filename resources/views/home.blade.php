@extends('layouts.app')

@section('title', 'Tổng quan')

@section('content')
<h1 class="h3 mb-4">Tổng quan hệ thống</h1>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Sinh viên</div>
            <div class="display-6">{{ $studentCount }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Lớp học</div>
            <div class="display-6">{{ $classCount }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Bản ghi điểm</div>
            <div class="display-6">{{ $gradeCount }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Điểm trung bình</div>
            <div class="display-6">{{ $averageScore !== null ? number_format($averageScore, 2) : '—' }}</div>
        </div></div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Sinh viên mới thêm</span>
        <a class="btn btn-sm btn-primary" href="{{ route('students.create') }}">+ Thêm sinh viên</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Mã SV</th><th>Họ và tên</th><th>Lớp</th><th>Email</th></tr></thead>
            <tbody>
            @forelse ($recentStudents as $student)
                <tr>
                    <td><a href="{{ route('students.show', $student) }}">{{ $student->student_code }}</a></td>
                    <td>{{ $student->full_name }}</td>
                    <td>{{ $student->schoolClass?->class_name }}</td>
                    <td>{{ $student->email }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu. Hãy chạy seeder hoặc thêm sinh viên.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
