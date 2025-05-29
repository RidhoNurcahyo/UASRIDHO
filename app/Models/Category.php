<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'category_id', 'name', 'description'
    ];

    public function products()
    {
        // foreign key di products = category_id, local key di category = category_id
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    // Supaya route model binding menggunakan category_id
    public function getRouteKeyName()
    {
        return 'category_id';
    }
}
