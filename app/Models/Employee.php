<?php

namespace App\Models;

use App\Enums\ClockingType;
use App\Enums\EmployeeRole;
use App\Support\Cpf;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'cpf',
        'position',
        'password',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => EmployeeRole::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === EmployeeRole::Admin;
    }

    /**
     * CPF armazenado somente com dígitos; exibição com máscara em telas.
     */
    protected function cpfFormatted(): Attribute
    {
        return Attribute::get(fn (): string => Cpf::formatMasked((string) ($this->attributes['cpf'] ?? '')));
    }

    public function clockings(): HasMany
    {
        return $this->hasMany(Clocking::class);
    }

    public function latestClocking(): HasOne
    {
        return $this->hasOne(Clocking::class)->latestOfMany(['punched_at', 'id']);
    }

    public function nextClockingType(): ClockingType
    {
        $last = $this->latestClocking;

        return match ($last?->type) {
            null, ClockingType::Saida => ClockingType::Entrada,
            ClockingType::Entrada => ClockingType::Saida,
        };
    }
}
