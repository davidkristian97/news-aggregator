<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreferredAuthor extends Model
{
    protected $fillable = [
        'user_id',
        'author_id'
    ];
}
