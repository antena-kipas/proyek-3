<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KompetensiDasar extends Model
{
    protected $fillable = ['mata_pelajaran_id', 'deskripsi_kd', 'silabus_id', 'mata_pelajaran_id'];

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function silabus(): BelongsTo
    {
        return $this->belongsTo(Silabus::class);
    }
}
