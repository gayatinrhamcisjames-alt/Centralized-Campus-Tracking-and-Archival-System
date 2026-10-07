<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpArticleFeedback extends Model
{
    protected $fillable = ['help_article_id', 'user_id', 'helpful', 'comment'];

    protected function casts(): array
    {
        return ['helpful' => 'boolean'];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(HelpArticle::class, 'help_article_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
