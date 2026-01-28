<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeasurementUnit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'symbol',
    ];

    /**
     * Get the items for the measurement unit.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}