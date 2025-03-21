<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['cost_id', 'date', 'desc', 'amount', 'qty', 'uom', 'total_amount'];

    public function cost()
    {
        return $this->belongsTo(Cost::class);
    }
}
