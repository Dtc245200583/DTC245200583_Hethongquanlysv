<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Trang chủ') · Quản lý sinh viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="{{ route('home') }}">Student Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Mở menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link @if(request()->routeIs('home')) active @endif" href="{{ route('home') }}">Tổng quan</a></li>
                <li class="nav-item"><a class="nav-link @if(request()->routeIs('students.*')) active @endif" href="{{ route('students.index') }}">Sinh viên</a></li>
                <li class="nav-item"><a class="nav-link @if(request()->routeIs('classes.*')) active @endif" href="{{ route('classes.index') }}">Lớp học</a></li>
                <li class="nav-item"><a class="nav-link @if(request()->routeIs('grades.*')) active @endif" href="{{ route('grades.index') }}">Điểm số</a></li>
            </ul>
            <a class="btn btn-sm btn-outline-light" href="http://localhost:8081" target="_blank" rel="noopener">phpMyAdmin</a>
        </div>
    </div>
</nav>

<main class="container py-4">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    @yield('content')
</main>

<footer class="border-top bg-white py-3 small text-muted">
    <div class="container d-flex flex-wrap justify-content-between gap-2">
        <span>Hệ thống quản lý sinh viên — Laravel · MySQL · Docker Compose</span>
        <span>Nguyễn Thị Quế · DTC245200583</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
