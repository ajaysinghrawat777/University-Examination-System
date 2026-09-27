<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mark extends Model
{
    protected $fillable = ['student_id', 'examination_course_assessment_id', 'marks', 'mark_import_id', 'source_fingerprint'];

    protected function casts(): array
    {
        return ['marks' => 'decimal:2'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
    
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(ExaminationCourseAssessment::class, 'examination_course_assessment_id');
    }
}
