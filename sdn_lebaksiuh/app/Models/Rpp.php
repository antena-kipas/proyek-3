<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rpp extends Model
{
    use HasFactory;

    public function muatan_terpadus(): HasMany
    {
        return $this->hasMany(MuatanTerpadu::class);
    }

    public function kegiatan_intis(): HasMany
    {
        return $this->hasMany(KegiatanInti::class);
    }
}