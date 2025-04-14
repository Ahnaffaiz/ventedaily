<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPreview extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'group_id', 'address', 'error'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
