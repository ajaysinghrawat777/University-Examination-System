<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ExaminationStatus;
use App\Http\Controllers\Controller;
use App\Jobs\RecalculateStudentResult;
use App\Models\Examination;
use App\Services\ResultPublicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class ResultApiController extends Controller
{
    public function results(Examination $examination): JsonResponse
    {
        if ($examination->status !== ExaminationStatus::Published) throw new RuntimeException('Results are not published.');
        return response()->json($examination->examResults()->with('student')->paginate(100));
    }
    public function calculate(Examination $examination): JsonResponse
    {
        if (!in_array($examination->status, [ExaminationStatus::Open, ExaminationStatus::Processing], true)) throw new RuntimeException('Examination cannot be calculated in its current state.');
        $examination->update(['status' => ExaminationStatus::Processing]);
        $ids = $examination->enrollments()->where('status', 'active')->pluck('student_id');
        foreach ($ids->chunk(1000) as $chunk) {
            foreach ($chunk as $id) RecalculateStudentResult::dispatch($examination->id, $id)->onQueue('results');
        }
        return response()->json(['status' => 'processing', 'students_queued' => $ids->count()]);
    }
    public function publish(Examination $examination, ResultPublicationService $publisher): JsonResponse
    {
        $pub = $publisher->publish($examination, (int)auth()->id());
        return response()->json($pub, 201);
    }
}
