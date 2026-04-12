@extends('layouts.app')

@section('title', 'Edit Periode Akademik')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>
                        Edit Periode Akademik
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.academic-periods.update', $period->id) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Semester</label>
                                <select name="semester" class="form-select" required>
                                    <option value="Ganjil" {{ $period->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ $period->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun Akademik</label>
                                <input type="text" name="tahun_akademik" class="form-control" 
                                       value="{{ $period->tahun_akademik }}" required>
                                <small class="text-muted">Format: YYYY/YYYY (contoh: 2025/2026)</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="{{ $period->start_date->format('Y-m-d') }}" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="{{ $period->end_date->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6 class="mb-3">Pengaturan Pendaftaran</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="skripsi_open" class="form-check-input" 
                                           id="skripsi_open" value="1" {{ $period->skripsi_open ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="skripsi_open">
                                        <i class="bi bi-file-earmark-text me-1"></i> Buka Pendaftaran Skripsi
                                    </label>
                                    <div class="small text-muted mt-1">
                                        Jika diaktifkan, mahasiswa dapat mendaftar sidang skripsi pada periode ini.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="metodologi_open" class="form-check-input" 
                                           id="metodologi_open" value="1" {{ $period->metodologi_open ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="metodologi_open">
                                        <i class="bi bi-book me-1"></i> Buka Pendaftaran Metodologi
                                    </label>
                                    <div class="small text-muted mt-1">
                                        Jika diaktifkan, mahasiswa dapat mendaftar ujian metodologi pada periode ini.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_active" class="form-check-input" 
                                           id="is_active" value="1" {{ $period->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_active">
                                        <i class="bi bi-check-circle me-1"></i> Aktifkan Periode Ini
                                    </label>
                                    <div class="small text-muted mt-1">
                                        Periode ini akan menjadi periode berjalan. Hanya satu periode yang bisa aktif.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3">{{ $period->description }}</textarea>
                        </div>
                        
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('admin.academic-periods.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Form submit loading state
    document.getElementById('editForm')?.addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner me-2"></span> Menyimpan...';
    });
</script>
@endpush