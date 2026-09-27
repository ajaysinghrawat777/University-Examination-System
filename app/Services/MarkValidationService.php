<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\ExaminationCourseAssessment;
use App\Models\Student;
use RuntimeException;

final class MarkValidationService
{
    public function validate(Student $student, ExaminationCourseAssessment $assessment, float $marks): void
    {
        if (!Enrollment::query()->where('examination_id', $assessment->examinationCourse->examination_id)->where('student_id', $student->id)->where('status', 'active')->exists()) throw new RuntimeException('Student is not actively enrolled in the examination.');
        if ($marks < 0 || $marks > (float)$assessment->max_marks) throw new RuntimeException('Marks must be between 0 and the component maximum.');
    }
}
