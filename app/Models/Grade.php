<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $fillable = ['student_id', 'subject_name', 'score'];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    protected function rank(): Attribute
    {
        return Attribute::get(fn () => self::rankFor((float) $this->score));
    }

    /**
     * Xếp loại theo thang điểm 10. Trả về [nhãn, màu Bootstrap].
     */
    public static function rankFor(?float $score): array
    {
        return match (true) {
            $score === null => ['Chưa có điểm', 'secondary'],
            $score >= 8.0 => ['Giỏi', 'success'],
            $score >= 6.5 => ['Khá', 'primary'],
            $score >= 5.0 => ['Trung bình', 'warning'],
            default => ['Yếu', 'danger'],
        };
    }
}
