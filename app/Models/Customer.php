<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'group_id', 'address'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function keeps()
    {
        return $this->hasMany(Keep::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}