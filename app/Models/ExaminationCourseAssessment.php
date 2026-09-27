<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExaminationCourseAssessment extends Model
{
    protected $fillable = ['examination_course_id', 'assessment_component_id', 'max_marks', 'pass_marks', 'weightage', 'is_mandatory'];

    protected function casts(): array
    {
        return ['max_marks' => 'decimal:2', 'pass_marks' => 'decimal:2', 'weightage' => 'decimal:4', 'is_mandatory' => 'boolean'];
    }

    public function examinationCourse(): BelongsTo
    {
        return $this->belongsTo(ExaminationCourse::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(AssessmentComponent::class, 'assessment_component_id');
    }
}
