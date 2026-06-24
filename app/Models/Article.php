<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'source_id',
        'category_id',
        'title',
        'description',
        'url',
        'published_at'
    ];
    
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function source() {
        return $this->belongsTo(Source::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function authors() {
        return $this->belongsToMany(Author::class, 'article_authors');
    }
}
