<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posyandu';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_posyandu',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'kode_pos',
        'telepon',
        'email',
        'deskripsi',
        'logo',
    ];

    /**
     * Get the balita for the posyandu.
     */
    public function balita()
    {
        return $this->hasMany(Balita::class);
    }

    /**
     * Get the ibu hamil for the posyandu.
     */
    public function ibuHamil()
    {
        return $this->hasMany(IbuHamil::class);
    }

    /**
     * Get the jadwal kegiatan for the posyandu.
     */
    public function jadwalKegiatan()
    {
        return $this->hasMany(JadwalKegiatan::class);
    }

    /**
     * Get the pengumuman for the posyandu.
     */
    public function pengumuman()
    {
        return $this->hasMany(Pengumuman::class);
    }

    /**
     * Get the alamat lengkap posyandu.
     *
     * @return string
     */
    public function alamatLengkap()
    {
        return "{$this->alamat}, {$this->kelurahan}, {$this->kecamatan}, {$this->kota}";
    }
}