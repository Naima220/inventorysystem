<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'amount',
        'description',
    ];

    // relation: debt → customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // relation: debt → payments
    public function payments()
    {
        return $this->hasMany(DebtPayment::class);
    }

    // helper: total paid
    public function getTotalPaidAttribute()
{
    return $this->payments->sum('paid_amount');
}

public function getRemainingAttribute()
{
    return $this->amount - $this->total_paid;
}

    
}