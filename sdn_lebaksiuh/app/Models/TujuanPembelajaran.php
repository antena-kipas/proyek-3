<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TujuanPembelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'rpp_id',
        'urutan',
        'tujuan_pembelajaran',
    ];

    public function rpp(): BelongsTo
    {
        return $this->belongsTo(Rpp::class);
    }
}