<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AssessmentComponentSeeder extends Seeder
{
    public function run(): void
    {
        $course = DB::table('courses')->first();

        if (!$course) {
            throw new RuntimeException(
                'No courses found. Please run CourseSeeder first.'
            );
        }

        $components = [
            [
                'code' => 'QUIZ',
                'name' => 'Quiz',
            ],
            [
                'code' => 'ASSIGNMENT',
                'name' => 'Assignment',
            ],
            [
                'code' => 'ATTENDANCE',
                'name' => 'Attendance',
            ],
            [
                'code' => 'MIDTERM',
                'name' => 'Mid Term Examination',
            ],
            [
                'code' => 'PROJECT',
                'name' => 'Project',
            ],
            [
                'code' => 'PRACTICAL',
                'name' => 'Practical Examination',
            ],
            [
                'code' => 'VIVA',
                'name' => 'Viva Voce',
            ],
            [
                'code' => 'LAB',
                'name' => 'Laboratory',
            ],
            [
                'code' => 'INTERNAL',
                'name' => 'Internal Assessment',
            ],
            [
                'code' => 'FINAL',
                'name' => 'Final Examination',
            ],
        ];

        $now = now();

        foreach ($components as &$component) {
            $component['course_id'] = $course->id;
            $component['created_at'] = $now;
            $component['updated_at'] = $now;
        }

        DB::table('assessment_components')->insert($components);

        $this->command->info('Inserted 10 assessment components.');
    }
}