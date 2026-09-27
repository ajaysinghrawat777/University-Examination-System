<?php

namespace App\Jobs;

use App\Models\Examination;
use App\Services\ResultCalculator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;

final class RecalculateStudentResult implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;
    public int $tries = 5;
    public int $timeout = 60;
    public function __construct(public int $examId, public int $studentId) {}
    public function middleware(): array
    {
        return [(new WithoutOverlapping("result:{$this->examId}:{$this->studentId}"))->expireAfter(120)->releaseAfter(2)];
    }
    public function handle(ResultCalculator $calculator): void
    {
        $calculator->calculateStudent(Examination::findOrFail($this->examId), $this->studentId);
    }
}
