<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\Posyandu;
use App\Models\PemeriksaanBalita;
use App\Models\Imunisasi;
use App\Models\Vitamin;
use App\Models\PemberianImunisasi;
use App\Models\PemberianVitamin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BalitaController extends Controller
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
        $balitas = Balita::where('is_active', true)
            ->when($request->search, function($query) use ($request) {
                return $query->where(function($q) use ($request) {
                    $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                      ->orWhere('nik', 'like', '%' . $request->search . '%')
                      ->orWhere('nama_ibu', 'like', '%' . $request->search . '%')
                      ->orWhere('nama_ayah', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('nama_lengkap')
            ->paginate(10);
        
        return view('balita.index', compact('balitas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $posyandus = Posyandu::all();
        return view('balita.create', compact('posyandus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'posyandu_id' => 'required|exists:posyandu,id',
            'nama_lengkap' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'nik' => 'required|string|unique:balita,nik',
            'no_kk' => 'required|string',
            'nama_ayah' => 'required|string|max:255',
            'nama_ibu' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kelurahan' => 'required|string',
            'kecamatan' => 'required|string',
            'kota' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menyimpan data balita.');
        }

        $data = $request->all();
        
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoName = time() . '_' . $foto->getClientOriginalName();
            $foto->storeAs('public/balita', $fotoName);
            $data['foto'] = $fotoName;
        }
        
        Balita::create($data);
        
        return redirect()->route('balita.index')
            ->with('success', 'Data balita berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Balita $balita)
    {
        $pemeriksaan = PemeriksaanBalita::where('balita_id', $balita->id)
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->paginate(5);
        
        $imunisasi = PemberianImunisasi::where('balita_id', $balita->id)
            ->with('imunisasi')
            ->orderBy('tanggal_pemberian', 'desc')
            ->get();
            
        $vitamin = PemberianVitamin::where('balita_id', $balita->id)
            ->with('vitamin')
            ->orderBy('tanggal_pemberian', 'desc')
            ->get();
            
        // Data untuk grafik pertumbuhan
        $data_pemeriksaan = PemeriksaanBalita::where('balita_id', $balita->id)
            ->orderBy('tanggal_pemeriksaan', 'asc')
            ->get();
            
        $labels = $data_pemeriksaan->pluck('tanggal_pemeriksaan')->map(function($tanggal) {
            return Carbon::parse($tanggal)->format('d/m/Y');
        });
        
        $berat_badan = $data_pemeriksaan->pluck('berat_badan');
        $tinggi_badan = $data_pemeriksaan->pluck('tinggi_badan');
        $lingkar_kepala = $data_pemeriksaan->pluck('lingkar_kepala');
        
        return view('balita.show', compact(
            'balita', 
            'pemeriksaan', 
            'imunisasi', 
            'vitamin', 
            'labels', 
            'berat_badan', 
            'tinggi_badan', 
            'lingkar_kepala'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Balita $balita)
    {
        $posyandus = Posyandu::all();
        return view('balita.edit', compact('balita', 'posyandus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Balita $balita)
    {
        $validator = Validator::make($request->all(), [
            'posyandu_id' => 'required|exists:posyandu,id',
            'nama_lengkap' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'nik' => 'required|string|unique:balita,nik,' . $balita->id,
            'no_kk' => 'required|string',
            'nama_ayah' => 'required|string|max:255',
            'nama_ibu' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kelurahan' => 'required|string',
            'kecamatan' => 'required|string',
            'kota' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui data balita.');
        }

        $data = $request->all();
        
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($balita->foto) {
                Storage::delete('public/balita/' . $balita->foto);
            }
            
            $foto = $request->file('foto');
            $fotoName = time() . '_' . $foto->getClientOriginalName();
            $foto->storeAs('public/balita', $fotoName);
            $data['foto'] = $fotoName;
        }
        
        $balita->update($data);
        
        return redirect()->route('balita.show', $balita)
            ->with('success', 'Data balita berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Balita $balita)
    {
        // Soft delete dengan mengubah is_active menjadi false
        $balita->update(['is_active' => false]);
        
        return redirect()->route('balita.index')
            ->with('success', 'Data balita berhasil dihapus.');
    }
    
    /**
     * Show pemeriksaan form
     */
    public function createPemeriksaan(Balita $balita)
    {
        return view('balita.pemeriksaan.create', compact('balita'));
    }
    
    /**
     * Store pemeriksaan
     */
    public function storePemeriksaan(Request $request, Balita $balita)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_pemeriksaan' => 'required|date',
            'berat_badan' => 'required|numeric|min:0',
            'tinggi_badan' => 'required|numeric|min:0',
            'lingkar_kepala' => 'nullable|numeric|min:0',
            'lingkar_lengan' => 'nullable|numeric|min:0',
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
        $data['balita_id'] = $balita->id;
        $data['user_id'] = auth()->id();
        
        // Hitung status gizi
        $pemeriksaan = new PemeriksaanBalita($data);
        $data['status_gizi'] = $pemeriksaan->hitungStatusGiziBBU();
        
        PemeriksaanBalita::create($data);
        
        return redirect()->route('balita.show', $balita)
            ->with('success', 'Data pemeriksaan balita berhasil ditambahkan.');
    }
    
    /**
     * Show imunisasi form
     */
    public function createImunisasi(Balita $balita)
    {
        $imunisasis = Imunisasi::where('is_active', true)->get();
        return view('balita.imunisasi.create', compact('balita', 'imunisasis'));
    }
    
    /**
     * Store imunisasi
     */
    public function storeImunisasi(Request $request, Balita $balita)
    {
        $validator = Validator::make($request->all(), [
            'imunisasi_id' => 'required|exists:imunisasi,id',
            'tanggal_pemberian' => 'required|date',
            'no_batch' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menyimpan data imunisasi.');
        }

        $data = $request->all();
        $data['balita_id'] = $balita->id;
        $data['user_id'] = auth()->id();
        
        // Cek apakah imunisasi sudah pernah diberikan
        $existingImunisasi = PemberianImunisasi::where('balita_id', $balita->id)
            ->where('imunisasi_id', $request->imunisasi_id)
            ->first();
            
        if ($existingImunisasi) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Imunisasi ini sudah pernah diberikan pada balita ini.');
        }
        
        PemberianImunisasi::create($data);
        
        return redirect()->route('balita.show', $balita)
            ->with('success', 'Data imunisasi balita berhasil ditambahkan.');
    }
    
    /**
     * Show vitamin form
     */
    public function createVitamin(Balita $balita)
    {
        $vitamins = Vitamin::where('is_active', true)->get();
        return view('balita.vitamin.create', compact('balita', 'vitamins'));
    }
    
    /**
     * Store vitamin
     */
    public function storeVitamin(Request $request, Balita $balita)
    {
        $validator = Validator::make($request->all(), [
            'vitamin_id' => 'required|exists:vitamin,id',
            'tanggal_pemberian' => 'required|date',
            'no_batch' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menyimpan data vitamin.');
        }

        $data = $request->all();
        $data['balita_id'] = $balita->id;
        $data['user_id'] = auth()->id();
        
        PemberianVitamin::create($data);
        
        return redirect()->route('balita.show', $balita)
            ->with('success', 'Data pemberian vitamin balita berhasil ditambahkan.');
    }
    
    /**
     * Export to PDF
     */
    public function exportPDF(Balita $balita)
    {
        $pemeriksaan = PemeriksaanBalita::where('balita_id', $balita->id)
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->get();
        
        $imunisasi = PemberianImunisasi::where('balita_id', $balita->id)
            ->with('imunisasi')
            ->orderBy('tanggal_pemberian', 'desc')
            ->get();
            
        $vitamin = PemberianVitamin::where('balita_id', $balita->id)
            ->with('vitamin')
            ->orderBy('tanggal_pemberian', 'desc')
            ->get();
        
        $pdf = PDF::loadView('balita.pdf', compact('balita', 'pemeriksaan', 'imunisasi', 'vitamin'));
        
        return $pdf->download('data-balita-' . $balita->nama_lengkap . '.pdf');
    }
    
    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        $date = Carbon::now()->format('Y-m-d');
        return Excel::download(new BalitaExport, 'data-balita-' . $date . '.xlsx');
    }
}