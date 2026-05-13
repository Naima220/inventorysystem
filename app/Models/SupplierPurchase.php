<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        't_cost','discount','sup_cost','paid','balance','supplier_name','supplier_phone',
    ];

    public function items()
    {
        return $this->hasMany(SupplierPurchaseItem::class);
    }
}