<?php

use App\Http\Controllers\GradeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::resource('students', StudentController::class);

Route::resource('classes', SchoolClassController::class)
    ->parameters(['classes' => 'schoolClass']);

Route::resource('grades', GradeController::class)->except(['show']);
