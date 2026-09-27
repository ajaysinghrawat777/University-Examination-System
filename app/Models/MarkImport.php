<?php

namespace App\Models;

use App\Enums\ImportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarkImport extends Model
{
    protected $fillable = ['uuid', 'examination_id', 'idempotency_key', 'file_path', 'file_sha256', 'status', 'total_rows', 'processed_rows', 'failed_rows', 'started_at', 'completed_at', 'failure_reason'];

    protected function casts(): array
    {
        return ['status' => ImportStatus::class, 'started_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function examination(): BelongsTo
    {
        return $this->belongsTo(Examination::class);
    }
}
