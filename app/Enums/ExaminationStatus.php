<?php

namespace App\Enums;

enum ExaminationStatus: string
{
    case Draft = 'draft';
    case Open = 'open';
    case Processing = 'processing';
    case Published = 'published';
    case Archived = 'archived';
}
