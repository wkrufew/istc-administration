<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Convenio extends Model
{
    protected $fillable = ['title', 'description', 'url', 'file', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
