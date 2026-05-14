<?php

namespace App\Models;

use App\Enums\ClockingType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Clocking extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'employee_id',
        'punched_at',
        'type',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'punched_at' => 'datetime',
            'type' => ClockingType::class,
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
