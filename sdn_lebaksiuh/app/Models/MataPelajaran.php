<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataPelajaran extends Model
{
    protected $fillable = ['nama_pelajaran', 'silabus_id'];

    public function silabus(): BelongsTo
    {
        return $this->belongsTo(Silabus::class);
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
