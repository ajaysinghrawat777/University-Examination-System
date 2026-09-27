<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Programme extends Model
{
    protected $fillable = ['code', 'name', 'is_active'];
    
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
