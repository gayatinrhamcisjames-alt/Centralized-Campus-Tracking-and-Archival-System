<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThesisDocumentVersion extends Model
{
    use HasFactory;

    protected $fillable = ['thesis_document_id', 'uploaded_by', 'version', 'file_path', 'file_size', 'notes'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(ThesisDocument::class, 'thesis_document_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
