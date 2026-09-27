<?php

namespace App\Contracts;

interface GradingPolicy
{
    public function grade(float $percentage, bool $passed): string;
}
