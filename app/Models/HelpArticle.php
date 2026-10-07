<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HelpArticle extends Model
{
    protected $fillable = [
        'help_category_id', 'author_id', 'title', 'slug', 'excerpt', 'content',
        'role_scope', 'reading_time', 'views', 'is_published', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'role_scope' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(HelpCategory::class, 'help_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(HelpArticleFeedback::class, 'help_article_id');
    }
}
