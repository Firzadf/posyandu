<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\JadwalKegiatan;
use App\Models\Pengumuman;
use App\Models\Posyandu;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get counts
        $totalBalita = Balita::where('is_active', true)->count();
        $totalIbuHamil = IbuHamil::where('is_active', true)->count();
        
        // Get pemeriksaan counts for current month
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        $pemeriksaanBalitaBulanIni = PemeriksaanBalita::whereBetween('tanggal_pemeriksaan', [$startOfMonth, $endOfMonth])->count();
        $pemeriksaanIbuHamilBulanIni = PemeriksaanIbuHamil::whereBetween('tanggal_pemeriksaan', [$startOfMonth, $endOfMonth])->count();
        
        // Get upcoming kegiatan
        $jadwalKegiatan = JadwalKegiatan::upcoming()->limit(5)->get();
        
        // Get active pengumuman
        $pengumuman = Pengumuman::active()->orderBy('created_at', 'desc')->limit(5)->get();
        
        // Get data for grafik pertumbuhan balita (last 6 months)
        $start = $now->copy()->subMonths(5)->startOfMonth();
        $labels = [];
        $dataBalitaGiziKurang = [];
        $dataBalitaGiziBaik = [];
        $dataBalitaGiziLebih = [];
        
        for ($i = 0; $i < 6; $i++) {
            $currentStart = $start->copy()->addMonths($i)->startOfMonth();
            $currentEnd = $start->copy()->addMonths($i)->endOfMonth();
            $monthLabel = $currentStart->locale('id')->translatedFormat('M Y');
            $labels[] = $monthLabel;
            
            // Count balita by status gizi
            $giziKurang = PemeriksaanBalita::whereBetween('tanggal_pemeriksaan', [$currentStart, $currentEnd])
                ->where('status_gizi', 'like', '%Kurang%')
                ->count();
                
            $giziBaik = PemeriksaanBalita::whereBetween('tanggal_pemeriksaan', [$currentStart, $currentEnd])
                ->where('status_gizi', 'like', '%Baik%')
                ->count();
                
            $giziLebih = PemeriksaanBalita::whereBetween('tanggal_pemeriksaan', [$currentStart, $currentEnd])
                ->where('status_gizi', 'like', '%Lebih%')
                ->count();
            
            $dataBalitaGiziKurang[] = $giziKurang;
            $dataBalitaGiziBaik[] = $giziBaik;
            $dataBalitaGiziLebih[] = $giziLebih;
        }
        
        // Get data for grafik kunjungan
        $dataKunjungan = [];
        
        for ($i = 0; $i < 6; $i++) {
            $currentStart = $start->copy()->addMonths($i)->startOfMonth();
            $currentEnd = $start->copy()->addMonths($i)->endOfMonth();
            
            $kunjunganBalita = PemeriksaanBalita::whereBetween('tanggal_pemeriksaan', [$currentStart, $currentEnd])
                ->count();
                
            $kunjunganIbuHamil = PemeriksaanIbuHamil::whereBetween('tanggal_pemeriksaan', [$currentStart, $currentEnd])
                ->count();
            
            $dataKunjungan['balita'][] = $kunjunganBalita;
            $dataKunjungan['ibu_hamil'][] = $kunjunganIbuHamil;
        }
        
        // Get data posyandu
        $posyandu = Posyandu::first();
        
        return view('dashboard', compact(
            'totalBalita', 
            'totalIbuHamil', 
            'pemeriksaanBalitaBulanIni', 
            'pemeriksaanIbuHamilBulanIni', 
            'jadwalKegiatan', 
            'pengumuman',
            'labels',
            'dataBalitaGiziKurang',
            'dataBalitaGiziBaik',
            'dataBalitaGiziLebih',
            'dataKunjungan',
            'posyandu'
        ));
    }
}