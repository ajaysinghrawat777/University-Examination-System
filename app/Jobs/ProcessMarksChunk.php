<?php

namespace App\Jobs;

use App\Models\ExaminationCourseAssessment;
use App\Models\Mark;
use App\Models\MarkImport;
use App\Models\MarkImportError;
use App\Models\Student;
use App\Services\MarkValidationService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ProcessMarksChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, Batchable;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public int $importId,
        public array $rows
    ) {
    }

    public function handle(MarkValidationService $validator): void
    {
        $import = MarkImport::findOrFail($this->importId);

        $examId = $import->examination_id;

        /*
         * Get admission numbers from CSV rows.
         */
        $admissions = array_values(
            array_unique(
                array_filter(
                    array_map(
                        fn ($r) => trim((string) ($r['payload'][0] ?? '')),
                        $this->rows
                    )
                )
            )
        );

        $students = Student::query()
            ->whereIn('admission_no', $admissions)
            ->get()
            ->keyBy('admission_no');

        /*
         * Get component codes from CSV rows.
         */
        $componentCodes = array_values(
            array_unique(
                array_filter(
                    array_map(
                        fn ($r) => trim((string) ($r['payload'][2] ?? '')),
                        $this->rows
                    )
                )
            )
        );

        /*
         * Load configured assessments for this examination.
         */
        $assessments = ExaminationCourseAssessment::query()
            ->with([
                'examinationCourse.course',
                'component',
            ])
            ->whereHas(
                'examinationCourse',
                fn ($q) => $q->where('examination_id', $examId)
            )
            ->whereHas(
                'component',
                fn ($q) => $q->whereIn('code', $componentCodes)
            )
            ->get();

        /*
         * Build:
         * COURSE_CODE|COMPONENT_CODE => Assessment
         */
        $assessmentMap = [];

        foreach ($assessments as $assessment) {
            $key =
                $assessment->examinationCourse->course->code
                . '|'
                . $assessment->component->code;

            $assessmentMap[$key] = $assessment;
        }

        $marks = [];
        $errors = [];

        $now = now();

        /*
         * Keep track of students whose result needs recalculation.
         */
        $affected = [];

        foreach ($this->rows as $item) {
            $row = $item['payload'];
            $rowNo = $item['row_number'];

            $admission = trim((string) ($row[0] ?? ''));
            $courseCode = trim((string) ($row[1] ?? ''));
            $componentCode = trim((string) ($row[2] ?? ''));
            $raw = $row[3] ?? null;

            try {
                if (!isset($students[$admission])) {
                    throw new \RuntimeException(
                        'Unknown student admission number.'
                    );
                }

                if (!is_numeric($raw)) {
                    throw new \RuntimeException(
                        'Marks must be numeric.'
                    );
                }

                $student = $students[$admission];

                $assessmentKey = $courseCode . '|' . $componentCode;

                $assessment = $assessmentMap[$assessmentKey] ?? null;

                if (!$assessment) {
                    throw new \RuntimeException(
                        'Course/component is not configured for this examination.'
                    );
                }

                $value = (float) $raw;

                $validator->validate(
                    $student,
                    $assessment,
                    $value
                );

                $fingerprint = hash(
                    'sha256',
                    implode('|', [
                        $examId,
                        $student->id,
                        $assessment->id,
                        number_format($value, 2, '.', ''),
                    ])
                );

                $marks[] = [
                    'student_id' => $student->id,
                    'examination_course_assessment_id' => $assessment->id,
                    'marks' => $value,
                    'mark_import_id' => $import->id,
                    'source_fingerprint' => $fingerprint,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $affected[$student->id] = true;
            } catch (Throwable $e) {
                $errors[] = [
                    'mark_import_id' => $import->id,
                    'row_number' => $rowNo,

                    /*
                     * IMPORTANT:
                     * payload must be a scalar database value.
                     *
                     * Do not pass $row directly to upsert().
                     */
                    'payload' => json_encode(
                        $row,
                        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                    ),

                    'error' => $e->getMessage(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::transaction(function () use (
            $marks,
            $errors,
            $import
        ) {
            /*
             * Insert/update valid marks.
             */
            if ($marks) {
                Mark::query()->upsert(
                    $marks,
                    [
                        'student_id',
                        'examination_course_assessment_id',
                    ],
                    [
                        'marks',
                        'mark_import_id',
                        'source_fingerprint',
                        'updated_at',
                    ]
                );
            }

            /*
             * Insert/update import errors.
             */
            if ($errors) {
                MarkImportError::query()->upsert(
                    $errors,
                    [
                        'mark_import_id',
                        'row_number',
                    ],
                    [
                        'payload',
                        'error',
                        'updated_at',
                    ]
                );
            }

            /*
             * processed_rows means rows actually processed,
             * whether they succeeded or failed.
             */
            MarkImport::query()
                ->whereKey($import->id)
                ->increment(
                    'processed_rows',
                    count($marks) + count($errors)
                );

            /*
             * failed_rows means validation/import failures.
             */
            MarkImport::query()
                ->whereKey($import->id)
                ->increment(
                    'failed_rows',
                    count($errors)
                );
        }, 3);

        /*
         * Recalculate results only for successfully imported marks.
         */
        foreach (array_keys($affected) as $studentId) {
            RecalculateStudentResult::dispatch(
                $examId,
                $studentId
            )->onQueue('results');
        }
    }
}
