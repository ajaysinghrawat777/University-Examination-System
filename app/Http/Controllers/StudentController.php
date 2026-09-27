<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Students/Index', [
            'students' => Student::query()->with('programme')->select(['id', 'programme_id', 'admission_no', 'name'])->orderBy('id')->paginate(50)->through(fn($s) => ['id' => $s->id, 'admission_no' => $s->admission_no, 'name' => $s->name, 'programme' => $s->programme?->name])
        ]);
    }
}
