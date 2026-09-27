<?php
namespace App\Http\Controllers\Api\V1; use App\Http\Controllers\Controller; use App\Models\MarkImport; use Illuminate\Http\JsonResponse;
class ImportApiController extends Controller { public function show(string $uuid):JsonResponse {return response()->json(MarkImport::where('uuid',$uuid)->firstOrFail());} }
