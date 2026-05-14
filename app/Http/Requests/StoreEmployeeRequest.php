<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesEmployeeCpfInput;
use App\Rules\BrazilianCpf;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StoreEmployeeBody',
    required: ['name', 'cpf', 'position'],
    properties: [
        new OA\Property(property: 'name', description: 'Nome completo (letras, até 120 caracteres)', type: 'string', maxLength: 120, example: 'Maria Silva'),
        new OA\Property(property: 'cpf', description: 'CPF com 11 dígitos (com ou sem máscara na API)', type: 'string', example: '529.982.247-25'),
        new OA\Property(property: 'position', description: 'Cargo', type: 'string', maxLength: 255, example: 'Atendente'),
    ]
)]
class StoreEmployeeRequest extends FormRequest
{
    use ValidatesEmployeeCpfInput;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->prepareEmployeeCpfFromRequest();
    }

    public function withValidator(Validator $validator): void
    {
        $this->withCpfPresentationValidator($validator);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', 'regex:/^[\p{L}\p{M}\s\-\'.]+$/u', Rule::unique('employees', 'name')],
            'cpf' => ['required', 'string', 'digits:11', Rule::unique('employees', 'cpf'), new BrazilianCpf],
            'position' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'cpf' => 'CPF',
            'position' => 'cargo',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Informe o nome completo.',
            'name.string' => 'O nome contém caracteres inválidos.',
            'name.max' => 'O nome não pode ter mais de :max caracteres.',
            'name.regex' => 'Use apenas letras (incluindo acentos), espaços, hífen ou apóstrofo no nome.',
            'name.unique' => 'Este nome já está cadastrado.',

            'cpf.required' => 'Informe o CPF.',
            'cpf.string' => 'O CPF informado é inválido.',
            'cpf.digits' => 'Escreva um CPF válido.',
            'cpf.unique' => 'Este CPF já está cadastrado.',

            'position.required' => 'Informe o cargo ou função.',
            'position.string' => 'O cargo contém caracteres inválidos.',
            'position.max' => 'O cargo não pode ter mais de :max caracteres.',
        ];
    }
}
