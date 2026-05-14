<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'cpf', 'position'];

    public function clockings()
    {
        return $this->hasMany(Clocking::class);
    }
}
