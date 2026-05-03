<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ocsa extends Model
{
    protected $fillable = ['title', 'description', 'file', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
