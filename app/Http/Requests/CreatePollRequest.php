<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePollRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array {
        return [
            'title' => 'required|string|min:5|max:100',
            'options' => 'required|array|min:2|max:4',
            'options.*' => 'required|string|min:1|max:50',
        ];
    }
}
