<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KegiatanPembelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'silabus_id',
        'kegiatan_pembelajaran',
    ];

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function silabus(): BelongsTo
    {
        return $this->belongsTo(Silabus::class);
    }
}
