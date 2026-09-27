<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentComponent extends Model
{
    protected $fillable = ['course_id', 'code', 'name'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
