<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'user_id',
        'type',
        'quantity',
        'notes',
        'transaction_date',
    ];

    /**
     * Get the item that the transaction belongs to.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the user that performed the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
