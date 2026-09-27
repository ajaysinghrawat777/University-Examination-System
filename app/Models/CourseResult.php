<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseResult extends Model
{
    protected $fillable = ['examination_id', 'student_id', 'course_id', 'total_marks', 'max_marks', 'percentage', 'grade', 'status', 'calculated_at'];
    
    protected function casts(): array
    {
        return [
            'total_marks' => 'decimal:2',
            'max_marks' => 'decimal:2',
            'percentage' => 'decimal:4',
            'calculated_at' => 'datetime'
        ];
    }
}
