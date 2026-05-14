<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clocking extends Model
{
    protected $fillable = ['employee_id', 'punched_at', 'type'];

    protected $casts = [
        'punched_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
