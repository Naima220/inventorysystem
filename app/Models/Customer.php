<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name', 'email', 'phone',  'address', 'gender' 
    ];

    // Relationship: A customer has many payments
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function orders()
{
    return $this->hasMany(Order::class);
}

    protected $guarded = [];
}
