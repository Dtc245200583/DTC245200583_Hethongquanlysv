@extends('layouts.app')

@section('title', 'Quản lý điểm')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Quản lý điểm</h1>
    <a class="btn btn-primary" href="{{ route('grades.create') }}">+ Nhập điểm</a>
</div>

<form class="row g-2 mb-3" method="GET" action="{{ route('grades.index') }}">
    <div class="col-md-6">
        <input type="search" name="q" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo mã SV hoặc họ tên">
    </div>
    <div class="col-md-3">
        <select name="subject" class="form-select">
            <option value="">— Tất cả môn —</option>
            @foreach ($subjects as $item)
                <option value="{{ $item }}" @selected($subject === $item)>{{ $item }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-outline-primary flex-fill" type="submit">Tìm kiếm</button>
        <a class="btn btn-outline-secondary" href="{{ route('grades.index') }}">Xóa lọc</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Mã SV</th><th>Họ và tên</th><th>Lớp</th><th>Môn học</th><th>Điểm</th><th>Xếp loại</th><th class="text-end">Thao tác</th></tr></thead>
            <tbody>
            @forelse ($grades as $grade)
                <tr>
                    <td><a href="{{ route('students.show', $grade->student) }}">{{ $grade->student->student_code }}</a></td>
                    <td>{{ $grade->student->full_name }}</td>
                    <td>{{ $grade->student->schoolClass?->class_name }}</td>
                    <td>{{ $grade->subject_name }}</td>
                    <td class="fw-semibold">{{ $grade->score }}</td>
                    <td><span class="badge text-bg-{{ $grade->rank[1] }}">{{ $grade->rank[0] }}</span></td>
                    <td class="text-end actions">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('grades.edit', $grade) }}">Sửa</a>
                        <form class="d-inline" method="POST" action="{{ route('grades.destroy', $grade) }}"
                              data-confirm="Xóa điểm môn {{ $grade->subject_name }} của {{ $grade->student->full_name }}?">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Không có bản ghi điểm.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $grades->links() }}</div>
@endsection
