@extends('layouts.app')

@section('title', 'Lớp '.$schoolClass->class_name)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Lớp {{ $schoolClass->class_name }} <small class="text-muted">({{ $schoolClass->school_year }})</small></h1>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('classes.edit', $schoolClass) }}">Sửa lớp</a>
        <a class="btn btn-outline-secondary" href="{{ route('classes.index') }}">Quay lại</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white fw-semibold">Danh sách sinh viên ({{ $students->total() }})</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Mã SV</th><th>Họ và tên</th><th>Email</th><th>ĐTB</th></tr></thead>
            <tbody>
            @forelse ($students as $student)
                <tr>
                    <td><a href="{{ route('students.show', $student) }}">{{ $student->student_code }}</a></td>
                    <td>{{ $student->full_name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->grades_avg_score !== null ? number_format($student->grades_avg_score, 2) : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Lớp chưa có sinh viên.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $students->links() }}</div>
@endsection
