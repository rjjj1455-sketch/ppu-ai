<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayananPublik extends Model
{
    protected $table = 'layanan_publik';

    protected $fillable = [
        'nomor',
        'keluhan',
        'solusi',
        'dinas',
        'link',
        'instagram',
        'validation_status',
        'validation_note',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Status label & warna badge
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->validation_status) {
            'valid'         => 'Valid',
            'revisi'        => 'Revisi',
            'salah_mapping' => 'Salah Mapping',
            default         => 'Pending',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->validation_status) {
            'valid'         => 'green',
            'revisi'        => 'yellow',
            'salah_mapping' => 'red',
            default         => 'gray',
        };
    }

    /**
     * Scope: search keluhan / solusi / dinas
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('keluhan', 'like', "%{$keyword}%")
              ->orWhere('solusi', 'like', "%{$keyword}%")
              ->orWhere('dinas', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope: filter dinas
     */
    public function scopeFilterDinas($query, $dinas)
    {
        return $query->where('dinas', $dinas);
    }
}