<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GradeController extends Controller
{
    public function index(Request $request): View
    {
        $keyword = trim((string) $request->query('q', ''));
        $subject = $request->query('subject');

        $grades = Grade::with('student.schoolClass')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->whereHas('student', function ($q) use ($keyword) {
                    $q->where('student_code', 'like', "%{$keyword}%")
                        ->orWhere('full_name', 'like', "%{$keyword}%");
                });
            })
            ->when($subject, fn ($query) => $query->where('subject_name', $subject))
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('grades.index', [
            'grades' => $grades,
            'subjects' => Grade::query()->distinct()->orderBy('subject_name')->pluck('subject_name'),
            'keyword' => $keyword,
            'subject' => $subject,
        ]);
    }

    public function create(Request $request): View
    {
        return view('grades.create', [
            'grade' => new Grade(['student_id' => $request->query('student_id')]),
            'students' => Student::orderBy('student_code')->get(['id', 'student_code', 'full_name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $grade = Grade::create($this->validated($request));
        Log::info('grade.created', ['id' => $grade->id, 'student_id' => $grade->student_id, 'score' => $grade->score]);

        return redirect()->route('students.show', $grade->student_id)
            ->with('success', "Đã nhập điểm môn {$grade->subject_name}.");
    }

    public function edit(Grade $grade): View
    {
        return view('grades.edit', [
            'grade' => $grade,
            'students' => Student::orderBy('student_code')->get(['id', 'student_code', 'full_name']),
        ]);
    }

    public function update(Request $request, Grade $grade): RedirectResponse
    {
        $grade->update($this->validated($request, $grade));
        Log::info('grade.updated', ['id' => $grade->id, 'student_id' => $grade->student_id, 'score' => $grade->score]);

        return redirect()->route('students.show', $grade->student_id)
            ->with('success', "Đã cập nhật điểm môn {$grade->subject_name}.");
    }

    public function destroy(Grade $grade): RedirectResponse
    {
        $grade->delete();
        Log::warning('grade.deleted', ['id' => $grade->id, 'student_id' => $grade->student_id]);

        return back()->with('success', "Đã xóa điểm môn {$grade->subject_name}.");
    }

    private function validated(Request $request, ?Grade $grade = null): array
    {
        return $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'subject_name' => ['required', 'string', 'max:100',
                Rule::unique('grades', 'subject_name')
                    ->where('student_id', $request->input('student_id'))
                    ->ignore($grade)],
            'score' => ['required', 'numeric', 'between:0,10', 'decimal:0,2'],
        ]);
    }
}
