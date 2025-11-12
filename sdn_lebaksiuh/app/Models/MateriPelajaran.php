<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MateriPelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'mata_pelajaran_id',
        'silabus_id',
        'judul',
        'konten',
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
