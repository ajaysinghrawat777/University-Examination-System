<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use App\Jobs\PrepareMarksImport;
use App\Models\Examination;
use App\Models\User;
use App\Enums\ExaminationStatus;

class MarkImportTest extends TestCase
{
    use RefreshDatabase;
    public function test_import_is_idempotent_by_exam_and_idempotency_key(): void
    {
        $user = User::factory()->create();
        $exam = Examination::create(['code' => 'EX-001', 'name' => 'Semester 1', 'academic_year' => '2026', 'term' => 'May', 'status' => ExaminationStatus::Open]);
        Queue::fake();
        $csv = "student_admission_no,course_code,assessment_component_code,marks\nSTU1,MATH101,IA1,18\n";
        $file = UploadedFile::fake()->createWithContent('marks.csv', $csv);
        $headers = ['Idempotency-Key' => 'same-key'];
        $this->actingAs($user)->post(route('examinations.import', $exam), ['file' => $file], $headers);
        $file2 = UploadedFile::fake()->createWithContent('marks.csv', $csv);
        $this->actingAs($user)->post(route('examinations.import', $exam), ['file' => $file2], $headers);
        $this->assertDatabaseCount('mark_imports', 1);
        Queue::assertPushed(PrepareMarksImport::class, 1);
    }
}
