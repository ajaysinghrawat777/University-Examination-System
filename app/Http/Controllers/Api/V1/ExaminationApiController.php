<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ExaminationStatus;
use App\Http\Controllers\Controller;
use App\Models\Examination;
use App\Jobs\PrepareMarksImport;
use App\Models\MarkImport;
use App\Http\Requests\StoreMarkImportRequest;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class ExaminationApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Examination::query()->latest('id')->paginate(50));
    }
    
    public function import(StoreMarkImportRequest $request, Examination $examination): JsonResponse
    {
        if ($examination->status !== ExaminationStatus::Open) throw new RuntimeException('Examination is not open.');
        $file = $request->file('file');
        $key = (string)$request->header('Idempotency-Key', Str::uuid());
        $path = $file->store('exam-imports');
        $import = MarkImport::query()->firstOrCreate(['examination_id' => $examination->id, 'idempotency_key' => $key], ['uuid' => (string)Str::uuid(), 'file_path' => $path, 'file_sha256' => hash_file('sha256', $file->getRealPath()), 'status' => 'queued']);
        if (!$import->wasRecentlyCreated) {
            \Illuminate\Support\Facades\Storage::delete($path);
        } else {
            PrepareMarksImport::dispatch($import->id)->onQueue('imports');
        }
        return response()->json(['id' => $import->uuid, 'status' => $import->status], 202);
    }
}
