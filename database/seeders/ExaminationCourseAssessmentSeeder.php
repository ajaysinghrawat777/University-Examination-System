<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ExaminationCourseAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Get all examination-course mappings.
         */
        $examinationCourses = DB::table('examination_courses')
            ->select('id', 'course_id')
            ->whereBetween('examination_id', [1, 10])
            ->whereBetween('course_id', [1, 100])
            ->get();

        if ($examinationCourses->isEmpty()) {
            throw new RuntimeException(
                'No examination courses found. Please run ExaminationCourseSeeder first.'
            );
        }

        /*
         * Get the three assessment components for each course.
         *
         * Expected:
         * ASSIGNMENT
         * FINAL
         * VIVA
         */
        $components = DB::table('assessment_components')
            ->select('id', 'course_id', 'code')
            ->whereIn('code', [
                'ASSIGNMENT',
                'FINAL',
                'VIVA',
            ])
            ->whereBetween('course_id', [1, 100])
            ->get()
            ->groupBy('course_id');

        $rows = [];
        $now = now();

        foreach ($examinationCourses as $examinationCourse) {

            $courseComponents = $components->get($examinationCourse->course_id, collect());

            foreach ($courseComponents as $component) {

                $maxMarks = match ($component->code) {
                    'ASSIGNMENT' => 20,
                    'VIVA'       => 10,
                    'FINAL'      => 70,
                    default      => 0,
                };

                $passMarks = match ($component->code) {
                    'ASSIGNMENT' => 8,
                    'VIVA'       => 4,
                    'FINAL'      => 28,
                    default      => 0,
                };

                $weightage = match ($component->code) {
                    'ASSIGNMENT' => 0.20,
                    'VIVA'       => 0.10,
                    'FINAL'      => 0.70,
                    default      => 0,
                };

                $rows[] = [
                    'examination_course_id' => $examinationCourse->id,
                    'assessment_component_id' => $component->id,
                    'max_marks' => $maxMarks,
                    'pass_marks' => $passMarks,
                    'weightage' => $weightage,
                    'is_mandatory' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        /*
         * Insert in chunks and make the seeder idempotent.
         */
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('examination_course_assessments')->upsert(
                $chunk,
                [
                    'examination_course_id',
                    'assessment_component_id',
                ],
                [
                    'max_marks',
                    'pass_marks',
                    'weightage',
                    'is_mandatory',
                    'updated_at',
                ]
            );
        }

        $this->command->info(
            'Examination course assessments seeded: ' . count($rows)
        );
    }
}