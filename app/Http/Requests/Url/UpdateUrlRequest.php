<?php

namespace App\Http\Requests\Url;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'original_url' => ['sometimes', 'url', 'max:2048'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}
