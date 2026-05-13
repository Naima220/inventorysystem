<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PaymentItem extends Model
{
    use HasFactory;

    // Haddii aad rabto inaad magac kale u bixiso table-ka, halkan ku sheeg
    // protected $table = 'payment_items';

    protected $fillable = [
        'payment_id',
        'product_id',
        'sale_price',
        'qty',
        'total_price',
      
    ];

    // ✅ PaymentItem wuxuu ku xiran yahay Payment
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    // ✅ PaymentItem wuxuu ku xiran yahay Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected $guarded = [];
}