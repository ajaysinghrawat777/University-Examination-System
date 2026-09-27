<?php

namespace App\Jobs;

use App\Enums\ImportStatus;
use App\Models\MarkImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

final class FinalizeMarksImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;
    public function __construct(public int $importId) {}
    public function handle(): void
    {
        $import = MarkImport::findOrFail($this->importId);
        $import->update(['status' => $import->failed_rows > 0 ? ImportStatus::FailedValidation : ImportStatus::Completed, 'completed_at' => now()]);
    }
}
