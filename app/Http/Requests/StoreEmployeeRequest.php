<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StoreEmployeeBody',
    required: ['name', 'cpf', 'position'],
    properties: [
        new OA\Property(property: 'name', description: 'Nome completo', type: 'string', maxLength: 255, example: 'Maria Silva'),
        new OA\Property(property: 'cpf', description: 'CPF com 11 dígitos (somente números)', type: 'string', pattern: '^[0-9]{11}$', example: '52998224725'),
        new OA\Property(property: 'position', description: 'Cargo', type: 'string', maxLength: 255, example: 'Atendente'),
    ]
)]
class StoreEmployeeRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'digits:11', 'regex:/^[0-9]{11}$/', 'unique:employees,cpf'],
            'position' => ['required', 'string', 'max:255'],
        ];
    }
}
