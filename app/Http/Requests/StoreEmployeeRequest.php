<?php

namespace App\Http\Requests;

use App\Enums\EmployeeRole;
use App\Http\Requests\Concerns\ValidatesEmployeeCpfInput;
use App\Rules\BrazilianCpf;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StoreEmployeeBody',
    required: ['name', 'password', 'cpf', 'position'],
    properties: [
        new OA\Property(property: 'name', description: 'Nome completo (letras, até 120 caracteres)', type: 'string', maxLength: 120, example: 'Maria Silva'),
        new OA\Property(property: 'password', description: 'Mínimo 8 caracteres (regras `Password` do Laravel)', type: 'string', format: 'password', example: 'SenhaSegura!1'),
        new OA\Property(property: 'password_confirmation', description: 'Deve coincidir com `password`', type: 'string', format: 'password'),
        new OA\Property(property: 'cpf', description: '11 dígitos ou com máscara; dígitos verificadores válidos', type: 'string', example: '529.982.247-25'),
        new OA\Property(property: 'position', description: 'Cargo', type: 'string', maxLength: 255, example: 'Atendente'),
        new OA\Property(property: 'role', description: '**Proibido na API** (422). Apenas na criação pela área web admin.', type: 'string', enum: ['admin', 'colaborador'], example: 'colaborador'),
    ]
)]
class StoreEmployeeRequest extends FormRequest
{
    use ValidatesEmployeeCpfInput;

    public function authorize(): bool
    {
        if ($this->is('api/*')) {
            return true;
        }

        return $this->user()?->isAdmin() ?? false;
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
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
            'cpf' => ['required', 'string', 'digits:11', Rule::unique('employees', 'cpf'), new BrazilianCpf],
            'position' => ['required', 'string', 'max:255'],
            'role' => Rule::when(
                $this->is('api/*'),
                ['prohibited'],
                ['required', new Enum(EmployeeRole::class)]
            ),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'password' => 'senha',
            'cpf' => 'CPF',
            'position' => 'cargo',
            'role' => 'perfil',
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

            'password.required' => 'Defina uma senha.',
            'password.confirmed' => 'A confirmação da senha não confere.',

            'cpf.required' => 'Informe o CPF.',
            'cpf.string' => 'O CPF informado é inválido.',
            'cpf.digits' => 'Escreva um CPF válido.',
            'cpf.unique' => 'Este CPF já está cadastrado.',

            'position.required' => 'Informe o cargo ou função.',
            'position.string' => 'O cargo contém caracteres inválidos.',
            'position.max' => 'O cargo não pode ter mais de :max caracteres.',

            'role.required' => 'Selecione o perfil (administrador ou colaborador).',
            'role.prohibited' => 'O perfil não pode ser definido pela API.',
        ];
    }
}
