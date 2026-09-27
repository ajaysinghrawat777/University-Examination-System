<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExaminationSeeder extends Seeder
{
    public function run(): void
    {
        $total = 10;
        $chunkSize = 10;

        $faker = fake();

        $terms = [
            'Semester 1',
            'Semester 2',
            'Semester 3',
            'Semester 4',
            'Semester 5',
            'Semester 6',
            'Semester 7',
            'Semester 8',
        ];

        $statuses = [
            // 'draft',
            'open',
            // 'processing',
            // 'published',
            // 'archived',
        ];

        for ($start = 1; $start <= $total; $start += $chunkSize) {
            $rows = [];
            $end = min($start + $chunkSize - 1, $total);
            $now = now();

            for ($i = $start; $i <= $end; $i++) {
                $academicYearStart = $faker->numberBetween(2025, 2026);
                $academicYear = $academicYearStart . '-' . ($academicYearStart + 1);

                $term = $faker->randomElement($terms);
                $status = $faker->randomElement($statuses);

                $publishedAt = $status === 'published'
                    ? $faker->dateTimeBetween('-2 years', 'now')
                    : null;

                $rows[] = [
                    'code' => 'EXAM-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'name' => $faker->randomElement([
                        'End Semester Examination',
                        'Mid Semester Examination',
                        'University Final Examination',
                        'Annual Examination',
                        'Internal Assessment Examination',
                        'Supplementary Examination',
                        'Backlog Examination',
                        'Improvement Examination',
                        'Practical Examination',
                        'Theory Examination',
                    ]) . ' ' . $term,
                    'academic_year' => $academicYear,
                    'term' => $term,
                    'status' => $status,
                    'published_at' => $publishedAt,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('examinations')->insert($rows);

            $this->command->info(
                "Inserted {$end} / {$total} examinations"
            );
        }
    }
}