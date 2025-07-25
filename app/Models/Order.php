<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'order_id',
        'customer_id',
        'order_date',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'order_date' => 'date',
        'total_amount' => 'decimal:2',
        'status' => 'string',
    ];

    /**
     * Relasi ke model Customer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Relasi ke model OrderItem.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Total kuantitas semua item dalam pesanan.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->orderItems()->sum('quantity');
    }

    /**
     * Total harga dihitung ulang dari item pesanan.
     */
    public function getComputedTotalAmountAttribute(): float
    {
        return $this->orderItems()->sum(DB::raw('price * quantity'));
    }
}