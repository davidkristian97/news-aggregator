<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = [
        'name'
    ];

    public function articles() {
        return $this->belongsToMany(Article::class, 'article_authors');
    }

    public function users() {
        return $this->belongsToMany(User::class, 'user_preferred_authors');
    }
}
