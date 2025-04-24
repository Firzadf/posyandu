<?php

namespace App\Http\Controllers;

use App\Models\IbuHamil;
use App\Models\Posyandu;
use App\Models\PemeriksaanIbuHamil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class IbuHamilController extends Controller
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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $ibuHamils = IbuHamil::where('is_active', true)
            ->when($request->search, function($query) use ($request) {
                return $query->where(function($q) use ($request) {
                    $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                      ->orWhere('nik', 'like', '%' . $request->search . '%')
                      ->orWhere('nama_suami', 'like', '%' . $request->search . '%')
                      ->orWhere('no_hp', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('nama_lengkap')
            ->paginate(10);
        
        return view('ibu-hamil.index', compact('ibuHamils'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $posyandus = Posyandu::all();
        return view('ibu-hamil.create', compact('posyandus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'posyandu_id' => 'required|exists:posyandu,id',
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'required|string|unique:ibu_hamil,nik',
            'tanggal_lahir' => 'required|date',
            'hpht' => 'required|date',
            'golongan_darah' => 'nullable|string|max:5',
            'usia_kehamilan' => 'required|integer|min:0|max:45',
            'kehamilan_ke' => 'required|integer|min:1',
            'tinggi_badan' => 'nullable|numeric|min:0',
            'berat_badan_sebelum_hamil' => 'nullable|numeric|min:0',
            'riwayat_penyakit' => 'nullable|string',
            'nama_suami' => 'nullable|string|max:255',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string',
            'kelurahan' => 'required|string',
            'kecamatan' => 'required|string',
            'kota' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menyimpan data ibu hamil.');
        }

        $data = $request->all();
        
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoName = time() . '_' . $foto->getClientOriginalName();
            $foto->storeAs('public/ibu-hamil', $fotoName);
            $data['foto'] = $fotoName;
        }
        
        IbuHamil::create($data);
        
        return redirect()->route('ibu-hamil.index')
            ->with('success', 'Data ibu hamil berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(IbuHamil $ibuHamil)
    {
        $pemeriksaan = PemeriksaanIbuHamil::where('ibu_hamil_id', $ibuHamil->id)
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->paginate(5);
            
        // Data untuk grafik pemantauan kehamilan
        $data_pemeriksaan = PemeriksaanIbuHamil::where('ibu_hamil_id', $ibuHamil->id)
            ->orderBy('tanggal_pemeriksaan', 'asc')
            ->get();
            
        $labels = $data_pemeriksaan->pluck('tanggal_pemeriksaan')->map(function($tanggal) {
            return Carbon::parse($tanggal)->format('d/m/Y');
        });
        
        $berat_badan = $data_pemeriksaan->pluck('berat_badan');
        $tekanan_sistolik = $data_pemeriksaan->pluck('tekanan_darah_sistolik');
        $tekanan_diastolik = $data_pemeriksaan->pluck('tekanan_darah_diastolik');
        $tinggi_fundus = $data_pemeriksaan->pluck('tinggi_fundus');
        
        return view('ibu-hamil.show', compact(
            'ibuHamil', 
            'pemeriksaan', 
            'labels', 
            'berat_badan', 
            'tekanan_sistolik', 
            'tekanan_diastolik',
            'tinggi_fundus'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IbuHamil $ibuHamil)
    {
        $posyandus = Posyandu::all();
        return view('ibu-hamil.edit', compact('ibuHamil', 'posyandus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IbuHamil $ibuHamil)
    {
        $validator = Validator::make($request->all(), [
            'posyandu_id' => 'required|exists:posyandu,id',
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'required|string|unique:ibu_hamil,nik,' . $ibuHamil->id,
            'tanggal_lahir' => 'required|date',
            'hpht' => 'required|date',
            'golongan_darah' => 'nullable|string|max:5',
            'usia_kehamilan' => 'required|integer|min:0|max:45',
            'kehamilan_ke' => 'required|integer|min:1',
            'tinggi_badan' => 'nullable|numeric|min:0',
            'berat_badan_sebelum_hamil' => 'nullable|numeric|min:0',
            'riwayat_penyakit' => 'nullable|string',
            'nama_suami' => 'nullable|string|max:255',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string',
            'kelurahan' => 'required|string',
            'kecamatan' => 'required|string',
            'kota' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui data ibu hamil.');
        }

        $data = $request->all();
        
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($ibuHamil->foto) {
                Storage::delete('public/ibu-hamil/' . $ibuHamil->foto);
            }
            
            $foto = $request->file('foto');
            $fotoName = time() . '_' . $foto->getClientOriginalName();
            $foto->storeAs('public/ibu-hamil', $fotoName);
            $data['foto'] = $fotoName;
        }
        
        $ibuHamil->update($data);
        
        return redirect()->route('ibu-hamil.show', $ibuHamil)
            ->with('success', 'Data ibu hamil berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IbuHamil $ibuHamil)
    {
        // Soft delete dengan mengubah is_active menjadi false
        $ibuHamil->update(['is_active' => false]);
        
        return redirect()->route('ibu-hamil.index')
            ->with('success', 'Data ibu hamil berhasil dihapus.');
    }
    
    /**
     * Show pemeriksaan form
     */
    public function createPemeriksaan(IbuHamil $ibuHamil)
    {
        return view('ibu-hamil.pemeriksaan.create', compact('ibuHamil'));
    }
    
    /**
     * Store pemeriksaan
     */
    public function storePemeriksaan(Request $request, IbuHamil $ibuHamil)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_pemeriksaan' => 'required|date',
            'usia_kehamilan' => 'required|integer|min:1|max:45',
            'berat_badan' => 'required|numeric|min:0',
            'tekanan_darah_sistolik' => 'required|integer|min:50|max:250',
            'tekanan_darah_diastolik' => 'required|integer|min:30|max:150',
            'tinggi_fundus' => 'nullable|integer|min:0',
            'denyut_jantung_janin' => 'nullable|string',
            'keluhan' => 'nullable|string',
            'tindakan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menyimpan data pemeriksaan.');
        }

        $data = $request->all();
        $data['ibu_hamil_id'] = $ibuHamil->id;
        $data['user_id'] = auth()->id();
        
        // Hitung status gizi
        $pemeriksaan = new PemeriksaanIbuHamil($data);
        $pemeriksaan->ibuHamil = $ibuHamil;
        $data['status_gizi'] = $pemeriksaan->hitungStatusGizi();
        
        // Periksa risiko kehamilan
        $data['resiko_kehamilan'] = $pemeriksaan->isRisikoTinggi() ? 'Risiko Tinggi' : 'Normal';
        
        PemeriksaanIbuHamil::create($data);
        
        return redirect()->route('ibu-hamil.show', $ibuHamil)
            ->with('success', 'Data pemeriksaan ibu hamil berhasil ditambahkan.');
    }
    
    /**
     * Export to PDF
     */
    public function exportPDF(IbuHamil $ibuHamil)
    {
        $pemeriksaan = PemeriksaanIbuHamil::where('ibu_hamil_id', $ibuHamil->id)
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->get();
        
        $pdf = PDF::loadView('ibu-hamil.pdf', compact('ibuHamil', 'pemeriksaan'));
        
        return $pdf->download('data-ibu-hamil-' . $ibuHamil->nama_lengkap . '.pdf');
    }
    
    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        $date = Carbon::now()->format('Y-m-d');
        return Excel::download(new IbuHamilExport, 'data-ibu-hamil-' . $date . '.xlsx');
    }
}