<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpFaq extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'sort_order',
        'is_published',
        'author_id',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
