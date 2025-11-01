<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rpp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'semester',
        'pembelajaran_ke',
        'tema_id',
        'tema_name',
        'sub_tema_id',
        'sub_tema_name',
    ];

    public function muatan_terpadus(): HasMany
    {
        return $this->hasMany(MuatanTerpadu::class);
    }

    public function kegiatan_intis(): HasMany
    {
        return $this->hasMany(KegiatanInti::class);
    }

    public function tujuanPembelajarans(): HasMany
    {
        return $this->hasMany(TujuanPembelajaran::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}