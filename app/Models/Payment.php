<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
    'invoice_id',
    'customer_id',
    'product_id',
    'sale_price',
    'qty',
    'total_price',
    'paid',
    'debt',
    'date',
    'payment_date',
    'total_payment',
   
];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function invoice()
{
    return $this->belongsTo(Invoice::class);
}

    protected $guarded = [];
}
