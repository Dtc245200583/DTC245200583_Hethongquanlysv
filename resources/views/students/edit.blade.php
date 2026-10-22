@extends('layouts.app')

@section('title', 'Sửa sinh viên')

@section('content')
<h1 class="h3 mb-3">Sửa sinh viên: {{ $student->full_name }}</h1>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('students.update', $student) }}" novalidate>
        @csrf
        @method('PUT')
        @include('students._form')
    </form>
</div></div>
@endsection
