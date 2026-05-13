<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class DebtPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'debt_id',
        'paid_amount',
    ];

    // relation: payment → debt
    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }
}