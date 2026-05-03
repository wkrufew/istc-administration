<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NormasAprobada extends Model
{
    protected $fillable = ['title', 'description', 'file', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
