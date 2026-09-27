<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Examination;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $examinationIds = Examination::query()
            ->pluck('id');

        $studentIds = range(1, 100);

        if ($examinationIds->isEmpty()) {
            $this->command->warn('No examinations found. Skipping EnrollmentSeeder.');

            return;
        }

        $rows = [];
        $now = now();

        foreach ($examinationIds as $examinationId) {
            foreach ($studentIds as $studentId) {
                $rows[] = [
                    'examination_id' => $examinationId,
                    'student_id' => $studentId,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            Enrollment::upsert(
                $chunk,
                ['examination_id', 'student_id'],
                ['status', 'updated_at']
            );
        }

        $this->command->info(
            "Enrolled students 1-100 in {$examinationIds->count()} examinations."
        );

        $this->command->info(
            'Total enrollment records: ' . count($rows)
        );
    }
}