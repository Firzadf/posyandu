<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'no_hp',
        'alamat',
        'foto',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Check if user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is kader
     *
     * @return bool
     */
    public function isKader()
    {
        return $this->role === 'kader';
    }

    /**
     * Check if user is bidan
     *
     * @return bool
     */
    public function isBidan()
    {
        return $this->role === 'bidan';
    }

    /**
     * Get the pemeriksaan balita that belong to the user.
     */
    public function pemeriksaanBalita()
    {
        return $this->hasMany(PemeriksaanBalita::class);
    }

    /**
     * Get the pemeriksaan ibu hamil that belong to the user.
     */
    public function pemeriksaanIbuHamil()
    {
        return $this->hasMany(PemeriksaanIbuHamil::class);
    }

    /**
     * Get the pemberian imunisasi that belong to the user.
     */
    public function pemberianImunisasi()
    {
        return $this->hasMany(PemberianImunisasi::class);
    }

    /**
     * Get the pemberian vitamin that belong to the user.
     */
    public function pemberianVitamin()
    {
        return $this->hasMany(PemberianVitamin::class);
    }

    /**
     * Get the jadwal kegiatan that belong to the user.
     */
    public function jadwalKegiatan()
    {
        return $this->hasMany(JadwalKegiatan::class);
    }

    /**
     * Get the pengumuman that belong to the user.
     */
    public function pengumuman()
    {
        return $this->hasMany(Pengumuman::class);
    }
}