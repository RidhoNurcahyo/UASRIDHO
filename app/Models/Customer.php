<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // ganti Model jadi Authenticatable kalau perlu login
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'customer_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'customer_id', 'name', 'email', 'password', 'phone', 'address'
    ];

    protected $hidden = [
        'password',
    ];

    // Mutator agar password otomatis di-hash jika belum hashed
    public function setPasswordAttribute($value)
    {
        if ($value && !Hash::needsRehash($value)) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'customer_id');
    }

    // Route model binding menggunakan customer_id
    public function getRouteKeyName()
    {
        return 'customer_id';
    }
}
