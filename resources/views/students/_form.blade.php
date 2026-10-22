@if ($errors->any())
    <div class="alert alert-danger">Vui lòng kiểm tra lại các trường được đánh dấu.</div>
@endif

<div class="row g-3">
    <div class="col-md-4">
        <label for="student_code" class="form-label">Mã sinh viên <span class="text-danger">*</span></label>
        <input id="student_code" name="student_code" maxlength="20" required
               value="{{ old('student_code', $student->student_code) }}"
               class="form-control @error('student_code') is-invalid @enderror">
        @error('student_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
        <input id="full_name" name="full_name" maxlength="100" required
               value="{{ old('full_name', $student->full_name) }}"
               class="form-control @error('full_name') is-invalid @enderror">
        @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
        <input id="email" name="email" type="email" maxlength="150" required
               value="{{ old('email', $student->email) }}"
               class="form-control @error('email') is-invalid @enderror">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="date_of_birth" class="form-label">Ngày sinh</label>
        <input id="date_of_birth" name="date_of_birth" type="date"
               value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
               class="form-control @error('date_of_birth') is-invalid @enderror">
        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="class_id" class="form-label">Lớp <span class="text-danger">*</span></label>
        <select id="class_id" name="class_id" required class="form-select @error('class_id') is-invalid @enderror">
            <option value="">— Chọn lớp —</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected((string) old('class_id', $student->class_id) === (string) $class->id)>
                    {{ $class->class_name }} ({{ $class->school_year }})
                </option>
            @endforeach
        </select>
        @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Lưu</button>
    <a class="btn btn-outline-secondary" href="{{ route('students.index') }}">Hủy</a>
</div>
