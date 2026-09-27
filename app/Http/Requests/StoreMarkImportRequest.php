<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreMarkImportRequest extends FormRequest
{
    // public function authorize(): bool
    // {
    //     return $this->user() !== null;
    // }
    public function rules(): array
    {
        return ['file' => ['required', 'file', File::types(['csv'])->max('250mb')]];
    }
}
