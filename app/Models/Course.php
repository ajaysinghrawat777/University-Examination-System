<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = ['code', 'name', 'credits', 'is_active'];
    
    public function components(): HasMany
    {
        return $this->hasMany(AssessmentComponent::class);
    }
}
