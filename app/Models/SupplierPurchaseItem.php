<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_purchase_id','product_id','cost_price','qty','total_price'
    ];

    public function purchase()
    {
        return $this->belongsTo(SupplierPurchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    // Inside SupplierPurchase model:
public function supplier()
{
    return $this->belongsTo(Supplier::class, 'supplier_name', 'supplier_name');
}

}