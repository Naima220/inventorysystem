<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Salary extends Model
{
    protected $fillable = ['employee_id', 'month', 'amount', 'payment_date', 'note',];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    protected $guarded = [];
}
