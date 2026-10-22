@if ($errors->any())
    <div class="alert alert-danger">Vui lòng kiểm tra lại các trường được đánh dấu.</div>
@endif

<div class="row g-3">
    <div class="col-md-6">
        <label for="student_id" class="form-label">Sinh viên <span class="text-danger">*</span></label>
        <select id="student_id" name="student_id" required class="form-select @error('student_id') is-invalid @enderror">
            <option value="">— Chọn sinh viên —</option>
            @foreach ($students as $student)
                <option value="{{ $student->id }}" @selected((string) old('student_id', $grade->student_id) === (string) $student->id)>
                    {{ $student->student_code }} — {{ $student->full_name }}
                </option>
            @endforeach
        </select>
        @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="subject_name" class="form-label">Môn học <span class="text-danger">*</span></label>
        <input id="subject_name" name="subject_name" maxlength="100" required
               value="{{ old('subject_name', $grade->subject_name) }}"
               class="form-control @error('subject_name') is-invalid @enderror">
        @error('subject_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2">
        <label for="score" class="form-label">Điểm (0–10) <span class="text-danger">*</span></label>
        <input id="score" name="score" type="number" min="0" max="10" step="0.01" required
               value="{{ old('score', $grade->score) }}"
               class="form-control @error('score') is-invalid @enderror">
        @error('score')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Lưu</button>
    <a class="btn btn-outline-secondary" href="{{ route('grades.index') }}">Hủy</a>
</div>
