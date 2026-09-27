<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AssessmentComponentSeeder extends Seeder
{
    public function run(): void
    {
        $courses = DB::table('courses')->pluck('id');

        if ($courses->isEmpty()) {
            throw new RuntimeException(
                'No courses found. Please run CourseSeeder first.'
            );
        }

        $components = [
            [
                'code' => 'ASSIGNMENT',
                'name' => 'Assignment',
            ],
            [
                'code' => 'FINAL',
                'name' => 'Final Examination',
            ],
            [
                'code' => 'VIVA',
                'name' => 'Viva Voce',
            ],
        ];

        $rows = [];
        $now = now();

        foreach ($courses as $courseId) {
            foreach ($components as $component) {
                $rows[] = [
                    'course_id' => $courseId,
                    'code' => $component['code'],
                    'name' => $component['name'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('assessment_components')->upsert(
                $chunk,
                ['course_id', 'code'],
                ['name', 'updated_at']
            );
        }

        $this->command->info(
            "Inserted/updated {$courses->count()} courses × 3 assessment components = "
            . count($rows)
            . ' records.'
        );
    }
}