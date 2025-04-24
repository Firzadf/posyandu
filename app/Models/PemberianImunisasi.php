<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemberianImunisasi extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pemberian_imunisasi';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'balita_id',
        'imunisasi_id',
        'tanggal_pemberian',
        'no_batch',
        'catatan',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_pemberian' => 'date',
    ];

    /**
     * Get the balita that owns the pemberian imunisasi.
     */
    public function balita()
    {
        return $this->belongsTo(Balita::class);
    }

    /**
     * Get the imunisasi that owns the pemberian imunisasi.
     */
    public function imunisasi()
    {
        return $this->belongsTo(Imunisasi::class);
    }

    /**
     * Get the user that performed the pemberian imunisasi.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hitung usia balita saat pemberian imunisasi dalam bulan.
     *
     * @return int
     */
    public function usiaBalitaSaatImunisasi()
    {
        return $this->balita->tanggal_lahir->diffInMonths($this->tanggal_pemberian);
    }

    /**
     * Check if imunisasi was given on time.
     *
     * @return bool
     */
    public function tepatWaktu()
    {
        $usia_imunisasi = $this->imunisasi->usia_pemberian;
        $usia_balita = $this->usiaBalitaSaatImunisasi();
        
        // Untuk imunisasi saat lahir (0 bulan), toleransi 1 bulan
        if ($usia_imunisasi == 0) {
            return $usia_balita <= 1;
        }
        
        // Untuk imunisasi lainnya, toleransi 2 bulan dari jadwal
        return $usia_balita <= ($usia_imunisasi + 2);
    }
}