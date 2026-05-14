<?php

namespace App\Models;

use App\Support\Cpf;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'cpf', 'position'];

    /**
     * CPF armazenado somente com dígitos; exibição com máscara em telas.
     */
    protected function cpfFormatted(): Attribute
    {
        return Attribute::get(fn (): string => Cpf::formatMasked((string) ($this->attributes['cpf'] ?? '')));
    }

    public function clockings()
    {
        return $this->hasMany(Clocking::class);
    }
}
