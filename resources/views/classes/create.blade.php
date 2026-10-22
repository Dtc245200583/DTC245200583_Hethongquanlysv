@extends('layouts.app')

@section('title', 'Thêm lớp')

@section('content')
<h1 class="h3 mb-3">Thêm lớp học</h1>
<div class="card shadow-sm"><div class="card-body">
    <form method="POST" action="{{ route('classes.store') }}" novalidate>
        @csrf
        @include('classes._form')
    </form>
</div></div>
@endsection
