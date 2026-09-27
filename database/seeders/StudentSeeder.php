<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Programme;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $total = 1_000_00;
        $chunkSize = 5_000;

        $programmeIds = Programme::query()
            ->pluck('id')
            ->all();

        if (empty($programmeIds)) {
            throw new \RuntimeException(
                'No programmes found. Please seed programmes before students.'
            );
        }

        $faker = fake();

        for ($start = 1; $start <= $total; $start += $chunkSize) {
            $rows = [];
            $end = min($start + $chunkSize - 1, $total);

            $now = Carbon::now();

            for ($i = $start; $i <= $end; $i++) {
                $admissionNo = 'ADM' . str_pad($i, 10, '0', STR_PAD_LEFT);

                $rows[] = [
                    'programme_id' => $faker->randomElement($programmeIds),
                    'admission_no' => $admissionNo,
                    'name' => $faker->name(),
                    'email' => 'student' . $i . '@university.test',
                    'is_active' => $faker->boolean(95),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('students')->insert($rows);

            $this->command->info(
                "Inserted {$end} / {$total} students"
            );
        }
    }
}
