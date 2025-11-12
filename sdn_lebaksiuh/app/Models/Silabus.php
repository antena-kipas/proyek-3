<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Silabus extends Model
{
    use HasFactory;

    protected $fillable = [
        'tema',
        'id_tema',
        'subtema',
        'id_subtema',
        'semester',
        'mata_pelajaran_id',
        'kelas',
    ];

    public function kompetensiIntis(): HasMany
    {
        return $this->hasMany(KompetensiInti::class);
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function kompetensiDasars(): HasMany
    {
        return $this->hasMany(KompetensiDasar::class);
    }

    public function indikators(): HasMany
    {
        return $this->hasMany(Indikator::class);
    }
}
