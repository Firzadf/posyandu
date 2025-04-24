<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class IbuHamil extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ibu_hamil';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'posyandu_id',
        'nama_lengkap',
        'nik',
        'tanggal_lahir',
        'hpht',
        'hpl',
        'golongan_darah',
        'usia_kehamilan',
        'kehamilan_ke',
        'tinggi_badan',
        'berat_badan_sebelum_hamil',
        'riwayat_penyakit',
        'nama_suami',
        'no_hp',
        'alamat',
        'rt_rw',
        'kelurahan',
        'kecamatan',
        'kota',
        'foto',
        'catatan',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'hpht' => 'date',
        'hpl' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the posyandu that owns the ibu hamil.
     */
    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class);
    }

    /**
     * Get the pemeriksaan for the ibu hamil.
     */
    public function pemeriksaan()
    {
        return $this->hasMany(PemeriksaanIbuHamil::class);
    }

    /**
     * Get the pemeriksaan terakhir for the ibu hamil.
     */
    public function pemeriksaanTerakhir()
    {
        return $this->hasOne(PemeriksaanIbuHamil::class)->latest('tanggal_pemeriksaan');
    }

    /**
     * Set the HPHT (Hari Pertama Haid Terakhir) dan hitung HPL (Hari Perkiraan Lahir).
     *
     * @param  string  $value
     * @return void
     */
    public function setHphtAttribute($value)
    {
        $this->attributes['hpht'] = $value;
        // Hitung HPL menggunakan rumus Naegele (HPHT + 7 hari - 3 bulan + 1 tahun)
        $hpht = Carbon::parse($value);
        $this->attributes['hpl'] = $hpht->copy()->addDays(7)->subMonths(3)->addYear()->toDateString();
    }

    /**
     * Get the usia kehamilan saat ini dalam minggu.
     *
     * @return int
     */
    public function usiaKehamilanSaatIni()
    {
        return Carbon::parse($this->hpht)->diffInWeeks(Carbon::now());
    }

    /**
     * Get the usia ibu hamil dalam tahun.
     *
     * @return int
     */
    public function usiaTahun()
    {
        return Carbon::parse($this->tanggal_lahir)->age;
    }

    /**
     * Get the trimester kehamilan.
     *
     * @return int
     */
    public function trimester()
    {
        $usia = $this->usiaKehamilanSaatIni();
        
        if ($usia < 13) {
            return 1;
        } elseif ($usia < 28) {
            return 2;
        } else {
            return 3;
        }
    }

    /**
     * Get the alamat lengkap ibu hamil.
     *
     * @return string
     */
    public function alamatLengkap()
    {
        $rt_rw = $this->rt_rw ? "RT/RW {$this->rt_rw}, " : '';
        return "{$this->alamat}, {$rt_rw}{$this->kelurahan}, {$this->kecamatan}, {$this->kota}";
    }

    /**
     * Apakah ibu hamil berisiko tinggi berdasarkan usia.
     *
     * @return bool
     */
    public function risikoTinggiUsia()
    {
        $usia = $this->usiaTahun();
        return $usia < 20 || $usia > 35;
    }
}