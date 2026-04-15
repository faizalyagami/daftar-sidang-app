@extends('layouts.app')

@section('title', 'Riwayat Review')

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reviewer.reviewer.history') }}" class="row g-3 align-items-end">
                <div class="col-auto">
                    <label class="form-label fw-semibold">Filter Periode</label>
                    <select name="period_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Periode</option>
                        @foreach($periods as $period)
                            <option value="{{ $period->id }}" {{ request('period_id') == $period->id ? 'selected' : '' }}>
                                {{ $period->semester }} {{ $period->tahun_akademik }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <a href="{{ route('reviewer.reviewer.history') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card fade-in border-0 shadow-sm">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Riwayat Review
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-4" id="historyTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="skripsi-tab" data-bs-toggle="tab" data-bs-target="#skripsi" type="button" role="tab">
                                <i class="bi bi-file-earmark-text"></i> Skripsi
                                <span class="badge bg-primary">{{ $skripsi->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="metodologi-tab" data-bs-toggle="tab" data-bs-target="#metodologi" type="button" role="tab">
                                <i class="bi bi-book"></i> Metodologi
                                <span class="badge bg-primary">{{ $metodologi->count() }}</span>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="skripsi" role="tabpanel">
                            @if($skripsi->isEmpty())
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada riwayat review skripsi</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tanggal Review</th>
                                                <th>NPM</th>
                                                <th>Nama</th>
                                                <th>Periode</th>
                                                <th>Judul</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($skripsi as $item)
                                            <tr>
                                                <td>{{ $item->updated_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $item->mahasiswa->npm }}</td>
                                                <td>{{ $item->mahasiswa->user->name }}</td>
                                                <td>
                                                    @if($item->academicPeriod)
                                                        @php
                                                            $badgeClass = $item->academicPeriod->semester == 'Ganjil' 
                                                                ? 'bg-primary' 
                                                                : 'bg-info';
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }}">
                                                            {{ $item->academicPeriod->semester }} {{ $item->academicPeriod->tahun_akademik }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ Str::limit($item->judul_skripsi, 50) }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = '';
                                                        $statusText = '';
                                                        switch($item->status) {
                                                            case 'approved': $statusClass = 'bg-success'; $statusText = 'Disetujui'; break;
                                                            case 'rejected': $statusClass = 'bg-danger'; $statusText = 'Ditolak'; break;
                                                            case 'revision': $statusClass = 'bg-warning'; $statusText = 'Revisi'; break;
                                                            default: $statusClass = 'bg-secondary'; $statusText = $item->status;
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('reviewer.skripsi.review', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="metodologi" role="tabpanel">
                            @if($metodologi->isEmpty())
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada riwayat review metodologi</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tanggal Review</th>
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
                                                <td>{{ $item->updated_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $item->mahasiswa->npm }}</td>
                                                <td>{{ $item->mahasiswa->user->name }}</td>
                                                <td>{{ Str::limit($item->judul_penelitian, 50) }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = '';
                                                        $statusText = '';
                                                        switch($item->status) {
                                                            case 'approved': $statusClass = 'bg-success'; $statusText = 'Disetujui'; break;
                                                            case 'rejected': $statusClass = 'bg-danger'; $statusText = 'Ditolak'; break;
                                                            case 'revision': $statusClass = 'bg-warning'; $statusText = 'Revisi'; break;
                                                            default: $statusClass = 'bg-secondary'; $statusText = $item->status;
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('reviewer.metodologi.review', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> Lihat
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
    </div>
</div>
@endsection