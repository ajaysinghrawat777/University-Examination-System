<?php

namespace App\Http\Controllers;

use App\Models\MarkImport;
use Inertia\Inertia;
use Inertia\Response;

class ImportController extends Controller
{
    public function show(string $uuid): Response
    {
        return Inertia::render('Imports/Show', [
            'importData' => MarkImport::query()->where('uuid', $uuid)->firstOrFail(['id', 'uuid as display_id', 'status', 'total_rows', 'processed_rows', 'failed_rows', 'completed_at'])->toArray()
        ]);
    }
}
