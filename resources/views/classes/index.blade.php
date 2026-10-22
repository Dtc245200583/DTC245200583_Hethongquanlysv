@extends('layouts.app')

@section('title', 'Quản lý lớp học')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Quản lý lớp học</h1>
    <a class="btn btn-primary" href="{{ route('classes.create') }}">+ Thêm lớp</a>
</div>

<form class="row g-2 mb-3" method="GET" action="{{ route('classes.index') }}">
    <div class="col-md-8">
        <input type="search" name="q" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo tên lớp">
    </div>
    <div class="col-md-4 d-flex gap-2">
        <button class="btn btn-outline-primary flex-fill" type="submit">Tìm kiếm</button>
        <a class="btn btn-outline-secondary" href="{{ route('classes.index') }}">Xóa lọc</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>#</th><th>Tên lớp</th><th>Niên khóa</th><th>Số sinh viên</th><th class="text-end">Thao tác</th></tr></thead>
            <tbody>
            @forelse ($classes as $class)
                <tr>
                    <td>{{ $classes->firstItem() + $loop->index }}</td>
                    <td class="fw-semibold">{{ $class->class_name }}</td>
                    <td>{{ $class->school_year }}</td>
                    <td>{{ $class->students_count }}</td>
                    <td class="text-end actions">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('classes.show', $class) }}">Xem</a>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('classes.edit', $class) }}">Sửa</a>
                        <form class="d-inline" method="POST" action="{{ route('classes.destroy', $class) }}"
                              data-confirm="Xóa lớp {{ $class->class_name }}?">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Chưa có lớp nào.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $classes->links() }}</div>
@endsection
