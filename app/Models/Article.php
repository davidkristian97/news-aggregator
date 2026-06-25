<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function scopeFromSources(Builder $query, array $sourceIds): Builder
    {
        return $query->whereIn('source_id', $sourceIds);
    }

    public function scopeInCategories(Builder $query, array $categoryIds): Builder
    {
        return $query->whereIn('category_id', $categoryIds);
    }

    public function scopeByAuthors(Builder $query, array $authorIds): Builder
    {
        return $query->whereHas('authors', fn ($q) => $q->whereIn('authors.id', $authorIds));
    }

    public function scopePublishedBetween(Builder $query, string $from, string $to): Builder
    {
        if ($from) {
            $query->where('published_at', '>=', $from);
        }
        if ($to) {
            $query->where('published_at', '<=', $to);
        }
        return $query;
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $like = '%'.$term.'%';
        return $query->where(function ($q) use ($like) {
            $q->where('title', 'like', $like)
              ->orWhere('description', 'like', $like);
        });
    }
}
