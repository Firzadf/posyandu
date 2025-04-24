<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JadwalKegiatan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jadwal_kegiatan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'posyandu_id',
        'nama_kegiatan',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'tempat',
        'deskripsi',
        'status',
        'is_pengumuman',
        'kirim_pengingat',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'is_pengumuman' => 'boolean',
        'kirim_pengingat' => 'boolean',
    ];

    /**
     * Get the posyandu that owns the jadwal kegiatan.
     */
    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class);
    }

    /**
     * Get the user that created the jadwal kegiatan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the formatted date.
     *
     * @return string
     */
    public function tanggalFormatted()
    {
        return $this->tanggal->locale('id')->translatedFormat('l, d F Y');
    }

    /**
     * Get the formatted time.
     *
     * @return string
     */
    public function waktuFormatted()
    {
        return Carbon::parse($this->waktu_mulai)->format('H:i') . ' - ' . 
               ($this->waktu_selesai ? Carbon::parse($this->waktu_selesai)->format('H:i') : 'Selesai');
    }

    /**
     * Scope a query to only include upcoming kegiatan.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('tanggal', '>=', Carbon::today())
                     ->where('status', '!=', 'Dibatalkan')
                     ->orderBy('tanggal', 'asc')
                     ->orderBy('waktu_mulai', 'asc');
    }

    /**
     * Scope a query to only include past kegiatan.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePast($query)
    {
        return $query->where(function($q) {
                        $q->where('tanggal', '<', Carbon::today())
                          ->orWhere('status', 'Selesai');
                     })
                     ->orderBy('tanggal', 'desc');
    }

    /**
     * Get the status badge class.
     *
     * @return string
     */
    public function statusBadgeClass()
    {
        switch ($this->status) {
            case 'Terjadwal':
                return 'badge-info';
            case 'Berlangsung':
                return 'badge-primary';
            case 'Selesai':
                return 'badge-success';
            case 'Dibatalkan':
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }
}