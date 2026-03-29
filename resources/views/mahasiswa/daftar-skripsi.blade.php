@extends('layouts.app')

@section('title', 'Daftar Sidang Skripsi')

@section('content')
<style>
    .form-section {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .form-section h5 {
        color: #667eea;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e0e0e0;
    }
    
    .required-field::after {
        content: '*';
        color: red;
        margin-left: 4px;
    }
    
    .file-info {
        font-size: 11px;
        color: #6c757d;
        margin-top: 5px;
    }
    
    .file-info i {
        margin-right: 3px;
    }
    
    .upload-area {
        border: 2px dashed #e0e0e0;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
        background: #f8f9fa;
    }
    
    .upload-area:hover {
        border-color: #667eea;
        background: #f0f4ff;
    }
    
    .upload-area i {
        font-size: 24px;
        color: #667eea;
        margin-bottom: 5px;
    }
    
    .period-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 15px;
        border-radius: 10px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .period-badge i {
        font-size: 16px;
    }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h4 class="mb-1">
                                <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                                Formulir Pendaftaran Sidang Skripsi
                            </h4>
                            <p class="text-muted mb-0">Silakan isi formulir berikut dengan lengkap dan benar</p>
                        </div>
                        <div class="period-badge mt-2 mt-sm-0">
                            <i class="bi bi-calendar-week"></i>
                            Periode Aktif: 
                            @if($activePeriod)
                                {{ $activePeriod->semester }} {{ $activePeriod->tahun_akademik }}
                            @else
                                Belum diatur
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('mahasiswa.store-skripsi') }}" method="POST" enctype="multipart/form-data" id="skripsiForm">
                        @csrf
                        
                        <!-- Data Pendaftaran -->
                        <div class="form-section">
                            <h5>
                                <i class="bi bi-info-circle me-2"></i>
                                Data Pendaftaran
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Judul Skripsi
                                    </label>
                                    <textarea class="form-control @error('judul_skripsi') is-invalid @enderror" 
                                              name="judul_skripsi" 
                                              rows="3"
                                              placeholder="Masukkan judul skripsi dengan lengkap dan jelas"
                                              required>{{ old('judul_skripsi') }}</textarea>
                                    <div class="file-info">
                                        <i class="bi bi-info-circle"></i> Gunakan huruf kapital di awal setiap kata
                                    </div>
                                    @error('judul_skripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Dosen Pembimbing
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('dosen_pembimbing') is-invalid @enderror" 
                                           name="dosen_pembimbing" 
                                           value="{{ old('dosen_pembimbing') }}"
                                           placeholder="Contoh: Dr. Ahmad Rizal, M.Si"
                                           required>
                                    @error('dosen_pembimbing')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        Narasumber Seminar Skripsi
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('narasumber') is-invalid @enderror" 
                                           name="narasumber" 
                                           value="{{ old('narasumber') }}"
                                           placeholder="Contoh: Dr. Siti Fatimah, M.Psi">
                                    @error('narasumber')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        Tanggal Seminar
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('tanggal_seminar') is-invalid @enderror" 
                                           name="tanggal_seminar" 
                                           value="{{ old('tanggal_seminar') }}">
                                    @error('tanggal_seminar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dokumen Persyaratan -->
                        <div class="form-section">
                            <h5>
                                <i class="bi bi-file-earmark-text me-2"></i>
                                Dokumen Persyaratan
                            </h5>
                            <div class="alert alert-info mb-4">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Pastikan semua dokumen yang diupload sudah benar dan sesuai dengan ketentuan. 
                                Format file yang diterima: PDF, JPG, JPEG, PNG (Max 2MB per file, kecuali Berkas Skripsi Max 10MB)
                            </div>
                            
                            <div class="row">
                                <!-- Bukti Pembayaran Registrasi -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Bukti Pembayaran Registrasi Terakhir
                                    </label>
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-calendar-check"></i> 
                                        @if($activePeriod)
                                            {{ $activePeriod->semester }} {{ $activePeriod->tahun_akademik }}
                                        @else
                                            Periode belum diatur
                                        @endif
                                    </small>
                                    <div class="upload-area" onclick="document.getElementById('bukti_pembayaran_registrasi').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">PDF, JPG, JPEG, PNG (Max 2MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('bukti_pembayaran_registrasi') is-invalid @enderror" 
                                           id="bukti_pembayaran_registrasi"
                                           name="bukti_pembayaran_registrasi" 
                                           accept=".pdf,.jpg,.jpeg,.png" 
                                           required>
                                    <div class="file-info" id="file-info-registrasi"></div>
                                    @error('bukti_pembayaran_registrasi')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Bukti Pembayaran Biaya Sidang -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Bukti Pembayaran Biaya Sidang
                                    </label>
                                    <small class="text-danger d-block mb-2">
                                        <i class="bi bi-cash-stack"></i> Rp. 1.500.000
                                    </small>
                                    <div class="upload-area" onclick="document.getElementById('bukti_pembayaran_sidang').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">PDF, JPG, JPEG, PNG (Max 2MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('bukti_pembayaran_sidang') is-invalid @enderror" 
                                           id="bukti_pembayaran_sidang"
                                           name="bukti_pembayaran_sidang" 
                                           accept=".pdf,.jpg,.jpeg,.png" 
                                           required>
                                    <div class="file-info" id="file-info-sidang"></div>
                                    @error('bukti_pembayaran_sidang')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Bukti Pembayaran Skripsi -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Bukti Pembayaran Skripsi
                                    </label>
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-calendar-check"></i> 
                                        @if($activePeriod)
                                            {{ $activePeriod->semester }} {{ $activePeriod->tahun_akademik }}
                                        @else
                                            Periode belum diatur
                                        @endif
                                    </small>
                                    <div class="upload-area" onclick="document.getElementById('bukti_pembayaran_skripsi').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">PDF, JPG, JPEG, PNG (Max 2MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('bukti_pembayaran_skripsi') is-invalid @enderror" 
                                           id="bukti_pembayaran_skripsi"
                                           name="bukti_pembayaran_skripsi" 
                                           accept=".pdf,.jpg,.jpeg,.png" 
                                           required>
                                    <div class="file-info" id="file-info-skripsi"></div>
                                    @error('bukti_pembayaran_skripsi')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Formulir Rencana Studi (FRS) -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Formulir Rencana Studi (FRS)
                                    </label>
                                    <div class="upload-area" onclick="document.getElementById('frs').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">PDF (Max 2MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('frs') is-invalid @enderror" 
                                           id="frs"
                                           name="frs" 
                                           accept=".pdf" 
                                           required>
                                    <div class="file-info" id="file-info-frs"></div>
                                    @error('frs')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Transkrip Nilai Terakhir -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Transkrip Nilai Terakhir
                                    </label>
                                    <div class="upload-area" onclick="document.getElementById('transkrip_nilai').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">PDF (Max 2MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('transkrip_nilai') is-invalid @enderror" 
                                           id="transkrip_nilai"
                                           name="transkrip_nilai" 
                                           accept=".pdf" 
                                           required>
                                    <div class="file-info" id="file-info-transkrip"></div>
                                    @error('transkrip_nilai')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Surat Bebas Perpustakaan -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Surat Bebas Perpustakaan
                                    </label>
                                    <small class="text-muted d-block mb-2">
                                        Perpustakaan Pusat Universitas dan Perpustakaan BEM-F Psikologi Unisba
                                    </small>
                                    <div class="upload-area" onclick="document.getElementById('surat_bebas_perpus').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">PDF (Max 2MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('surat_bebas_perpus') is-invalid @enderror" 
                                           id="surat_bebas_perpus"
                                           name="surat_bebas_perpus" 
                                           accept=".pdf" 
                                           required>
                                    <div class="file-info" id="file-info-perpus"></div>
                                    @error('surat_bebas_perpus')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Surat Bebas Peminjaman Alat Tes -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Surat Bebas Peminjaman Alat Tes
                                    </label>
                                    <small class="text-muted d-block mb-2">
                                        Alat Tes dan Material lain dari Lab. Psikologi Unisba
                                    </small>
                                    <div class="upload-area" onclick="document.getElementById('surat_bebas_alat_tes').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">PDF (Max 2MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('surat_bebas_alat_tes') is-invalid @enderror" 
                                           id="surat_bebas_alat_tes"
                                           name="surat_bebas_alat_tes" 
                                           accept=".pdf" 
                                           required>
                                    <div class="file-info" id="file-info-alat-tes"></div>
                                    @error('surat_bebas_alat_tes')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Sertifikat Pesantren Calon Sarjana -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Sertifikat Pesantren Calon Sarjana
                                    </label>
                                    <div class="upload-area" onclick="document.getElementById('sertifikat_pesantren').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">PDF (Max 2MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('sertifikat_pesantren') is-invalid @enderror" 
                                           id="sertifikat_pesantren"
                                           name="sertifikat_pesantren" 
                                           accept=".pdf" 
                                           required>
                                    <div class="file-info" id="file-info-pesantren"></div>
                                    @error('sertifikat_pesantren')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Sertifikat SKS Non Akademik -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Sertifikat SKS Non Akademik
                                    </label>
                                    <div class="upload-area" onclick="document.getElementById('sertifikat_sks_non_akademik').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">PDF (Max 2MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('sertifikat_sks_non_akademik') is-invalid @enderror" 
                                           id="sertifikat_sks_non_akademik"
                                           name="sertifikat_sks_non_akademik" 
                                           accept=".pdf" 
                                           required>
                                    <div class="file-info" id="file-info-sks"></div>
                                    @error('sertifikat_sks_non_akademik')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Surat Lolos Turn It In -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Surat Lolos Turn It In
                                    </label>
                                    <small class="text-danger d-block mb-2">
                                        <i class="bi bi-exclamation-triangle"></i> Maksimal Similarity < 25%
                                    </small>
                                    <div class="upload-area" onclick="document.getElementById('surat_lolos_turnitin').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">PDF (Max 2MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('surat_lolos_turnitin') is-invalid @enderror" 
                                           id="surat_lolos_turnitin"
                                           name="surat_lolos_turnitin" 
                                           accept=".pdf" 
                                           required>
                                    <div class="file-info" id="file-info-turnitin"></div>
                                    @error('surat_lolos_turnitin')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Sertifikat TOEFL -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Sertifikat TOEFL
                                    </label>
                                    <small class="text-danger d-block mb-2">
                                        <i class="bi bi-exclamation-triangle"></i> Minimal Skor 475
                                    </small>
                                    <div class="upload-area" onclick="document.getElementById('sertifikat_toefl').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">PDF (Max 2MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('sertifikat_toefl') is-invalid @enderror" 
                                           id="sertifikat_toefl"
                                           name="sertifikat_toefl" 
                                           accept=".pdf" 
                                           required>
                                    <div class="file-info" id="file-info-toefl"></div>
                                    @error('sertifikat_toefl')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Pas Foto -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Pas Foto
                                    </label>
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-image"></i> Latar biru, softfile asli bukan scan foto
                                    </small>
                                    <div class="upload-area" onclick="document.getElementById('pas_foto').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">JPG, JPEG, PNG (Max 2MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('pas_foto') is-invalid @enderror" 
                                           id="pas_foto"
                                           name="pas_foto" 
                                           accept=".jpg,.jpeg,.png" 
                                           required>
                                    <div class="file-info" id="file-info-foto"></div>
                                    @error('pas_foto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Buku Bimbingan -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Buku Bimbingan
                                    </label>
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-book"></i> Scan lengkap dari bagian identitas
                                    </small>
                                    <div class="upload-area" onclick="document.getElementById('buku_bimbingan').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">PDF (Max 2MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('buku_bimbingan') is-invalid @enderror" 
                                           id="buku_bimbingan"
                                           name="buku_bimbingan" 
                                           accept=".pdf" 
                                           required>
                                    <div class="file-info" id="file-info-bimbingan"></div>
                                    @error('buku_bimbingan')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Surat Perbaikan Hasil Seminar -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Surat Perbaikan Hasil Seminar Skripsi
                                    </label>
                                    <div class="upload-area" onclick="document.getElementById('surat_perbaikan').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">PDF (Max 2MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('surat_perbaikan') is-invalid @enderror" 
                                           id="surat_perbaikan"
                                           name="surat_perbaikan" 
                                           accept=".pdf" 
                                           required>
                                    <div class="file-info" id="file-info-perbaikan"></div>
                                    @error('surat_perbaikan')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Surat Ijin Sidang -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Surat Pernyataan Ijin Mengikuti Sidang Skripsi
                                    </label>
                                    <div class="upload-area" onclick="document.getElementById('surat_ijin_sidang').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">PDF (Max 2MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('surat_ijin_sidang') is-invalid @enderror" 
                                           id="surat_ijin_sidang"
                                           name="surat_ijin_sidang" 
                                           accept=".pdf" 
                                           required>
                                    <div class="file-info" id="file-info-ijin"></div>
                                    @error('surat_ijin_sidang')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Berkas Skripsi Softcopy -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-semibold required-field">
                                        Berkas Skripsi Softcopy
                                    </label>
                                    <div class="upload-area" onclick="document.getElementById('berkas_skripsi').click()">
                                        <i class="bi bi-cloud-upload"></i>
                                        <div>Klik atau drag file untuk upload</div>
                                        <small class="text-muted">PDF (Max 10MB)</small>
                                    </div>
                                    <input type="file" 
                                           class="form-control d-none @error('berkas_skripsi') is-invalid @enderror" 
                                           id="berkas_skripsi"
                                           name="berkas_skripsi" 
                                           accept=".pdf" 
                                           required>
                                    <div class="file-info" id="file-info-berkas"></div>
                                    @error('berkas_skripsi')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pernyataan -->
                        <div class="form-section">
                            <div class="alert alert-warning">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="persetujuan" required>
                                    <label class="form-check-label" for="persetujuan">
                                        <strong>Pernyataan:</strong> Dengan ini saya menyatakan bahwa dokumen pendaftaran SIDANG SKRIPSI yang saya ajukan adalah ASLI dan SUDAH MENDAPAT PERSETUJUAN DOSEN PEMBIMBING SKRIPSI. Apabila di kemudian hari ditemukan dokumen yang saya ajukan palsu atau tanpa seizin dosen pembimbing, SAYA BERSEDIA MENERIMA KONSEKUENSI yang ditentukan Fakultas Psikologi Universitas Islam Bandung.
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tombol Aksi -->
                        <div class="d-flex justify-content-between gap-3 mt-4">
                            <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary px-4">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-5" id="submitBtn">
                                <i class="bi bi-send"></i> Submit Pendaftaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // File upload preview
    function setupFileUpload(inputId, infoId) {
        const input = document.getElementById(inputId);
        const info = document.getElementById(infoId);
        
        if (input) {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const fileName = this.files[0].name;
                    const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2);
                    info.innerHTML = `<i class="bi bi-check-circle-fill text-success"></i> ${fileName} (${fileSize} MB)`;
                    info.style.color = '#28a745';
                } else {
                    info.innerHTML = '';
                }
            });
        }
    }
    
    // Setup all file uploads
    setupFileUpload('bukti_pembayaran_registrasi', 'file-info-registrasi');
    setupFileUpload('bukti_pembayaran_sidang', 'file-info-sidang');
    setupFileUpload('bukti_pembayaran_skripsi', 'file-info-skripsi');
    setupFileUpload('frs', 'file-info-frs');
    setupFileUpload('transkrip_nilai', 'file-info-transkrip');
    setupFileUpload('surat_bebas_perpus', 'file-info-perpus');
    setupFileUpload('surat_bebas_alat_tes', 'file-info-alat-tes');
    setupFileUpload('sertifikat_pesantren', 'file-info-pesantren');
    setupFileUpload('sertifikat_sks_non_akademik', 'file-info-sks');
    setupFileUpload('surat_lolos_turnitin', 'file-info-turnitin');
    setupFileUpload('sertifikat_toefl', 'file-info-toefl');
    setupFileUpload('pas_foto', 'file-info-foto');
    setupFileUpload('buku_bimbingan', 'file-info-bimbingan');
    setupFileUpload('surat_perbaikan', 'file-info-perbaikan');
    setupFileUpload('surat_ijin_sidang', 'file-info-ijin');
    setupFileUpload('berkas_skripsi', 'file-info-berkas');
    
    // Form submit loading state
    document.getElementById('skripsiForm')?.addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner me-2"></span> Memproses...';
    });
    
    // Drag and drop functionality
    document.querySelectorAll('.upload-area').forEach(area => {
        area.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#667eea';
            this.style.background = '#f0f4ff';
        });
        
        area.addEventListener('dragleave', function(e) {
            this.style.borderColor = '#e0e0e0';
            this.style.background = '#f8f9fa';
        });
        
        area.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#e0e0e0';
            this.style.background = '#f8f9fa';
            
            const input = this.nextElementSibling;
            if (input && input.type === 'file') {
                input.files = e.dataTransfer.files;
                // Trigger change event
                const event = new Event('change');
                input.dispatchEvent(event);
            }
        });
    });
</script>
@endpush
@endsection