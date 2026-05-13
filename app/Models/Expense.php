<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Expense extends Model
{
    protected $fillable = [
        'expense_name', 'amount', 'category', 'notes', 'expense_date',
    ];

    protected $guarded = [];
}
