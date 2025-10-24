<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KegiatanInti extends Model
{
    protected $fillable = [
        'rpp_id',
        'kelompok',
        'konten',
        'urutan',
    ];

    public function rpp(): BelongsTo
    {
        return $this->belongsTo(Rpp::class);
    }
}
