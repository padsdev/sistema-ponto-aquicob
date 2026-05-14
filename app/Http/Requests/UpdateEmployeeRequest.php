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
    schema: 'UpdateEmployeeBody',
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 120, example: 'Maria Silva Santos'),
        new OA\Property(property: 'cpf', type: 'string', example: '529.982.247-25'),
        new OA\Property(property: 'position', type: 'string', maxLength: 255, example: 'Supervisora'),
    ]
)]
class UpdateEmployeeRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:120', 'regex:/^[\p{L}\p{M}\s\-\'.]+$/u', Rule::unique('employees', 'name')->ignore($this->route('employee'))],
            'cpf' => ['sometimes', 'required', 'string', 'digits:11', Rule::unique('employees', 'cpf')->ignore($this->route('employee')), new BrazilianCpf],
            'position' => ['sometimes', 'required', 'string', 'max:255'],
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
