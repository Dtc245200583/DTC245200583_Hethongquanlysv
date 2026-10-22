@extends('layouts.app')

@section('title', 'Danh sách sinh viên')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Danh sách sinh viên</h1>
    <a class="btn btn-primary" href="{{ route('students.create') }}">+ Thêm sinh viên</a>
</div>

<form class="row g-2 mb-3" method="GET" action="{{ route('students.index') }}">
    <div class="col-md-6">
        <input type="search" name="q" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo mã SV, họ tên hoặc email">
    </div>
    <div class="col-md-3">
        <select name="class_id" class="form-select">
            <option value="">— Tất cả lớp —</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected((string) $classId === (string) $class->id)>{{ $class->class_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-outline-primary flex-fill" type="submit">Tìm kiếm</button>
        <a class="btn btn-outline-secondary" href="{{ route('students.index') }}">Xóa lọc</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
            <tr><th>#</th><th>Mã SV</th><th>Họ và tên</th><th>Email</th><th>Ngày sinh</th><th>Lớp</th><th>ĐTB</th><th class="text-end">Thao tác</th></tr>
            </thead>
            <tbody>
            @forelse ($students as $student)
                @php
                    [$rankLabel, $rankColor] = \App\Models\Grade::rankFor($student->grades_avg_score !== null ? (float) $student->grades_avg_score : null);
                @endphp
                <tr>
                    <td>{{ $students->firstItem() + $loop->index }}</td>
                    <td class="fw-semibold">{{ $student->student_code }}</td>
                    <td>{{ $student->full_name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->date_of_birth?->format('d/m/Y') }}</td>
                    <td>{{ $student->schoolClass?->class_name }}</td>
                    <td>
                        {{ $student->grades_avg_score !== null ? number_format($student->grades_avg_score, 2) : '—' }}
                        <span class="badge text-bg-{{ $rankColor }}">{{ $rankLabel }}</span>
                    </td>
                    <td class="text-end actions">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('students.show', $student) }}">Xem</a>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('students.edit', $student) }}">Sửa</a>
                        <form class="d-inline" method="POST" action="{{ route('students.destroy', $student) }}"
                              data-confirm="Xóa sinh viên {{ $student->full_name }}? Điểm của sinh viên cũng sẽ bị xóa.">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Không tìm thấy sinh viên nào.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $students->links() }}</div>
@endsection
