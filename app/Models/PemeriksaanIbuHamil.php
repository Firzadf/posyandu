<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanIbuHamil extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pemeriksaan_ibu_hamil';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ibu_hamil_id',
        'tanggal_pemeriksaan',
        'usia_kehamilan',
        'berat_badan',
        'tekanan_darah_sistolik',
        'tekanan_darah_diastolik',
        'tinggi_fundus',
        'denyut_jantung_janin',
        'status_gizi',
        'resiko_kehamilan',
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
    ];

    /**
     * Get the ibu hamil that owns the pemeriksaan.
     */
    public function ibuHamil()
    {
        return $this->belongsTo(IbuHamil::class);
    }

    /**
     * Get the user that performed the pemeriksaan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tekanan darah lengkap.
     *
     * @return string
     */
    public function tekananDarah()
    {
        return "{$this->tekanan_darah_sistolik}/{$this->tekanan_darah_diastolik} mmHg";
    }

    /**
     * Hitung status gizi ibu hamil berdasarkan IMT (Indeks Massa Tubuh).
     *
     * @return string
     */
    public function hitungStatusGizi()
    {
        // Perlu tinggi badan dari data ibu hamil
        $tinggi_badan = (float) $this->ibuHamil->tinggi_badan / 100; // konversi ke meter
        
        // Jika tidak ada data tinggi badan
        if (!$tinggi_badan) {
            return null;
        }
        
        // Hitung IMT: BB(kg) / (TB(m)^2)
        $imt = $this->berat_badan / ($tinggi_badan * $tinggi_badan);
        
        // Klasifikasi IMT untuk ibu hamil
        if ($imt < 18.5) {
            return 'Kurang (KEK)';
        } elseif ($imt >= 18.5 && $imt < 25) {
            return 'Normal';
        } elseif ($imt >= 25 && $imt < 30) {
            return 'Berlebih';
        } else {
            return 'Obesitas';
        }
    }

    /**
     * Periksa apakah tekanan darah normal.
     *
     * @return bool
     */
    public function isTekananDarahNormal()
    {
        // Tekanan darah normal: sistolik < 140 dan diastolik < 90
        return $this->tekanan_darah_sistolik < 140 && $this->tekanan_darah_diastolik < 90;
    }

    /**
     * Periksa apakah termasuk risiko tinggi.
     *
     * @return bool
     */
    public function isRisikoTinggi()
    {
        // Cek beberapa faktor risiko tinggi
        $risikoUsia = $this->ibuHamil->risikoTinggiUsia();
        $risikoTekananDarah = !$this->isTekananDarahNormal();
        $risikoGizi = $this->status_gizi === 'Kurang (KEK)' || $this->status_gizi === 'Obesitas';
        
        return $risikoUsia || $risikoTekananDarah || $risikoGizi;
    }
}