<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResult extends Model
{
    protected $fillable = ['examination_id', 'student_id', 'courses_count', 'total_marks', 'max_marks', 'percentage', 'grade', 'status', 'calculated_at', 'published_at'];
    
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    protected function casts(): array
    {
        return ['total_marks' => 'decimal:2', 'max_marks' => 'decimal:2', 'percentage' => 'decimal:4', 'calculated_at' => 'datetime', 'published_at' => 'datetime'];
    }
}
