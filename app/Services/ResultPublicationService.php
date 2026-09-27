<?php

namespace App\Services;

use App\Enums\ExaminationStatus;
use App\Models\Examination;
use App\Models\ResultPublication;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class ResultPublicationService
{
    public function publish(Examination $exam, int $userId): ResultPublication
    {
        $lock = Cache::lock("publish:examination:{$exam->id}", 120);
        return $lock->block(5, function () use ($exam, $userId) {
            return DB::transaction(function () use ($exam, $userId) {
                $exam->refresh();
                if ($exam->status === ExaminationStatus::Published) return ResultPublication::query()->where('examination_id', $exam->id)->latest('version')->firstOrFail();
                if ($exam->status !== ExaminationStatus::Processing) throw new RuntimeException('Examination must be in processing state before publication.');
                if ($exam->imports()->whereIn('status', ['queued', 'processing'])->exists()) throw new RuntimeException('Mark imports are still processing.');
                $expected = $exam->enrollments()->where('status', 'active')->count();
                $actual = $exam->examResults()->count();
                if ($expected !== $actual) throw new RuntimeException("Results are incomplete: {$actual}/{$expected} calculated.");
                $version = ((int)ResultPublication::query()->where('examination_id', $exam->id)->max('version')) + 1;
                $checksum = hash('sha256', json_encode($exam->examResults()->orderBy('student_id')->get(['student_id', 'total_marks', 'percentage', 'grade', 'status'])->values()->all()));
                $pub = ResultPublication::create(['examination_id' => $exam->id, 'version' => $version, 'published_by' => $userId, 'checksum' => $checksum, 'published_at' => now()]);
                $exam->update(['status' => ExaminationStatus::Published, 'published_at' => now()]);
                $exam->examResults()->update(['published_at' => now()]);
                return $pub;
            }, attempts: 3);
        });
    }
}
