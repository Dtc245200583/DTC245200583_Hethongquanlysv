@extends('layouts.app')

@section('title', 'Nhập điểm')

@section('content')
<h1 class="h3 mb-3">Nhập điểm</h1>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('grades.store') }}" novalidate>
        @csrf
        @include('grades._form')
    </form>
</div></div>
@endsection
