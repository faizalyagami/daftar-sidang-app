@extends('layouts.app')

@section('title', 'Reviewer Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card fade-in">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Dashboard Reviewer
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stat-card text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <h3>{{ $totalPending }}</h3>
                                <p class="mb-0">Menunggu Review</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card text-center" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <h3>{{ $totalRevision }}</h3>
                                <p class="mb-0">Perlu Revisi</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card text-center" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <h3>{{ $totalCompleted }}</h3>
                                <p class="mb-0">Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        Pendaftaran Skripsi
                    </h5>
                </div>
                <div class="card-body">
                    @if($skripsi->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Tidak ada pendaftaran skripsi yang perlu direview</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
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
                                    @foreach($skripsi as $item)
                                    <tr>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $item->mahasiswa->npm }}</td>
                                        <td>{{ $item->mahasiswa->user->name }}</td>
                                        <td>{{ Str::limit($item->judul_skripsi, 30) }}</td>
                                        <td>
                                            @if($item->status == 'review')
                                                <span class="badge bg-warning">Perlu Review</span>
                                            @else
                                                <span class="badge bg-info">Revisi</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('reviewer.skripsi.review', $item->id) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil-square"></i> Review
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
            <div class="card">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-book me-2"></i>
                        Pendaftaran Metodologi
                    </h5>
                </div>
                <div class="card-body">
                    @if($metodologi->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Tidak ada pendaftaran metodologi yang perlu direview</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
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
                                    @foreach($metodologi as $item)
                                    <tr>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $item->mahasiswa->npm }}</td>
                                        <td>{{ $item->mahasiswa->user->name }}</td>
                                        <td>{{ Str::limit($item->judul_penelitian, 30) }}</td>
                                        <td>
                                            @if($item->status == 'review')
                                                <span class="badge bg-warning">Perlu Review</span>
                                            @else
                                                <span class="badge bg-info">Revisi</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('reviewer.metodologi.review', $item->id) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil-square"></i> Review
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

@push('styles')
<style>
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 20px;
        color: white;
    }
    .stat-card h3 {
        font-size: 32px;
        font-weight: bold;
        margin-bottom: 5px;
    }
</style>
@endpush