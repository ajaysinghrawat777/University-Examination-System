<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgrammeSeeder extends Seeder
{
    public function run(): void
    {
        $total = 500;
        $chunkSize = 100;

        $faker = fake();

        for ($start = 1; $start <= $total; $start += $chunkSize) {
            $rows = [];
            $end = min($start + $chunkSize - 1, $total);

            $now = now();

            for ($i = $start; $i <= $end; $i++) {
                $rows[] = [
                    'code' => 'PROG-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'name' => $faker->randomElement([
                        'Bachelor of Computer Applications',
                        'Bachelor of Science',
                        'Bachelor of Commerce',
                        'Bachelor of Business Administration',
                        'Bachelor of Arts',
                        'Bachelor of Engineering',
                        'Bachelor of Technology',
                        'Master of Computer Applications',
                        'Master of Science',
                        'Master of Commerce',
                        'Master of Business Administration',
                        'Master of Arts',
                        'Master of Technology',
                        'Diploma in Computer Science',
                        'Diploma in Business Administration',
                    ]) . ' ' . $faker->randomElement([
                        'Computer Science',
                        'Information Technology',
                        'Mathematics',
                        'Physics',
                        'Chemistry',
                        'Commerce',
                        'Management',
                        'Economics',
                        'English',
                        'Electronics',
                    ]),
                    'is_active' => $faker->boolean(90),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('programmes')->insert($rows);

            $this->command->info("Inserted {$end} / {$total} programmes");
        }
    }
}