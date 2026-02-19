<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayananPublik extends Model
{
    protected $table = 'layanan_publik'; // Nama tabel di database
    protected $primaryKey = 'nomor';    // Sesuai migration Anda

    protected $fillable = [
        'keluhan',
        'solusi',
        'dinas',
        'link',
        'instagram'
    ];
}