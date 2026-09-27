<?php

namespace App\Http\Controllers;

use App\Enums\ExaminationStatus;
use App\Models\Examination;
use App\Models\MarkImport;
use App\Jobs\PrepareMarksImport;
use App\Http\Requests\StoreMarkImportRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class ExaminationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Examinations/Index', [
            'examinations' => Examination::query()->withCount(['enrollments as enrolled_count' => fn($q) => $q->where('status', 'active')])->withCount('courses')->latest('id')->paginate(25)
        ]);
    }

    public function show(Examination $examination): Response
    {
        return Inertia::render('Examinations/Show', [
            'examination' => $examination->loadCount(['courses', 'enrollments as enrolled_count' => fn($q) => $q->where('status', 'active')])
        ]);
    }

    public function import(StoreMarkImportRequest $request, Examination $examination): RedirectResponse
    {
        if ($examination->status !== ExaminationStatus::Open) throw new RuntimeException('Marks can only be imported while an examination is open.');
        $file = $request->file('file');
        $path = $file->store('exam-imports');
        $key = (string)$request->header('Idempotency-Key', Str::uuid());
        $import = MarkImport::query()->firstOrCreate(['examination_id' => $examination->id, 'idempotency_key' => $key], ['uuid' => (string)Str::uuid(), 'file_path' => $path, 'file_sha256' => hash_file('sha256', $file->getRealPath()), 'status' => 'queued']);
        if (!$import->wasRecentlyCreated) {
            Storage::delete($path);
        } else {
            PrepareMarksImport::dispatch($import->id)->onQueue('imports');
        }
        return back()->with('import_uuid', $import->uuid);
    }
}
