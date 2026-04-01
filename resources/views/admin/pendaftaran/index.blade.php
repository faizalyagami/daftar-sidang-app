@extends('layouts.app')

@section('title', 'Semua Pendaftaran')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Filter Periode -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.pendaftaran') }}" class="row g-3 align-items-end">
                    <div class="col-auto">
                        <label class="form-label fw-semibold">Filter Periode Pendaftaran</label>
                        <select name="period_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Periode</option>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}" {{ request('period_id') == $period->id ? 'selected' : '' }}>
                                    {{ $period->semester }} {{ $period->tahun_akademik }}
                                    {{-- ({{ $period->start_date->format('d/m/Y') }} - {{ $period->end_date->format('d/m/Y') }}) --}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.pendaftaran') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4" id="pendaftaranTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="skripsi-tab" data-bs-toggle="tab" 
                        data-bs-target="#skripsi" type="button" role="tab">
                    <i class="bi bi-file-text"></i> Sidang Skripsi
                    <span class="badge bg-primary">{{ $skripsi->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="metodologi-tab" data-bs-toggle="tab" 
                        data-bs-target="#metodologi" type="button" role="tab">
                    <i class="bi bi-book"></i> Ujian Metodologi
                    <span class="badge bg-primary">{{ $metodologi->count() }}</span>
                </button>
            </li>
        </ul>
        
        <div class="tab-content">
            <div class="tab-pane fade show active" id="skripsi" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <th>Tanggal</th>
                                    <th>Periode</th>
                                    <th>NPM</th>
                                    <th>Nama</th>
                                    <th>Judul Skripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </thead>
                                <tbody>
                                    @forelse($skripsi as $item)
                                    @php
                                        $period = $item->academicPeriod;
                                    @endphp
                                    <tr>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @if($period)
                                                <span class="badge bg-secondary">{{ $period->semester }} {{ $period->tahun_akademik }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $item->mahasiswa->npm }}</td>
                                        <td>{{ $item->mahasiswa->user->name }}</td>
                                        <td>{{ Str::limit($item->judul_skripsi, 50) }}</td>
                                        <td>
                                            <span class="status-badge status-{{ $item->status }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.pendaftaran.skripsi.show', $item->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada pendaftaran sidang skripsi</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="metodologi" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    32
                                        <th>Tanggal</th>
                                        <th>Periode</th>
                                        <th>NPM</th>
                                        <th>Nama</th>
                                        <th>Judul Penelitian</th>
                                        <th>Pembimbing</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </thead>
                                <tbody>
                                    @forelse($metodologi as $item)
                                    @php
                                        $period = $item->academicPeriod;
                                    @endphp
                                    <tr>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @if($period)
                                                <span class="badge bg-secondary">{{ $period->semester }} {{ $period->tahun_akademik }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $item->mahasiswa->npm }}</td>
                                        <td>{{ $item->mahasiswa->user->name }}</td>
                                        <td>{{ Str::limit($item->judul_penelitian, 50) }}</td>
                                        <td>{{ $item->dosen_pembimbing }}</td>
                                        <td>
                                            <span class="status-badge status-{{ $item->status }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.pendaftaran.metodologi.show', $item->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada pendaftaran ujian metodologi</td>
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
</div>
@endsection