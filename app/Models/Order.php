<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'customer_name',
        'customer_phone',
        'order_status',
        
    ];

    // Relationship with Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // One Order has many Order Items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // One Order can have one Invoice
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    protected $guarded = [];
}
