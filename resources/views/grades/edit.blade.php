@extends('layouts.app')

@section('title', 'Sửa điểm')

@section('content')
<h1 class="h3 mb-3">Sửa điểm môn {{ $grade->subject_name }}</h1>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('grades.update', $grade) }}" novalidate>
        @csrf
        @method('PUT')
        @include('grades._form')
    </form>
</div></div>
@endsection
