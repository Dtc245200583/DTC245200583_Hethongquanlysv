<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// "Class" là từ khóa của PHP nên model được đặt tên SchoolClass, ánh xạ tới bảng classes.
class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $fillable = ['class_name', 'school_year'];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}
