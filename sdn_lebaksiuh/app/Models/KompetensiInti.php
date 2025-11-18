<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KompetensiInti extends Model
{
    use HasFactory;

    protected $fillable = [
        'silabus_id',
        'kompetensi_inti',
    ];

    public function silabus(): BelongsTo
    {
        return $this->belongsTo(Silabus::class);
    }
}
