@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<style>
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 20px;
        color: white;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: default;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
    }

    .stat-number {
        font-size: 36px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 14px;
        opacity: 0.9;
    }

    .stat-icon {
        font-size: 48px;
        opacity: 0.2;
        position: absolute;
        right: 20px;
        bottom: 20px;
    }

    .period-badge {
        background: #e9ecef;
        color: #4a5568;
        border-radius: 30px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
        margin: 3px;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffc107;
    }

    .status-review {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #17a2b8;
    }

    .status-approved {
        background: #d4edda;
        color: #155724;
        border: 1px solid #28a745;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #dc3545;
    }

    .status-revision {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffc107;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #a0aec0;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .table-custom thead th {
        background: #f8f9fc;
        border-bottom: 2px solid #e3e6f0;
        color: #5a5c69;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px;
    }

    .table-custom tbody td {
        padding: 12px;
        vertical-align: middle;
    }
</style>

<div class="container-fluid px-4">
    <!-- Welcome Section dengan style card-custom -->
    <div class="card-custom card border-0 shadow-sm mb-4">
        <div class="card-header-custom">
            <h5 class="mb-0">
                <i class="bi bi-mortarboard-fill me-2"></i>
                Selamat Datang, {{ Auth::user()->name }}!
            </h5>
        </div>
        <div class="card-body">
            <p class="mb-0 text-muted">Berikut adalah ringkasan dan status pendaftaran Anda saat ini.</p>
        </div>
    </div>

    <!-- Statistik Durasi -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="stat-number">{{ $skripsiDuration ?? '-' }}</div>
                <div class="stat-label">Lama Menempuh Skripsi</div>
                <i class="bi bi-file-earmark-text stat-icon"></i>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="stat-number">{{ $metodologiDuration ?? '-' }}</div>
                <div class="stat-label">Lama Menempuh Metodologi</div>
                <i class="bi bi-book stat-icon"></i>
            </div>
        </div>
    </div>

    <div class="row mb-4">
    @if($jadwalSkripsi)
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header-custom">
                <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Jadwal Sidang Skripsi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Hari & Tanggal</strong><br>
                        {{ \Carbon\Carbon::parse($jadwalSkripsi->tanggal)->translatedFormat('l, d F Y') }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Waktu</strong><br>
                        {{ \Carbon\Carbon::parse($jadwalSkripsi->waktu_mulai)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($jadwalSkripsi->waktu_selesai)->format('H:i') }} WIB
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Ruang</strong><br>
                        {{ $jadwalSkripsi->ruang ?? 'Belum ditentukan' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Dosen Penguji</strong><br>
                        @php
                            $penguji = [];
                            if($jadwalSkripsi->dosen_penguji_1) $penguji[] = $jadwalSkripsi->dosen_penguji_1;
                            if($jadwalSkripsi->dosen_penguji_2) $penguji[] = $jadwalSkripsi->dosen_penguji_2;
                            if($jadwalSkripsi->dosen_penguji_3) $penguji[] = $jadwalSkripsi->dosen_penguji_3;
                        @endphp
                        {{ implode(', ', $penguji) ?: 'Belum ditentukan' }}
                    </div>
                        @if($jadwalSkripsi->keterangan)
                        <div class="col-12 mt-2">
                            <strong>Keterangan</strong><br>
                            {{ $jadwalSkripsi->keterangan }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($jadwalMetodologi)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Jadwal Ujian Metodologi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Hari & Tanggal</strong><br>
                            {{ \Carbon\Carbon::parse($jadwalMetodologi->tanggal)->translatedFormat('l, d F Y') }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Waktu</strong><br>
                            {{ \Carbon\Carbon::parse($jadwalMetodologi->waktu_mulai)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($jadwalMetodologi->waktu_selesai)->format('H:i') }} WIB
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Ruang</strong><br>
                            {{ $jadwalMetodologi->ruang ?? 'Belum ditentukan' }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Dosen Penguji</strong><br>
                            @php
                                $penguji = [];
                                if($jadwalMetodologi->dosen_penguji_1) $penguji[] = $jadwalMetodologi->dosen_penguji_1;
                                if($jadwalMetodologi->dosen_penguji_2) $penguji[] = $jadwalMetodologi->dosen_penguji_2;
                            @endphp
                            {{ implode(', ', $penguji) ?: 'Belum ditentukan' }}
                        </div>
                        @if($jadwalMetodologi->keterangan)
                        <div class="col-12 mt-2">
                            <strong>Keterangan</strong><br>
                            {{ $jadwalMetodologi->keterangan }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Riwayat Pendaftaran -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card-custom card border-0 shadow-sm h-100">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Riwayat Skripsi</h5>
                </div>
                <div class="card-body">
                    @if(isset($skripsiPeriods) && $skripsiPeriods->count())
                    <div>
                        @foreach($skripsiPeriods as $period)
                        <span class="period-badge">{{ $period }}</span>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p class="mb-0">Belum ada pendaftaran skripsi</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-custom card border-0 shadow-sm h-100">
                <div class="card-header-custom">
                    <h5 class="mb-0"><i class="bi bi-book me-2"></i>Riwayat Metodologi</h5>
                </div>
                <div class="card-body">
                    @if(isset($metodologiPeriods) && $metodologiPeriods->count())
                    <div>
                        @foreach($metodologiPeriods as $period)
                        <span class="period-badge">{{ $period }}</span>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p class="mb-0">Belum ada pendaftaran metodologi</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Pendaftaran -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card-custom card border-0 shadow-sm h-100">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Pendaftaran Skripsi</h5>
                    <a href="{{ route('mahasiswa.daftar-skripsi') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle"></i> Daftar Baru
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($pendaftaranSkripsi->isEmpty())
                    <div class="empty-state p-4">
                        <i class="bi bi-inbox"></i>
                        <p class="mb-0">Belum ada pendaftaran skripsi</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-custom table-hover mb-0">
                            <thead>
                                <th>Tanggal</th>
                                <th>Judul</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </thead>
                            <tbody>
                                @foreach($pendaftaranSkripsi as $item)
                                <tr>
                                    <td class="align-middle">{{ $item->created_at->format('d/m/Y') }}</td>
                                    <td class="align-middle">{{ Str::limit($item->judul_skripsi, 50) }}</td>
                                    <td class="align-middle">
                                        <span class="status-badge status-{{ $item->status }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('mahasiswa.show-skripsi', $item->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card-custom card border-0 shadow-sm h-100">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-book me-2"></i>Pendaftaran Metodologi</h5>
                    <a href="{{ route('mahasiswa.daftar-metodologi') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle"></i> Daftar Baru
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($pendaftaranMetodologi->isEmpty())
                    <div class="empty-state p-4">
                        <i class="bi bi-inbox"></i>
                        <p class="mb-0">Belum ada pendaftaran metodologi</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-custom table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Judul</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendaftaranMetodologi as $item)
                                <tr>
                                    <td class="align-middle">{{ $item->created_at->format('d/m/Y') }}</td>
                                    <td class="align-middle">{{ Str::limit($item->judul_penelitian, 50) }}</td>
                                    <td class="align-middle">
                                        <span class="status-badge status-{{ $item->status }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('mahasiswa.show-metodologi', $item->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection