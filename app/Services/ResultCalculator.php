<?php

namespace App\Services;

use App\Contracts\GradingPolicy;
use App\Models\CourseResult;
use App\Models\ExamResult;
use App\Models\Examination;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

final class ResultCalculator
{
    public function __construct(private readonly GradingPolicy $grading) {}
    public function calculateStudent(Examination $exam, int $studentId): void
    {
        DB::transaction(function () use ($exam, $studentId) {
            $enrollment = Enrollment::query()->where('examination_id', $exam->id)->where('student_id', $studentId)->lockForUpdate()->firstOrFail();
            $examCourses = $exam->courses()->with(['course', 'assessments.component'])->get();
            $marks = DB::table('marks as m')->join('examination_course_assessments as eca', 'eca.id', '=', 'm.examination_course_assessment_id')->where('m.student_id', $studentId)->whereIn('eca.examination_course_id', $examCourses->pluck('id'))->get()->keyBy('examination_course_assessment_id');
            $examTotal = 0.0;
            $examMax = 0.0;
            $allPassed = true;
            $courseCount = 0;
            foreach ($examCourses as $ec) {
                $courseTotal = 0.0;
                $courseMax = 0.0;
                $coursePassed = true;
                $complete = true;
                $courseCount++;
                foreach ($ec->assessments as $a) {
                    $max = (float)$a->max_marks;
                    $courseMax += $max;
                    $examMax += $max;
                    $row = $marks->get($a->id);
                    if (!$row) {
                        $complete = false;
                        continue;
                    }
                    $value = (float)$row->marks;
                    $courseTotal += $value;
                    $examTotal += $value;
                    if ($a->is_mandatory && $value < (float)$a->pass_marks) $coursePassed = false;
                }
                $pct = $courseMax > 0 ? ($courseTotal / $courseMax) * 100 : 0;
                $passed = $complete && $coursePassed;
                $grade = $this->grading->grade($pct, $passed);
                $status = $passed ? 'pass' : 'fail';
                CourseResult::query()->updateOrCreate(['examination_id' => $exam->id, 'student_id' => $studentId, 'course_id' => $ec->course_id], ['total_marks' => $courseTotal, 'max_marks' => $courseMax, 'percentage' => $pct, 'grade' => $grade, 'status' => $status, 'calculated_at' => now()]);
                if (!$passed) $allPassed = false;
            }
            $pct = $examMax > 0 ? ($examTotal / $examMax) * 100 : 0;
            $grade = $this->grading->grade($pct, $allPassed);
            $status = $allPassed ? 'pass' : 'fail';
            ExamResult::query()->updateOrCreate(['examination_id' => $exam->id, 'student_id' => $studentId], ['courses_count' => $courseCount, 'total_marks' => $examTotal, 'max_marks' => $examMax, 'percentage' => $pct, 'grade' => $grade, 'status' => $status, 'calculated_at' => now(), 'published_at' => null]);
        }, attempts: 3);
    }
}
