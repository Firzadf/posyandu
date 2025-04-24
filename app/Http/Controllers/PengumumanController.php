<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PengumumanController extends Controller
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
        $pengumumans = Pengumuman::query()
            ->when($request->search, function($query) use ($request) {
                return $query->where(function($q) use ($request) {
                    $q->where('judul', 'like', '%' . $request->search . '%')
                      ->orWhere('isi', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('pengumuman.index', compact('pengumumans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $posyandus = Posyandu::all();
        return view('pengumuman.create', compact('posyandus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'posyandu_id' => 'required|exists:posyandu,id',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'is_active' => 'boolean',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menyimpan pengumuman.');
        }

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['is_active'] = $request->has('is_active');
        
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $gambarName = time() . '_' . $gambar->getClientOriginalName();
            $gambar->storeAs('public/pengumuman', $gambarName);
            $data['gambar'] = $gambarName;
        }
        
        Pengumuman::create($data);
        
        return redirect()->route('pengumuman.index')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengumuman $pengumuman)
    {
        return view('pengumuman.show', compact('pengumuman'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pengumuman $pengumuman)
    {
        $posyandus = Posyandu::all();
        return view('pengumuman.edit', compact('pengumuman', 'posyandus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validator = Validator::make($request->all(), [
            'posyandu_id' => 'required|exists:posyandu,id',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'is_active' => 'boolean',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui pengumuman.');
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($pengumuman->gambar) {
                Storage::delete('public/pengumuman/' . $pengumuman->gambar);
            }
            
            $gambar = $request->file('gambar');
            $gambarName = time() . '_' . $gambar->getClientOriginalName();
            $gambar->storeAs('public/pengumuman', $gambarName);
            $data['gambar'] = $gambarName;
        }
        
        $pengumuman->update($data);
        
        return redirect()->route('pengumuman.show', $pengumuman)
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengumuman $pengumuman)
    {
        // Hapus gambar jika ada
        if ($pengumuman->gambar) {
            Storage::delete('public/pengumuman/' . $pengumuman->gambar);
        }
        
        $pengumuman->delete();
        
        return redirect()->route('pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
    
    /**
     * Toggle status pengumuman
     */
    public function toggleStatus(Pengumuman $pengumuman)
    {
        $pengumuman->update(['is_active' => !$pengumuman->is_active]);
        
        $status = $pengumuman->is_active ? 'aktif' : 'tidak aktif';
        
        return redirect()->back()
            ->with('success', "Pengumuman berhasil diubah menjadi $status.");
    }
}