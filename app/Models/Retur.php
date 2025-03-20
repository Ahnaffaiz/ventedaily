<?php

namespace App\Models;

use App\Enums\ReturStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    use HasFactory;
    protected $fillable = ['sale_id', 'user_id', 'desc', 'total_price', 'total_items', 'no_retur', 'status'];

    protected $casts = [
        'status' => ReturStatus::class,
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function returItems()
    {
        return $this->hasMany(ReturItem::class);
    }
}
