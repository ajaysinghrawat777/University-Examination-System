<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [];

        for ($i = 1; $i <= 500; $i++) {
            $courses[] = [
                'code' => 'COURSE-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'name' => fake()->sentence(fake()->numberBetween(2, 5)),
                'credits' => fake()->randomFloat(8, 9, 10),
                'is_active' => fake()->boolean(95),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Course::insert($courses);
    }
}
