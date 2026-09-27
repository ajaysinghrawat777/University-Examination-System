<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $fillable = ['programme_id', 'admission_no', 'name', 'email', 'is_active'];
    
    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }
}
