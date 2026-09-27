<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use App\Models\MarkImport;
use App\Models\Student;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Dashboard', [
            'metrics' => [
                'students' => Student::count(),
                'examinations' => Examination::count(),
                'published' => Examination::where('status', 'published')->count(),
                'pendingImports' => MarkImport::whereIn('status', ['queued', 'processing'])->count()
            ]
        ]);
    }
}
