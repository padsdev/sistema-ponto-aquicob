<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClockingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id' => Rule::when(
                $this->user()->isAdmin(),
                ['required', 'integer', Rule::exists('employees', 'id')],
                ['prohibited']
            ),
            'notes' => ['nullable', 'string', 'max:2000'],
            'type' => ['prohibited'],
            'punched_at' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'employee_id' => 'funcionário',
            'notes' => 'observação',
        ];
    }
}
