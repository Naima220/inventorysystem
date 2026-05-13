<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_sn',
        'name',
        'category',
        'stock',
        'unit_price',
        'sales_unit_price', // ✅ Ensure this exists in DB
         'low_stock_limit'
    ];

    public function category()
{
    return $this->belongsTo(Category::class);
}

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function purchase()
    {
        return $this->belongsTo(SupplierPurchase::class);
    }
}