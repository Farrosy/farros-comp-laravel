<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'processor',
        'ram_gb',
        'storage',
        'battery_condition',
        'physical_condition',
        'price',
        'stock',
        'image',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
