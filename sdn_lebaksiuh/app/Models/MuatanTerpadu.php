<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MuatanTerpadu extends Model
{
    use HasFactory;

    protected $fillable = ['mata_pelajaran'];

    public function rpp(): BelongsTo
    {
        return $this->belongsTo(Rpp::class);
    }
}