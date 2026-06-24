<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $fillable = [
        'name'
    ];

    public function articles() {
        return $this->hasMany(Article::class);
    }

    public function users() {
        return $this->belongsToMany(User::class, 'user_preferred_sources');
    }
}
