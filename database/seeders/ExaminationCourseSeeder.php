<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExaminationCourseSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [];
        $now = now();

        for ($examinationId = 1; $examinationId <= 10; $examinationId++) {
            for ($courseId = 1; $courseId <= 100; $courseId++) {
                $rows[] = [
                    'examination_id' => $examinationId,
                    'course_id' => $courseId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('examination_courses')->upsert(
                $chunk,
                ['examination_id', 'course_id'],
                ['updated_at']
            );
        }

        $this->command->info(
            'Examination courses seeded: ' . count($rows)
        );
    }
}