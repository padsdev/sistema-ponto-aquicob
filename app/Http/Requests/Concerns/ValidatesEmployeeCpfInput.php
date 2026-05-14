<?php

namespace App\Http\Requests\Concerns;

use App\Support\Cpf;
use Illuminate\Validation\Validator;

trait ValidatesEmployeeCpfInput
{
    protected function prepareEmployeeCpfFromRequest(): void
    {
        if (! $this->has('cpf')) {
            return;
        }

        $raw = trim((string) $this->input('cpf'));
        $this->merge([
            'cpf_raw' => $raw,
            'cpf' => Cpf::digitsOnly($raw),
        ]);
    }

    public function withCpfPresentationValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            if ($v->errors()->has('cpf')) {
                return;
            }

            if ($this->is('api/*')) {
                return;
            }

            $raw = trim((string) $this->input('cpf_raw', ''));
            if ($raw === '') {
                return;
            }

            if (! preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $raw)) {
                $v->errors()->add('cpf', 'Escreva um CPF válido.');
            }
        });
    }
}
