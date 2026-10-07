<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'thesis_id', 'action', 'ip_address', 'metadata'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public static function record(string $action, ?Thesis $thesis = null, array $metadata = [], ?Request $request = null): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'thesis_id' => $thesis?->id,
            'action' => $action,
            'ip_address' => ($request ?? request())->ip(),
            'metadata' => $metadata ?: null,
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }
}
