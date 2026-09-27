<?php

namespace App\Models;

use App\Enums\ExaminationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Examination extends Model
{
    protected $fillable = ['code', 'name', 'academic_year', 'term', 'status'];
    
    protected function casts(): array
    {
        return ['status' => ExaminationStatus::class, 'published_at' => 'datetime'];
    }

    public function courses(): HasMany
    {
        return $this->hasMany(ExaminationCourse::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function imports(): HasMany
    {
        return $this->hasMany(MarkImport::class);
    }

    public function examResults(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }
}
