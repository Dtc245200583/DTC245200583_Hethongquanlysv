@extends('layouts.app')

@section('title', $student->full_name)

@section('content')
@php
    [$rankLabel, $rankColor] = \App\Models\Grade::rankFor($student->grades->isEmpty() ? null : (float) $student->grades->avg('score'));
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">{{ $student->full_name }} <small class="text-muted">({{ $student->student_code }})</small></h1>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="{{ route('students.edit', $student) }}">Sửa thông tin</a>
        <a class="btn btn-outline-secondary" href="{{ route('students.index') }}">Quay lại</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm h-100"><div class="card-body">
            <dl class="mb-0">
                <dt>Email</dt><dd>{{ $student->email }}</dd>
                <dt>Ngày sinh</dt><dd>{{ $student->date_of_birth?->format('d/m/Y') ?? '—' }}</dd>
                <dt>Lớp</dt>
                <dd><a href="{{ route('classes.show', $student->schoolClass) }}">{{ $student->schoolClass->class_name }}</a> ({{ $student->schoolClass->school_year }})</dd>
                <dt>Điểm trung bình</dt>
                <dd>
                    {{ $student->grades->isEmpty() ? '—' : number_format($student->grades->avg('score'), 2) }}
                    <span class="badge text-bg-{{ $rankColor }}">{{ $rankLabel }}</span>
                </dd>
            </dl>
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Bảng điểm</span>
                <a class="btn btn-sm btn-primary" href="{{ route('grades.create', ['student_id' => $student->id]) }}">+ Nhập điểm</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light"><tr><th>Môn học</th><th>Điểm</th><th>Xếp loại</th><th class="text-end">Thao tác</th></tr></thead>
                    <tbody>
                    @forelse ($student->grades as $grade)
                        <tr>
                            <td>{{ $grade->subject_name }}</td>
                            <td>{{ $grade->score }}</td>
                            <td><span class="badge text-bg-{{ $grade->rank[1] }}">{{ $grade->rank[0] }}</span></td>
                            <td class="text-end actions">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('grades.edit', $grade) }}">Sửa</a>
                                <form class="d-inline" method="POST" action="{{ route('grades.destroy', $grade) }}"
                                      data-confirm="Xóa điểm môn {{ $grade->subject_name }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Chưa có điểm.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
