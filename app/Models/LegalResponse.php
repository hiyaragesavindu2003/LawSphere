<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'legal_request_id',
        'lawyer_id',
        'response_text',
    ];

    public function legalRequest(): BelongsTo
    {
        return $this->belongsTo(LegalRequest::class);
    }

    public function lawyer(): BelongsTo
    {
        return $this->belongsTo(Lawyer::class);
    }
}
