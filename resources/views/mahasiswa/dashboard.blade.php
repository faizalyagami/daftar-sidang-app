@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card fade-in">
            <div class="card-body">
                <h4 class="mb-3">Selamat Datang, {{ Auth::user()->name }}!</h4>
                <p class="text-muted">Berikut adalah status pendaftaran Anda saat ini.</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Pendaftaran Sidang Skripsi
                </h5>
            </div>
            <div class="card-body">
                @if($pendaftaranSkripsi->isEmpty())
                    <div class="text-center py-4">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Belum ada pendaftaran sidang skripsi.</p>
                        <a href="{{ route('mahasiswa.daftar-skripsi') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle"></i> Daftar Sekarang
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                32
                                    <th>Tanggal</th>
                                    <th>Judul</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendaftaranSkripsi as $item)
                                <tr>
                                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                    <td>{{ Str::limit($item->judul_skripsi, 40) }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $item->status }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('mahasiswa.show-skripsi', $item->id) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Detail
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

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-book me-2"></i>
                    Pendaftaran Ujian Metodologi
                </h5>
            </div>
            <div class="card-body">
                @if($pendaftaranMetodologi->isEmpty())
                    <div class="text-center py-4">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Belum ada pendaftaran ujian metodologi.</p>
                        <a href="{{ route('mahasiswa.daftar-metodologi') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle"></i> Daftar Sekarang
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Judul</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendaftaranMetodologi as $item)
                                <tr>
                                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                    <td>{{ Str::limit($item->judul_penelitian, 40) }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $item->status }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('mahasiswa.show-metodologi', $item->id) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Detail
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
@endsection