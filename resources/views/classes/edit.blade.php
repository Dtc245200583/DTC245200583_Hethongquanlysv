@extends('layouts.app')

@section('title', 'Sửa lớp')

@section('content')
<h1 class="h3 mb-3">Sửa lớp: {{ $schoolClass->class_name }}</h1>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('classes.update', $schoolClass) }}" novalidate>
        @csrf
        @method('PUT')
        @include('classes._form')
    </form>
</div></div>
@endsection
