<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';
    public $incrementing = false;
    protected $keyType = 'string'; // Gunakan 'int' jika bukan UUID

    protected $fillable = [
        'order_id',
        'customer_id',
        'order_date',
        'total_amount',
        'status'
    ];

    /**
     * Relasi ke Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Relasi ke OrderItem
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Total kuantitas semua item dalam pesanan
     */
    public function getTotalQuantityAttribute()
    {
        return $this->orderItems->sum('quantity');
    }

    /**
     * Total harga (jika ingin dihitung ulang dari item)
     */
    public function getComputedTotalAmountAttribute()
    {
        return $this->orderItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }
}
