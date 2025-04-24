<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pengumuman extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pengumuman';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'posyandu_id',
        'judul',
        'isi',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
        'gambar',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the posyandu that owns the pengumuman.
     */
    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class);
    }

    /**
     * Get the user that created the pengumuman.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active pengumuman.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('tanggal_mulai', '<=', Carbon::today())
                     ->where(function($q) {
                         $q->whereNull('tanggal_selesai')
                           ->orWhere('tanggal_selesai', '>=', Carbon::today());
                     });
    }

    /**
     * Get the formatted dates.
     *
     * @return string
     */
    public function periodFormatted()
    {
        if ($this->tanggal_selesai) {
            return $this->tanggal_mulai->locale('id')->translatedFormat('d F Y') . ' - ' . 
                   $this->tanggal_selesai->locale('id')->translatedFormat('d F Y');
        }
        
        return 'Mulai ' . $this->tanggal_mulai->locale('id')->translatedFormat('d F Y');
    }
}