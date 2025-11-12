<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianDiri extends Model
{
    use HasFactory;

    protected $table = 'penilaian_diris';

    protected $fillable = [
        'mata_pelajaran_id',
        'silabus_id',
        'pertanyaan',
        'jawaban',
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
