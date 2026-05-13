<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
    'order_id',
    'product_id',
    'product_sn',
    'quantity',
    'sale_price',
    'total_price',
 
];


    // Relationship with Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected $guarded = [];
}
