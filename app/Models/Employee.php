<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['name', 'cpf', 'position'];

    public function clockings()
    {
        return $this->hasMany(Clocking::class);
    }
}
