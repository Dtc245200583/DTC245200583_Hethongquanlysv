<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'studentCount' => Student::count(),
            'classCount' => SchoolClass::count(),
            'gradeCount' => Grade::count(),
            'averageScore' => Grade::avg('score'),
            'recentStudents' => Student::with('schoolClass')->latest()->take(5)->get(),
        ]);
    }
}
