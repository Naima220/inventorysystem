<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Invoice extends Model
{
   protected $fillable = [
    'customer_id',
    'customer_name',
    'customer_phone',
    'debt',
    'total_amount',
];
 protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function invoiceItems()
{
    return $this->hasMany(InvoiceItem::class);
}

    protected $guarded = [];

}
