<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rpp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mata_pelajaran',
        'topik_materi',
        'alokasi_waktu',
        'tujuan_1',
        'tujuan_2',
        'generated_json_data',
        'file_path',
    ];

    protected $casts = [
        'generated_json_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
