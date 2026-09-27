<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Examination;
use App\Models\Student;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $examinationIds = Examination::query()
            ->pluck('id')
            ->all();

        $studentIds = Student::query()
            ->pluck('id')
            ->all();

        if (empty($examinationIds)) {
            $this->command->warn('No examinations found. Skipping EnrollmentSeeder.');

            return;
        }

        if (empty($studentIds)) {
            $this->command->warn('No students found. Skipping EnrollmentSeeder.');

            return;
        }

        $rows = [];

        foreach ($examinationIds as $examinationId) {
            // Enroll a random number of students for each examination.
            $count = min(
                count($studentIds),
                random_int(50, min(200, count($studentIds)))
            );

            $selectedStudentIds = collect($studentIds)
                ->shuffle()
                ->take($count);

            foreach ($selectedStudentIds as $studentId) {
                $rows[] = [
                    'examination_id' => $examinationId,
                    'student_id' => $studentId,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (empty($rows)) {
            $this->command->warn('No enrollment records generated.');

            return;
        }

        /*
         * Upsert makes the seeder safe to run more than once.
         * The unique key is:
         * examination_id + student_id
         */
        foreach (array_chunk($rows, 500) as $chunk) {
            Enrollment::upsert(
                $chunk,
                ['examination_id', 'student_id'],
                ['status', 'updated_at']
            );
        }

        $this->command->info(
            'Enrollments seeded successfully: ' . count($rows)
        );
    }
}