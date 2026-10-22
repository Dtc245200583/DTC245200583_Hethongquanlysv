<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(Request $request): View
    {
        $keyword = trim((string) $request->query('q', ''));

        $classes = SchoolClass::withCount('students')
            ->when($keyword !== '', fn ($query) => $query->where('class_name', 'like', "%{$keyword}%"))
            ->orderBy('class_name')
            ->paginate(10)
            ->withQueryString();

        return view('classes.index', ['classes' => $classes, 'keyword' => $keyword]);
    }

    public function create(): View
    {
        return view('classes.create', ['schoolClass' => new SchoolClass()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $schoolClass = SchoolClass::create($this->validated($request));
        Log::info('class.created', ['id' => $schoolClass->id, 'name' => $schoolClass->class_name]);

        return redirect()->route('classes.index')
            ->with('success', "Đã thêm lớp {$schoolClass->class_name}.");
    }

    public function show(SchoolClass $schoolClass): View
    {
        $students = $schoolClass->students()
            ->withAvg('grades', 'score')
            ->orderBy('student_code')
            ->paginate(15);

        return view('classes.show', ['schoolClass' => $schoolClass, 'students' => $students]);
    }

    public function edit(SchoolClass $schoolClass): View
    {
        return view('classes.edit', ['schoolClass' => $schoolClass]);
    }

    public function update(Request $request, SchoolClass $schoolClass): RedirectResponse
    {
        $schoolClass->update($this->validated($request, $schoolClass));
        Log::info('class.updated', ['id' => $schoolClass->id, 'name' => $schoolClass->class_name]);

        return redirect()->route('classes.index')
            ->with('success', "Đã cập nhật lớp {$schoolClass->class_name}.");
    }

    public function destroy(SchoolClass $schoolClass): RedirectResponse
    {
        // Khóa ngoại students.class_id là RESTRICT; kiểm tra trước để báo lỗi thân thiện.
        if ($schoolClass->students()->exists()) {
            return back()->with('error', "Không thể xóa lớp {$schoolClass->class_name} vì lớp vẫn còn sinh viên.");
        }

        $schoolClass->delete();
        Log::warning('class.deleted', ['id' => $schoolClass->id, 'name' => $schoolClass->class_name]);

        return redirect()->route('classes.index')
            ->with('success', "Đã xóa lớp {$schoolClass->class_name}.");
    }

    private function validated(Request $request, ?SchoolClass $schoolClass = null): array
    {
        return $request->validate([
            'class_name' => ['required', 'string', 'max:100', Rule::unique('classes', 'class_name')->ignore($schoolClass)],
            'school_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
        ]);
    }
}
