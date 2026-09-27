<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarkImportError extends Model
{
    protected $fillable = ['mark_import_id', 'row_number', 'payload', 'error'];
    
    protected $casts = ['payload' => 'array'];

    public function import(): BelongsTo
    {
        return $this->belongsTo(MarkImport::class, 'mark_import_id');
    }
}
