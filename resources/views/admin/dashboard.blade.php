@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section mb-4 fade-in">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">Selamat Datang, {{ Auth::user()->name }}! 👋</h2>
                <p class="mb-0 opacity-75">Berikut adalah ringkasan sistem pendaftaran sidang skripsi dan ujian metodologi penelitian.</p>
                <small class="opacity-75">Terakhir login: {{ now()->format('d F Y H:i') }}</small>
            </div>
            <div class="col-md-4 text-end">
                <i class="bi bi-calendar-check fs-1 opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" onclick="window.location='{{ route('admin.mahasiswa.index') }}'">
                <div class="stat-number">{{ $totalMahasiswa ?? 0 }}</div>
                <div class="stat-label">Total Mahasiswa</div>
                <div class="stat-trend">
                    <i class="bi bi-people-fill"></i> Terdaftar di sistem
                </div>
                <i class="bi bi-people-fill stat-icon"></i>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);" onclick="window.location='{{ route('admin.pendaftaran') }}'">
                <div class="stat-number">{{ $totalSkripsi ?? 0 }}</div>
                <div class="stat-label">Pendaftaran Skripsi</div>
                <div class="stat-trend">
                    <i class="bi bi-file-text-fill"></i> Total pendaftaran
                </div>
                <i class="bi bi-file-text-fill stat-icon"></i>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);" onclick="window.location='{{ route('admin.pendaftaran') }}'">
                <div class="stat-number">{{ $totalMetodologi ?? 0 }}</div>
                <div class="stat-label">Pendaftaran Metodologi</div>
                <div class="stat-trend">
                    <i class="bi bi-book-fill"></i> Total pendaftaran
                </div>
                <i class="bi bi-book-fill stat-icon"></i>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);" onclick="window.location='{{ route('admin.pendaftaran') }}'">
                <div class="stat-number">{{ $pendingSkripsi ?? 0 }}</div>
                <div class="stat-label">Menunggu Review</div>
                <div class="stat-trend">
                    <i class="bi bi-hourglass-split"></i> Perlu ditindaklanjuti
                </div>
                <i class="bi bi-hourglass-split stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-custom card">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning-charge-fill me-2"></i>
                        Aksi Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="quick-action" onclick="window.location='{{ route('admin.mahasiswa.import') }}'">
                                <i class="bi bi-cloud-upload"></i>
                                <h6 class="mb-0">Import Mahasiswa</h6>
                                <small class="text-muted">Upload data SIAKAD</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="quick-action" onclick="window.location='{{ route('admin.users.create') }}'">
                                <i class="bi bi-person-plus"></i>
                                <h6 class="mb-0">Tambah User</h6>
                                <small class="text-muted">Admin/Reviewer/Mahasiswa</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="quick-action" onclick="window.location='{{ route('admin.pendaftaran') }}'">
                                <i class="bi bi-files"></i>
                                <h6 class="mb-0">Lihat Pendaftaran</h6>
                                <small class="text-muted">Semua pendaftaran</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="quick-action" onclick="window.location='{{ route('admin.mahasiswa.index') }}'">
                                <i class="bi bi-mortarboard"></i>
                                <h6 class="mb-0">Data Mahasiswa</h6>
                                <small class="text-muted">Kelola data mahasiswa</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Pendaftaran -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card-custom card h-100">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-file-text-fill me-2"></i>
                        Pendaftaran Skripsi Terbaru
                    </h5>
                    <a href="{{ route('admin.pendaftaran') }}" class="text-white text-decoration-none">
                        <small>Lihat semua <i class="bi bi-arrow-right"></i></small>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>NPM</th>
                                    <th>Nama</th>
                                    <th>Judul</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($skripsiTerbaru ?? [] as $item)
                                <tr>
                                    <td><small>{{ $item->created_at->format('d/m/Y') }}</small></td>
                                    <td>{{ $item->mahasiswa->npm ?? '-' }}</td>
                                    <td>{{ $item->mahasiswa->user->name ?? '-' }}</td>
                                    <td>{{ Str::limit($item->judul_skripsi, 30) }}</td>
                                    <td><span class="status-badge status-{{ $item->status }}">{{ ucfirst($item->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.pendaftaran.skripsi.show', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada pendaftaran</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card-custom card h-100">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-book-fill me-2"></i>
                        Pendaftaran Metodologi Terbaru
                    </h5>
                    <a href="{{ route('admin.pendaftaran') }}" class="text-white text-decoration-none">
                        <small>Lihat semua <i class="bi bi-arrow-right"></i></small>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>NPM</th>
                                    <th>Nama</th>
                                    <th>Judul</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($metodologiTerbaru ?? [] as $item)
                                <tr>
                                    <td><small>{{ $item->created_at->format('d/m/Y') }}</small></td>
                                    <td>{{ $item->mahasiswa->npm ?? '-' }}</td>
                                    <td>{{ $item->mahasiswa->user->name ?? '-' }}</td>
                                    <td>{{ Str::limit($item->judul_penelitian, 30) }}</td>
                                    <td><span class="status-badge status-{{ $item->status }}">{{ ucfirst($item->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.pendaftaran.metodologi.show', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada pendaftaran</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.stat-number').forEach(el => {
        const value = parseInt(el.innerText);
        if (!isNaN(value) && value > 0) {
            let current = 0;
            const increment = value / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= value) {
                    el.innerText = value;
                    clearInterval(timer);
                } else {
                    el.innerText = Math.floor(current);
                }
            }, 20);
        }
    });
</script>
@endpush