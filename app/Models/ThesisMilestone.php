<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThesisMilestone extends Model
{
    use HasFactory;

    protected $fillable = ['thesis_id', 'name', 'description', 'due_date', 'completed_at', 'status'];

    protected function casts(): array
    {
        return ['due_date' => 'date', 'completed_at' => 'date'];
    }

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }
}
