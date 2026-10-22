@if ($errors->any())
    <div class="alert alert-danger">Vui lòng kiểm tra lại các trường được đánh dấu.</div>
@endif

<div class="row g-3">
    <div class="col-md-6">
        <label for="class_name" class="form-label">Tên lớp <span class="text-danger">*</span></label>
        <input id="class_name" name="class_name" maxlength="100" required
               value="{{ old('class_name', $schoolClass->class_name) }}"
               class="form-control @error('class_name') is-invalid @enderror">
        @error('class_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="school_year" class="form-label">Niên khóa <span class="text-danger">*</span></label>
        <input id="school_year" name="school_year" maxlength="20" required placeholder="VD: 2024-2028"
               value="{{ old('school_year', $schoolClass->school_year) }}"
               class="form-control @error('school_year') is-invalid @enderror">
        @error('school_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Định dạng NNNN-NNNN.</div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Lưu</button>
    <a class="btn btn-outline-secondary" href="{{ route('classes.index') }}">Hủy</a>
</div>
