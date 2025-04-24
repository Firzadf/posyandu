@extends('layouts.app')

@section('title', 'Dashboard - Sistem Informasi Posyandu')

@section('content')
<div class="container">
    <h2 class="mb-4">
        <i class="fas fa-tachometer-alt me-2"></i>
        Dashboard
    </h2>
    
    <div class="row fade-in">
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="dashboard-stat bg-stat-1 position-relative">
                <div class="stat-count">{{ $totalBalita }}</div>
                <div class="stat-title">Total Balita</div>
                <i class="fas fa-baby stat-icon"></i>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="dashboard-stat bg-stat-2 position-relative">
                <div class="stat-count">{{ $totalIbuHamil }}</div>
                <div class="stat-title">Total Ibu Hamil</div>
                <i class="fas fa-female stat-icon"></i>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="dashboard-stat bg-stat-3 position-relative">
                <div class="stat-count">{{ $pemeriksaanBalitaBulanIni }}</div>
                <div class="stat-title">Pemeriksaan Balita Bulan Ini</div>
                <i class="fas fa-stethoscope stat-icon"></i>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="dashboard-stat bg-stat-4 position-relative">
                <div class="stat-count">{{ $pemeriksaanIbuHamilBulanIni }}</div>
                <div class="stat-title">Pemeriksaan Ibu Hamil Bulan Ini</div>
                <i class="fas fa-heartbeat stat-icon"></i>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Grafik Status Gizi Balita
                    </h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary active" data-chart-period="6">6 Bulan</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-chart-period="3">3 Bulan</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-chart-period="1">1 Bulan</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartStatusGizi"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Grafik Kunjungan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartKunjungan"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Jadwal Kegiatan Mendatang
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($jadwalKegiatan as $kegiatan)
                            <a href="{{ route('jadwal-kegiatan.show', $kegiatan) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $kegiatan->nama_kegiatan }}</h6>
                                    <span class="badge {{ $kegiatan->statusBadgeClass() }}">{{ $kegiatan->status }}</span>
                                </div>
                                <p class="mb-1">
                                    <i class="fas fa-calendar-day me-1"></i> {{ $kegiatan->tanggalFormatted() }}
                                </p>
                                <small>
                                    <i class="fas fa-clock me-1"></i> {{ $kegiatan->waktuFormatted() }}
                                    <i class="fas fa-map-marker-alt ms-2 me-1"></i> {{ $kegiatan->tempat }}
                                </small>
                            </a>
                        @empty
                            <div class="list-group-item">
                                <p class="mb-0 text-center text-muted">Tidak ada jadwal kegiatan mendatang</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('jadwal-kegiatan.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-calendar-alt me-1"></i> Lihat Semua Jadwal
                    </a>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bullhorn me-2"></i>
                        Pengumuman Terbaru
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($pengumuman as $item)
                            <a href="{{ route('pengumuman.show', $item) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $item->judul }}</h6>
                                    <small>{{ $item->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ Str::limit($item->isi, 80) }}</p>
                                <small>
                                    <i class="fas fa-calendar-alt me-1"></i> {{ $item->periodFormatted() }}
                                </small>
                            </a>
                        @empty
                            <div class="list-group-item">
                                <p class="mb-0 text-center text-muted">Tidak ada pengumuman terbaru</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('pengumuman.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-bullhorn me-1"></i> Lihat Semua Pengumuman
                    </a>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Posyandu
                    </h5>
                </div>
                <div class="card-body">
                    @if($posyandu)
                        <h6 class="fw-bold">{{ $posyandu->nama_posyandu }}</h6>
                        <p class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            {{ $posyandu->alamatLengkap() }}
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-phone me-2"></i>
                            {{ $posyandu->telepon ?? '-' }}
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            {{ $posyandu->email ?? '-' }}
                        </p>
                        
                        @if($posyandu->deskripsi)
                            <p class="mt-3">{{ $posyandu->deskripsi }}</p>
                        @endif
                    @else
                        <p class="text-center text-muted">Data Posyandu belum tersedia</p>
                    @endif
                </div>
                @if(auth()->user()->isAdmin() && $posyandu)
                <div class="card-footer text-center">
                    <a href="{{ route('posyandu.edit', $posyandu) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit me-1"></i> Edit Informasi Posyandu
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data untuk grafik status gizi
        const labelsGizi = @json($labels);
        const dataGiziKurang = @json($dataBalitaGiziKurang);
        const dataGiziBaik = @json($dataBalitaGiziBaik);
        const dataGiziLebih = @json($dataBalitaGiziLebih);
        
        // Membuat grafik status gizi
        const chartStatusGizi = new Chart(
            document.getElementById('chartStatusGizi'),
            {
                type: 'line',
                data: {
                    labels: labelsGizi,
                    datasets: [
                        {
                            label: 'Gizi Kurang',
                            data: dataGiziKurang,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Gizi Baik',
                            data: dataGiziBaik,
                            borderColor: '#22c55e',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Gizi Lebih',
                            data: dataGiziLebih,
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            }
        );
        
        // Data untuk grafik kunjungan
        const dataKunjunganBalita = @json($dataKunjungan['balita']);
        const dataKunjunganIbuHamil = @json($dataKunjungan['ibu_hamil']);
        
        // Membuat grafik kunjungan
        const chartKunjungan = new Chart(
            document.getElementById('chartKunjungan'),
            {
                type: 'bar',
                data: {
                    labels: labelsGizi,
                    datasets: [
                        {
                            label: 'Kunjungan Balita',
                            data: dataKunjunganBalita,
                            backgroundColor: 'rgba(59, 130, 246, 0.7)'
                        },
                        {
                            label: 'Kunjungan Ibu Hamil',
                            data: dataKunjunganIbuHamil,
                            backgroundColor: 'rgba(249, 115, 22, 0.7)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            }
        );
        
        // Filter periode grafik
        document.querySelectorAll('[data-chart-period]').forEach(button => {
            button.addEventListener('click', function() {
                const period = parseInt(this.getAttribute('data-chart-period'));
                
                // Remove active class from all buttons
                document.querySelectorAll('[data-chart-period]').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Update chart data
                const newLabels = labelsGizi.slice(-period);
                const newDataGiziKurang = dataGiziKurang.slice(-period);
                const newDataGiziBaik = dataGiziBaik.slice(-period);
                const newDataGiziLebih = dataGiziLebih.slice(-period);
                
                chartStatusGizi.data.labels = newLabels;
                chartStatusGizi.data.datasets[0].data = newDataGiziKurang;
                chartStatusGizi.data.datasets[1].data = newDataGiziBaik;
                chartStatusGizi.data.datasets[2].data = newDataGiziLebih;
                chartStatusGizi.update();
                
                // Update chart kunjungan data
                const newDataKunjunganBalita = dataKunjunganBalita.slice(-period);
                const newDataKunjunganIbuHamil = dataKunjunganIbuHamil.slice(-period);
                
                chartKunjungan.data.labels = newLabels;
                chartKunjungan.data.datasets[0].data = newDataKunjunganBalita;
                chartKunjungan.data.datasets[1].data = newDataKunjunganIbuHamil;
                chartKunjungan.update();
            });
        });
    });
</script>
@endsection