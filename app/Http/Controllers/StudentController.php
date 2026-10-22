<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $keyword = trim((string) $request->query('q', ''));
        $classId = $request->query('class_id');

        $students = Student::with('schoolClass')
            ->withAvg('grades', 'score')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('student_code', 'like', "%{$keyword}%")
                        ->orWhere('full_name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->when($classId, fn ($query) => $query->where('class_id', $classId))
            ->orderBy('student_code')
            ->paginate(10)
            ->withQueryString();

        return view('students.index', [
            'students' => $students,
            'classes' => SchoolClass::orderBy('class_name')->get(),
            'keyword' => $keyword,
            'classId' => $classId,
        ]);
    }

    public function create(): View
    {
        return view('students.create', [
            'student' => new Student(),
            'classes' => SchoolClass::orderBy('class_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $student = Student::create($this->validated($request));
        Log::info('student.created', ['id' => $student->id, 'code' => $student->student_code]);

        return redirect()->route('students.index')
            ->with('success', "Đã thêm sinh viên {$student->full_name}.");
    }

    public function show(Student $student): View
    {
        $student->load(['schoolClass', 'grades' => fn ($q) => $q->orderBy('subject_name')]);

        return view('students.show', ['student' => $student]);
    }

    public function edit(Student $student): View
    {
        return view('students.edit', [
            'student' => $student,
            'classes' => SchoolClass::orderBy('class_name')->get(),
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $student->update($this->validated($request, $student));
        Log::info('student.updated', ['id' => $student->id, 'code' => $student->student_code]);

        return redirect()->route('students.index')
            ->with('success', "Đã cập nhật sinh viên {$student->full_name}.");
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();
        Log::warning('student.deleted', ['id' => $student->id, 'code' => $student->student_code]);

        return redirect()->route('students.index')
            ->with('success', "Đã xóa sinh viên {$student->full_name}.");
    }

    private function validated(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'student_code' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9]+$/',
                Rule::unique('students', 'student_code')->ignore($student)],
            'full_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('students', 'email')->ignore($student)],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],
        ]);
    }
}
