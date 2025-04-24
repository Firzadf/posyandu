<?php

namespace App\Http\Controllers;

use App\Models\JadwalKegiatan;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class JadwalKegiatanController extends Controller
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
        // Ambil parameter filter
        $status = $request->status;
        $dari_tanggal = $request->dari_tanggal;
        $sampai_tanggal = $request->sampai_tanggal;
        
        // Query jadwal kegiatan
        $jadwalKegiatan = JadwalKegiatan::query()
            ->when($request->search, function($query) use ($request) {
                return $query->where(function($q) use ($request) {
                    $q->where('nama_kegiatan', 'like', '%' . $request->search . '%')
                      ->orWhere('tempat', 'like', '%' . $request->search . '%')
                      ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
                });
            })
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($dari_tanggal, function($query) use ($dari_tanggal) {
                return $query->where('tanggal', '>=', $dari_tanggal);
            })
            ->when($sampai_tanggal, function($query) use ($sampai_tanggal) {
                return $query->where('tanggal', '<=', $sampai_tanggal);
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu_mulai', 'asc')
            ->paginate(10);
        
        return view('jadwal-kegiatan.index', compact('jadwalKegiatan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $posyandus = Posyandu::all();
        return view('jadwal-kegiatan.create', compact('posyandus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'posyandu_id' => 'required|exists:posyandu,id',
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'nullable|after:waktu_mulai',
            'tempat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:Terjadwal,Berlangsung,Selesai,Dibatalkan',
            'is_pengumuman' => 'boolean',
            'kirim_pengingat' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal menyimpan jadwal kegiatan.');
        }

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['is_pengumuman'] = $request->has('is_pengumuman');
        $data['kirim_pengingat'] = $request->has('kirim_pengingat');
        
        JadwalKegiatan::create($data);
        
        return redirect()->route('jadwal-kegiatan.index')
            ->with('success', 'Jadwal kegiatan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(JadwalKegiatan $jadwalKegiatan)
    {
        return view('jadwal-kegiatan.show', compact('jadwalKegiatan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JadwalKegiatan $jadwalKegiatan)
    {
        $posyandus = Posyandu::all();
        return view('jadwal-kegiatan.edit', compact('jadwalKegiatan', 'posyandus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JadwalKegiatan $jadwalKegiatan)
    {
        $validator = Validator::make($request->all(), [
            'posyandu_id' => 'required|exists:posyandu,id',
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'nullable|after:waktu_mulai',
            'tempat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:Terjadwal,Berlangsung,Selesai,Dibatalkan',
            'is_pengumuman' => 'boolean',
            'kirim_pengingat' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui jadwal kegiatan.');
        }

        $data = $request->all();
        $data['is_pengumuman'] = $request->has('is_pengumuman');
        $data['kirim_pengingat'] = $request->has('kirim_pengingat');
        
        $jadwalKegiatan->update($data);
        
        return redirect()->route('jadwal-kegiatan.show', $jadwalKegiatan)
            ->with('success', 'Jadwal kegiatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JadwalKegiatan $jadwalKegiatan)
    {
        $jadwalKegiatan->delete();
        
        return redirect()->route('jadwal-kegiatan.index')
            ->with('success', 'Jadwal kegiatan berhasil dihapus.');
    }
    
    /**
     * Update status jadwal kegiatan
     */
    public function updateStatus(Request $request, JadwalKegiatan $jadwalKegiatan)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Terjadwal,Berlangsung,Selesai,Dibatalkan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui status jadwal kegiatan.');
        }

        $jadwalKegiatan->update(['status' => $request->status]);
        
        return redirect()->back()
            ->with('success', 'Status jadwal kegiatan berhasil diperbarui.');
    }
    
    /**
     * Calendar view
     */
    public function calendar()
    {
        $events = JadwalKegiatan::all()->map(function($kegiatan) {
            return [
                'id' => $kegiatan->id,
                'title' => $kegiatan->nama_kegiatan,
                'start' => $kegiatan->tanggal->format('Y-m-d') . 'T' . Carbon::parse($kegiatan->waktu_mulai)->format('H:i:s'),
                'end' => $kegiatan->waktu_selesai ? 
                    $kegiatan->tanggal->format('Y-m-d') . 'T' . Carbon::parse($kegiatan->waktu_selesai)->format('H:i:s') : null,
                'url' => route('jadwal-kegiatan.show', $kegiatan),
                'backgroundColor' => $this->getStatusColor($kegiatan->status),
                'borderColor' => $this->getStatusColor($kegiatan->status),
                'textColor' => '#fff',
                'extendedProps' => [
                    'status' => $kegiatan->status,
                    'tempat' => $kegiatan->tempat,
                ]
            ];
        });
        
        return view('jadwal-kegiatan.calendar', compact('events'));
    }
    
    /**
     * Get color based on status
     */
    private function getStatusColor($status)
    {
        switch ($status) {
            case 'Terjadwal':
                return '#3788d8'; // blue
            case 'Berlangsung':
                return '#28a745'; // green
            case 'Selesai':
                return '#6c757d'; // gray
            case 'Dibatalkan':
                return '#dc3545'; // red
            default:
                return '#f39c12'; // orange
        }
    }
}