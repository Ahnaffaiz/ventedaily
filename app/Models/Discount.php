<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'value', 'discount_type'];

    protected $casts = [
        'discount_type' => 'enum:%,rupiah',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}