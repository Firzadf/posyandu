<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\PemberianImunisasi;
use App\Models\PemberianVitamin;
use App\Models\JadwalKegiatan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Tampilkan halaman pilihan laporan.
     */
    public function index()
    {
        return view('laporan.index');
    }
    
    /**
     * Laporan balita.
     */
    public function balita(Request $request)
    {
        $request->validate([
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'usia_min' => 'nullable|integer|min:0',
            'usia_max' => 'nullable|integer|min:0',
            'dari_tanggal' => 'nullable|date',
            'sampai_tanggal' => 'nullable|date|after_or_equal:dari_tanggal',
        ]);
        
        $jenis_kelamin = $request->jenis_kelamin;
        $usia_min = $request->usia_min;
        $usia_max = $request->usia_max;
        $dari_tanggal = $request->dari_tanggal;
        $sampai_tanggal = $request->sampai_tanggal;
        
        $balitas = Balita::where('is_active', true)
            ->when($jenis_kelamin, function($q) use ($jenis_kelamin) {
                return $q->where('jenis_kelamin', $jenis_kelamin);
            })
            ->when($dari_tanggal, function($q) use ($dari_tanggal) {
                return $q->where('created_at', '>=', $dari_tanggal);
            })
            ->when($sampai_tanggal, function($q) use ($sampai_tanggal) {
                return $q->where('created_at', '<=', $sampai_tanggal . ' 23:59:59');
            })
            ->get()
            ->when($usia_min, function($collection) use ($usia_min) {
                return $collection->filter(function($balita) use ($usia_min) {
                    return $balita->usiaBulan() >= $usia_min;
                });
            })
            ->when($usia_max, function($collection) use ($usia_max) {
                return $collection->filter(function($balita) use ($usia_max) {
                    return $balita->usiaBulan() <= $usia_max;
                });
            });
        
        if ($request->output === 'pdf') {
            $pdf = PDF::loadView('laporan.balita_pdf', [
                'balitas' => $balitas, 
                'filter' => [
                    'jenis_kelamin' => $jenis_kelamin,
                    'usia_min' => $usia_min,
                    'usia_max' => $usia_max,
                    'dari_tanggal' => $dari_tanggal,
                    'sampai_tanggal' => $sampai_tanggal,
                ]
            ]);
            
            return $pdf->download('laporan-balita-' . Carbon::now()->format('Y-m-d') . '.pdf');
        }
        
        return view('laporan.balita', [
            'balitas' => $balitas, 
            'filter' => [
                'jenis_kelamin' => $jenis_kelamin,
                'usia_min' => $usia_min,
                'usia_max' => $usia_max,
                'dari_tanggal' => $dari_tanggal,
                'sampai_tanggal' => $sampai_tanggal,
            ]
        ]);
    }
    
    /**
     * Laporan ibu hamil.
     */
    public function ibuHamil(Request $request)
    {
        $request->validate([
            'usia_min' => 'nullable|integer|min:0',
            'usia_max' => 'nullable|integer|min:0',
            'usia_kehamilan_min' => 'nullable|integer|min:0',
            'usia_kehamilan_max' => 'nullable|integer|min:0',
            'dari_tanggal' => 'nullable|date',
            'sampai_tanggal' => 'nullable|date|after_or_equal:dari_tanggal',
        ]);
        
        $usia_min = $request->usia_min;
        $usia_max = $request->usia_max;
        $usia_kehamilan_min = $request->usia_kehamilan_min;
        $usia_kehamilan_max = $request->usia_kehamilan_max;
        $dari_tanggal = $request->dari_tanggal;
        $sampai_tanggal = $request->sampai_tanggal;
        
        $ibuHamils = IbuHamil::where('is_active', true)
            ->when($usia_kehamilan_min, function($q) use ($usia_kehamilan_min) {
                return $q->where('usia_kehamilan', '>=', $usia_kehamilan_min);
            })
            ->when($usia_kehamilan_max, function($q) use ($usia_kehamilan_max) {
                return $q->where('usia_kehamilan', '<=', $usia_kehamilan_max);
            })
            ->when($dari_tanggal, function($q) use ($dari_tanggal) {
                return $q->where('created_at', '>=', $dari_tanggal);
            })
            ->when($sampai_tanggal, function($q) use ($sampai_tanggal) {
                return $q->where('created_at', '<=', $sampai_tanggal . ' 23:59:59');
            })
            ->get()
            ->when($usia_min, function($collection) use ($usia_min) {
                return $collection->filter(function($ibuHamil) use ($usia_min) {
                    return $ibuHamil->usiaTahun() >= $usia_min;
                });
            })
            ->when($usia_max, function($collection) use ($usia_max) {
                return $collection->filter(function($ibuHamil) use ($usia_max) {
                    return $ibuHamil->usiaTahun() <= $usia_max;
                });
            });
        
        if ($request->output === 'pdf') {
            $pdf = PDF::loadView('laporan.ibu_hamil_pdf', [
                'ibuHamils' => $ibuHamils, 
                'filter' => [
                    'usia_min' => $usia_min,
                    'usia_max' => $usia_max,
                    'usia_kehamilan_min' => $usia_kehamilan_min,
                    'usia_kehamilan_max' => $usia_kehamilan_max,
                    'dari_tanggal' => $dari_tanggal,
                    'sampai_tanggal' => $sampai_tanggal,
                ]
            ]);
            
            return $pdf->download('laporan-ibu-hamil-' . Carbon::now()->format('Y-m-d') . '.pdf');
        }
        
        return view('laporan.ibu_hamil', [
            'ibuHamils' => $ibuHamils, 
            'filter' => [
                'usia_min' => $usia_min,
                'usia_max' => $usia_max,
                'usia_kehamilan_min' => $usia_kehamilan_min,
                'usia_kehamilan_max' => $usia_kehamilan_max,
                'dari_tanggal' => $dari_tanggal,
                'sampai_tanggal' => $sampai_tanggal,
            ]
        ]);
    }
    
    /**
     * Laporan pemeriksaan balita.
     */
    public function pemeriksaanBalita(Request $request)
    {
        $request->validate([
            'dari_tanggal' => 'nullable|date',
            'sampai_tanggal' => 'nullable|date|after_or_equal:dari_tanggal',
            'status_gizi' => 'nullable|string',
        ]);
        
        $dari_tanggal = $request->dari_tanggal ?? Carbon::now()->startOfMonth()->toDateString();
        $sampai_tanggal = $request->sampai_tanggal ?? Carbon::now()->endOfMonth()->toDateString();
        $status_gizi = $request->status_gizi;
        
        $pemeriksaanBalitas = PemeriksaanBalita::with('balita', 'user')
            ->whereBetween('tanggal_pemeriksaan', [$dari_tanggal, $sampai_tanggal])
            ->when($status_gizi, function($q) use ($status_gizi) {
                return $q->where('status_gizi', 'like', '%' . $status_gizi . '%');
            })
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->get();
            
        // Hitung statistik
        $totalPemeriksaan = $pemeriksaanBalitas->count();
        $giziBalita = $pemeriksaanBalitas->groupBy('status_gizi')
            ->map(function($group) {
                return count($group);
            });
        
        if ($request->output === 'pdf') {
            $pdf = PDF::loadView('laporan.pemeriksaan_balita_pdf', [
                'pemeriksaanBalitas' => $pemeriksaanBalitas, 
                'filter' => [
                    'dari_tanggal' => $dari_tanggal,
                    'sampai_tanggal' => $sampai_tanggal,
                    'status_gizi' => $status_gizi,
                ],
                'totalPemeriksaan' => $totalPemeriksaan,
                'giziBalita' => $giziBalita,
            ]);
            
            return $pdf->download('laporan-pemeriksaan-balita-' . Carbon::now()->format('Y-m-d') . '.pdf');
        }
        
        return view('laporan.pemeriksaan_balita', [
            'pemeriksaanBalitas' => $pemeriksaanBalitas, 
            'filter' => [
                'dari_tanggal' => $dari_tanggal,
                'sampai_tanggal' => $sampai_tanggal,
                'status_gizi' => $status_gizi,
            ],
            'totalPemeriksaan' => $totalPemeriksaan,
            'giziBalita' => $giziBalita,
        ]);
    }
    
    /**
     * Laporan pemeriksaan ibu hamil.
     */
    public function pemeriksaanIbuHamil(Request $request)
    {
        $request->validate([
            'dari_tanggal' => 'nullable|date',
            'sampai_tanggal' => 'nullable|date|after_or_equal:dari_tanggal',
            'resiko_kehamilan' => 'nullable|string',
        ]);
        
        $dari_tanggal = $request->dari_tanggal ?? Carbon::now()->startOfMonth()->toDateString();
        $sampai_tanggal = $request->sampai_tanggal ?? Carbon::now()->endOfMonth()->toDateString();
        $resiko_kehamilan = $request->resiko_kehamilan;
        
        $pemeriksaanIbuHamils = PemeriksaanIbuHamil::with('ibuHamil', 'user')
            ->whereBetween('tanggal_pemeriksaan', [$dari_tanggal, $sampai_tanggal])
            ->when($resiko_kehamilan, function($q) use ($resiko_kehamilan) {
                return $q->where('resiko_kehamilan', $resiko_kehamilan);
            })
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->get();
            
        // Hitung statistik
        $totalPemeriksaan = $pemeriksaanIbuHamils->count();
        $resikoIbuHamil = $pemeriksaanIbuHamils->groupBy('resiko_kehamilan')
            ->map(function($group) {
                return count($group);
            });
        
        if ($request->output === 'pdf') {
            $pdf = PDF::loadView('laporan.pemeriksaan_ibu_hamil_pdf', [
                'pemeriksaanIbuHamils' => $pemeriksaanIbuHamils, 
                'filter' => [
                    'dari_tanggal' => $dari_tanggal,
                    'sampai_tanggal' => $sampai_tanggal,
                    'resiko_kehamilan' => $resiko_kehamilan,
                ],
                'totalPemeriksaan' => $totalPemeriksaan,
                'resikoIbuHamil' => $resikoIbuHamil,
            ]);
            
            return $pdf->download('laporan-pemeriksaan-ibu-hamil-' . Carbon::now()->format('Y-m-d') . '.pdf');
        }
        
        return view('laporan.pemeriksaan_ibu_hamil', [
            'pemeriksaanIbuHamils' => $pemeriksaanIbuHamils, 
            'filter' => [
                'dari_tanggal' => $dari_tanggal,
                'sampai_tanggal' => $sampai_tanggal,
                'resiko_kehamilan' => $resiko_kehamilan,
            ],
            'totalPemeriksaan' => $totalPemeriksaan,
            'resikoIbuHamil' => $resikoIbuHamil,
        ]);
    }
    
    /**
     * Laporan imunisasi.
     */
    public function imunisasi(Request $request)
    {
        $request->validate([
            'dari_tanggal' => 'nullable|date',
            'sampai_tanggal' => 'nullable|date|after_or_equal:dari_tanggal',
            'imunisasi_id' => 'nullable|exists:imunisasi,id',
        ]);
        
        $dari_tanggal = $request->dari_tanggal ?? Carbon::now()->startOfMonth()->toDateString();
        $sampai_tanggal = $request->sampai_tanggal ?? Carbon::now()->endOfMonth()->toDateString();
        $imunisasi_id = $request->imunisasi_id;
        
        $pemberianImunisasis = PemberianImunisasi::with('balita', 'imunisasi', 'user')
            ->whereBetween('tanggal_pemberian', [$dari_tanggal, $sampai_tanggal])
            ->when($imunisasi_id, function($q) use ($imunisasi_id) {
                return $q->where('imunisasi_id', $imunisasi_id);
            })
            ->orderBy('tanggal_pemberian', 'desc')
            ->get();
            
        // Hitung statistik
        $totalPemberian = $pemberianImunisasis->count();
        $jenisImunisasi = $pemberianImunisasis->groupBy('imunisasi.nama_imunisasi')
            ->map(function($group) {
                return count($group);
            });
        
        // Get list imunisasi for filter
        $imunisasis = \App\Models\Imunisasi::where('is_active', true)->get();
        
        if ($request->output === 'pdf') {
            $pdf = PDF::loadView('laporan.imunisasi_pdf', [
                'pemberianImunisasis' => $pemberianImunisasis, 
                'filter' => [
                    'dari_tanggal' => $dari_tanggal,
                    'sampai_tanggal' => $sampai_tanggal,
                    'imunisasi_id' => $imunisasi_id,
                ],
                'totalPemberian' => $totalPemberian,
                'jenisImunisasi' => $jenisImunisasi,
            ]);
            
            return $pdf->download('laporan-imunisasi-' . Carbon::now()->format('Y-m-d') . '.pdf');
        }
        
        return view('laporan.imunisasi', [
            'pemberianImunisasis' => $pemberianImunisasis, 
            'filter' => [
                'dari_tanggal' => $dari_tanggal,
                'sampai_tanggal' => $sampai_tanggal,
                'imunisasi_id' => $imunisasi_id,
            ],
            'totalPemberian' => $totalPemberian,
            'jenisImunisasi' => $jenisImunisasi,
            'imunisasis' => $imunisasis,
        ]);
    }
    
    /**
     * Laporan vitamin.
     */
    public function vitamin(Request $request)
    {
        $request->validate([
            'dari_tanggal' => 'nullable|date',
            'sampai_tanggal' => 'nullable|date|after_or_equal:dari_tanggal',
            'vitamin_id' => 'nullable|exists:vitamin,id',
        ]);
        
        $dari_tanggal = $request->dari_tanggal ?? Carbon::now()->startOfMonth()->toDateString();
        $sampai_tanggal = $request->sampai_tanggal ?? Carbon::now()->endOfMonth()->toDateString();
        $vitamin_id = $request->vitamin_id;
        
        $pemberianVitamins = PemberianVitamin::with('balita', 'vitamin', 'user')
            ->whereBetween('tanggal_pemberian', [$dari_tanggal, $sampai_tanggal])
            ->when($vitamin_id, function($q) use ($vitamin_id) {
                return $q->where('vitamin_id', $vitamin_id);
            })
            ->orderBy('tanggal_pemberian', 'desc')
            ->get();
            
        // Hitung statistik
        $totalPemberian = $pemberianVitamins->count();
        $jenisVitamin = $pemberianVitamins->groupBy('vitamin.nama_vitamin')
            ->map(function($group) {
                return count($group);
            });
        
        // Get list vitamin for filter
        $vitamins = \App\Models\Vitamin::where('is_active', true)->get();
        
        if ($request->output === 'pdf') {
            $pdf = PDF::loadView('laporan.vitamin_pdf', [
                'pemberianVitamins' => $pemberianVitamins, 
                'filter' => [
                    'dari_tanggal' => $dari_tanggal,
                    'sampai_tanggal' => $sampai_tanggal,
                    'vitamin_id' => $vitamin_id,
                ],
                'totalPemberian' => $totalPemberian,
                'jenisVitamin' => $jenisVitamin,
            ]);
            
            return $pdf->download('laporan-vitamin-' . Carbon::now()->format('Y-m-d') . '.pdf');
        }
        
        return view('laporan.vitamin', [
            'pemberianVitamins' => $pemberianVitamins, 
            'filter' => [
                'dari_tanggal' => $dari_tanggal,
                'sampai_tanggal' => $sampai_tanggal,
                'vitamin_id' => $vitamin_id,
            ],
            'totalPemberian' => $totalPemberian,
            'jenisVitamin' => $jenisVitamin,
            'vitamins' => $vitamins,
        ]);
    }
    
    /**
     * Laporan kegiatan.
     */
    public function kegiatan(Request $request)
    {
        $request->validate([
            'dari_tanggal' => 'nullable|date',
            'sampai_tanggal' => 'nullable|date|after_or_equal:dari_tanggal',
            'status' => 'nullable|in:Terjadwal,Berlangsung,Selesai,Dibatalkan',
        ]);
        
        $dari_tanggal = $request->dari_tanggal ?? Carbon::now()->startOfMonth()->toDateString();
        $sampai_tanggal = $request->sampai_tanggal ?? Carbon::now()->endOfMonth()->toDateString();
        $status = $request->status;
        
        $kegiatans = JadwalKegiatan::with('posyandu', 'user')
            ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal])
            ->when($status, function($q) use ($status) {
                return $q->where('status', $status);
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu_mulai', 'desc')
            ->get();
            
        // Hitung statistik
        $totalKegiatan = $kegiatans->count();
        $statusKegiatan = $kegiatans->groupBy('status')
            ->map(function($group) {
                return count($group);
            });
        
        if ($request->output === 'pdf') {
            $pdf = PDF::loadView('laporan.kegiatan_pdf', [
                'kegiatans' => $kegiatans, 
                'filter' => [
                    'dari_tanggal' => $dari_tanggal,
                    'sampai_tanggal' => $sampai_tanggal,
                    'status' => $status,
                ],
                'totalKegiatan' => $totalKegiatan,
                'statusKegiatan' => $statusKegiatan,
            ]);
            
            return $pdf->download('laporan-kegiatan-' . Carbon::now()->format('Y-m-d') . '.pdf');
        }
        
        return view('laporan.kegiatan', [
            'kegiatans' => $kegiatans, 
            'filter' => [
                'dari_tanggal' => $dari_tanggal,
                'sampai_tanggal' => $sampai_tanggal,
                'status' => $status,
            ],
            'totalKegiatan' => $totalKegiatan,
            'statusKegiatan' => $statusKegiatan,
        ]);
    }
}