<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryPreview extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
        'error',
    ];
}
