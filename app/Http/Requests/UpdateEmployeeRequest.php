<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateEmployeeBody',
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Maria Silva Santos'),
        new OA\Property(property: 'cpf', type: 'string', pattern: '^[0-9]{11}$', example: '52998224725'),
        new OA\Property(property: 'position', type: 'string', maxLength: 255, example: 'Supervisora'),
    ]
)]
class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'cpf' => ['sometimes', 'required', 'string', 'digits:11', 'regex:/^[0-9]{11}$/', Rule::unique('employees', 'cpf')->ignore($this->route('employee'))],
            'position' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }
}
