<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultPublication extends Model
{
    protected $fillable = ['examination_id', 'version', 'published_by', 'checksum', 'published_at'];
    
    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }
}
