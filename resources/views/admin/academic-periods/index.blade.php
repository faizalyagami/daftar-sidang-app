@extends('layouts.app')

@section('title', 'Kelola Periode Akademik')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-week me-2"></i>
                        Periode Akademik
                    </h5>
                    <a href="{{ route('admin.academic-periods.create') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle"></i> Tambah Periode
                    </a>
                </div>
                <div class="card-body">
                    <!-- Active Period Info -->
                    @if($activePeriod)
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                            <div>
                                <strong>Periode Aktif Saat Ini:</strong><br>
                                {{ $activePeriod->semester }} {{ $activePeriod->tahun_akademik }}
                                ({{ $activePeriod->start_date->format('d/m/Y') }} - {{ $activePeriod->end_date->format('d/m/Y') }})
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Belum ada periode akademik yang aktif. Silakan aktifkan salah satu periode.
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                32
                                <th>No</th>
                                <th>Semester</th>
                                <th>Tahun Akademik</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status Periode</th>
                                <th>Status Pendaftaran</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @forelse($periods as $index => $period)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $period->semester }}</td>
                                    <td>{{ $period->tahun_akademik }}</td>
                                    <td>{{ $period->start_date->format('d/m/Y') }}</td>
                                    <td>{{ $period->end_date->format('d/m/Y') }}</td>
                                    <td>
                                        @if($period->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                        @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- BADGE STATUS BUKA/TUTUP SKRIPSI & METODOLOGI --}}
                                        @if($period->isSkripsiRegistrationOpen())
                                        <span class="badge bg-success">Skripsi: Buka</span>
                                        @else
                                        <span class="badge bg-danger">Skripsi: Tutup</span>
                                        @endif
                                        <br>
                                        @if($period->isMetodologiRegistrationOpen())
                                        <span class="badge bg-success mt-1">Metodologi Penelitian: Buka</span>
                                        @else
                                        <span class="badge bg-danger mt-1">Metodologi Penelitian: Tutup</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.academic-periods.edit', $period->id) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if(!$period->is_active)
                                        <button onclick="setActive({{ $period->id }})"
                                            class="btn btn-sm btn-success">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                        @endif
                                        <form action="{{ route('admin.academic-periods.destroy', $period->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin ingin menghapus periode ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada data periode akademik</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $periods->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setActive(id) {
        if (confirm('Apakah Anda yakin ingin mengaktifkan periode ini?')) {
            window.location.href = '/admin/academic-periods/' + id + '/set-active';
        }
    }
</script>
@endpush
@endsection