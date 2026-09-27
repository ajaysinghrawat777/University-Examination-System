<?php

namespace App\Jobs;

use App\Enums\ImportStatus;
use App\Models\MarkImport;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class PrepareMarksImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, Batchable;
    public int $tries = 3;
    public function __construct(public int $importId) {}
    public function handle(): void
    {
        $import = MarkImport::findOrFail($this->importId);
        $import->update(['status' => ImportStatus::Processing, 'started_at' => now()]);
        $file = Storage::path($import->file_path);
        $fh = new \SplFileObject($file);
        $fh->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);
        $header = $fh->fgetcsv();
        Log::info("Header", $header);
        $required = ['student_admission_no', 'course_code', 'assessment_component_code', 'marks'];
        if ($header !== $required) throw new \RuntimeException('CSV header must be exactly: ' . implode(',', $required));
        $chunk = [];
        $rowNumber = 1;
        $total = 0;
        $jobs = [];
        while (!$fh->eof()) {
            $row = $fh->fgetcsv();
            if ($row === false || $row === [null]) continue;
            $rowNumber++;
            $total++;
            $chunk[] = ['row_number' => $rowNumber, 'payload' => $row];
            if (count($chunk) >= 1000) {
                $jobs[] = new ProcessMarksChunk($import->id, $chunk);
                $chunk = [];
            }
        }
        if ($chunk) $jobs[] = new ProcessMarksChunk($import->id, $chunk);
        $import->update(['total_rows' => $total]);
        if (!$this->batch()) {
            \Illuminate\Support\Facades\Bus::batch($jobs)->name("marks-import:{$import->uuid}")->allowFailures()->finally(fn() => FinalizeMarksImport::dispatch($import->id))->dispatch();
        } else {
            $this->batch()->add($jobs);
        }
    }
    public function failed(Throwable $e): void
    {
        MarkImport::whereKey($this->importId)->update(['status' => ImportStatus::Failed, 'failure_reason' => $e->getMessage(), 'completed_at' => now()]);
    }
}
