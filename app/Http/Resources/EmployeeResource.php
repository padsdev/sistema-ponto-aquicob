<?php

namespace App\Http\Resources;

use App\Support\Cpf;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Employee',
    title: 'Funcionário',
    description: 'Recurso devolvido em `data` (objeto ou lista).',
    properties: [
        new OA\Property(property: 'id', description: 'Chave primária', type: 'integer', example: 1),
        new OA\Property(property: 'name', description: 'Nome completo (único na base de dados)', type: 'string', example: 'Maria Silva'),
        new OA\Property(property: 'cpf', description: 'CPF mascarado (xxx.xxx.xxx-xx)', type: 'string', example: '529.982.247-25'),
        new OA\Property(property: 'position', description: 'Cargo ou função', type: 'string', example: 'Atendente'),
        new OA\Property(property: 'role', description: 'Papel: `admin` ou `colaborador`', type: 'string', enum: ['admin', 'colaborador'], example: 'colaborador'),
        new OA\Property(property: 'created_at', description: 'ISO 8601', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', description: 'ISO 8601', type: 'string', format: 'date-time'),
    ]
)]
class EmployeeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cpf' => Cpf::formatMasked((string) ($this->cpf ?? '')),
            'position' => $this->position,
            'role' => $this->role instanceof \BackedEnum ? $this->role->value : $this->role,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
