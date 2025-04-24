<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanBalita extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pemeriksaan_balita';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'balita_id',
        'tanggal_pemeriksaan',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'lingkar_lengan',
        'status_gizi',
        'keluhan',
        'tindakan',
        'catatan',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'berat_badan' => 'float',
        'tinggi_badan' => 'float',
        'lingkar_kepala' => 'float',
        'lingkar_lengan' => 'float',
    ];

    /**
     * Get the balita that owns the pemeriksaan.
     */
    public function balita()
    {
        return $this->belongsTo(Balita::class);
    }

    /**
     * Get the user that performed the pemeriksaan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hitung usia balita saat pemeriksaan dalam bulan.
     *
     * @return int
     */
    public function usiaBalitaSaatPemeriksaan()
    {
        return $this->balita->tanggal_lahir->diffInMonths($this->tanggal_pemeriksaan);
    }

    /**
     * Hitung status gizi berdasarkan Berat Badan menurut Umur (BB/U).
     *
     * @return string
     */
    public function hitungStatusGiziBBU()
    {
        // Implementasi perhitungan status gizi berdasarkan standar WHO atau Kemenkes
        // Ini adalah contoh sederhana, pada implementasi nyata perlu data referensi yang lebih lengkap
        
        $usia = $this->usiaBalitaSaatPemeriksaan();
        $bb = $this->berat_badan;
        $jk = $this->balita->jenis_kelamin;
        
        // Contoh sederhana (perlu diganti dengan standar WHO/Kemenkes yang valid)
        if ($jk == 'Laki-laki') {
            if ($usia <= 6) {
                if ($bb < 5.5) return 'Gizi Kurang';
                if ($bb <= 8.5) return 'Gizi Baik';
                return 'Gizi Lebih';
            } else if ($usia <= 12) {
                if ($bb < 7.0) return 'Gizi Kurang';
                if ($bb <= 10.5) return 'Gizi Baik';
                return 'Gizi Lebih';
            } else if ($usia <= 24) {
                if ($bb < 9.0) return 'Gizi Kurang';
                if ($bb <= 13.0) return 'Gizi Baik';
                return 'Gizi Lebih';
            } else if ($usia <= 36) {
                if ($bb < 11.0) return 'Gizi Kurang';
                if ($bb <= 15.0) return 'Gizi Baik';
                return 'Gizi Lebih';
            } else {
                if ($bb < 13.0) return 'Gizi Kurang';
                if ($bb <= 17.0) return 'Gizi Baik';
                return 'Gizi Lebih';
            }
        } else { // Perempuan
            if ($usia <= 6) {
                if ($bb < 5.0) return 'Gizi Kurang';
                if ($bb <= 8.0) return 'Gizi Baik';
                return 'Gizi Lebih';
            } else if ($usia <= 12) {
                if ($bb < 6.5) return 'Gizi Kurang';
                if ($bb <= 9.5) return 'Gizi Baik';
                return 'Gizi Lebih';
            } else if ($usia <= 24) {
                if ($bb < 8.5) return 'Gizi Kurang';
                if ($bb <= 12.5) return 'Gizi Baik';
                return 'Gizi Lebih';
            } else if ($usia <= 36) {
                if ($bb < 10.5) return 'Gizi Kurang';
                if ($bb <= 14.5) return 'Gizi Baik';
                return 'Gizi Lebih';
            } else {
                if ($bb < 12.5) return 'Gizi Kurang';
                if ($bb <= 16.5) return 'Gizi Baik';
                return 'Gizi Lebih';
            }
        }
    }
}