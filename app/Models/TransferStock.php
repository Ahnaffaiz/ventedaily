<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferStock extends Model
{
    use HasFactory;

    protected $fillable = ['total_items', 'transfer_from', 'transfer_to', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transferProducts()
    {
        return $this->hasMany(TransferProductStock::class);
    }
}
