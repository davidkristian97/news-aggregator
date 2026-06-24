<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreferredSource extends Model
{
    protected $fillable = [
        'user_id',
        'source_id'
    ];
}
