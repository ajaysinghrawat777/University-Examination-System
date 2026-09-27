<?php

namespace App\Services;

use App\Contracts\GradingPolicy;

final class DefaultGradingPolicy implements GradingPolicy
{
    public function grade(float $percentage, bool $passed): string
    {
        if (!$passed) return 'F';
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C',
            $percentage >= 40 => 'D',
            default => 'F'
        };
    }
}
