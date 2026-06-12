@extends('layouts.app')

@section('title', 'Edit Pendaftaran Skripsi - Upload Ulang Dokumen')

@section('content')
    <style>
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
    </style>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h4 class="mb-1">
                            <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                            Edit Pendaftaran Sidang Skripsi
                        </h4>
                        <p class="text-muted mb-0">Upload ulang dokumen yang diminta reviewer</p>
                    </div>
                </div>

                <!-- Informasi Revisi -->
                @if (count($revisiList) > 0)
                    <div class="alert alert-warning mb-4">
                        <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Dokumen yang Perlu Diupload
                            Ulang</h5>
                        <ul class="mb-0">
                            @foreach ($revisiList as $revisi)
                                <li><strong>{{ $revisi['dokumen'] }}</strong>: {{ $revisi['komentar'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('mahasiswa.update-skripsi', $pendaftaran->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Informasi Pendaftaran (Readonly) -->
                            <div class="form-section mb-4">
                                <h5>Informasi Pendaftaran</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Judul Skripsi</label>
                                        <p class="form-control-static border rounded p-2 bg-light">
                                            {{ $pendaftaran->judul_skripsi }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Dosen Pembimbing</label>
                                        <p class="form-control-static border rounded p-2 bg-light">
                                            {{ $pendaftaran->dosen_pembimbing }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Dokumen yang Direvisi -->
                            <div class="form-section mb-4">
                                <h5>Upload Ulang Dokumen yang Direvisi</h5>
                                <div class="alert alert-info mb-3">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Hanya upload dokumen yang diminta reviewer. Dokumen lain tidak perlu diupload ulang.
                                </div>

                                @foreach ($revisiList as $revisi)
                                    @php
                                        $fieldName = '';
                                        switch ($revisi['dokumen']) {
                                            case 'Bukti Pembayaran Registrasi Terakhir':
                                                $fieldName = 'bukti_pembayaran_registrasi';
                                                break;
                                            case 'Bukti Pembayaran Sidang':
                                                $fieldName = 'bukti_pembayaran_sidang';
                                                break;
                                            case 'Bukti Pembayaran Skripsi':
                                                $fieldName = 'bukti_pembayaran_skripsi';
                                                break;
                                            case 'Formulir Rencana Studi (FRS)':
                                                $fieldName = 'frs';
                                                break;
                                            case 'Transkrip Nilai':
                                                $fieldName = 'transkrip_nilai';
                                                break;
                                            case 'Surat Bebas Perpustakaan':
                                                $fieldName = 'surat_bebas_perpus';
                                                break;
                                            case 'Surat Bebas Alat Tes':
                                                $fieldName = 'surat_bebas_alat_tes';
                                                break;
                                            case 'Sertifikat Pesantren':
                                                $fieldName = 'sertifikat_pesantren';
                                                break;
                                            case 'Sertifikat SKS Non Akademik':
                                                $fieldName = 'sertifikat_sks_non_akademik';
                                                break;
                                            case 'Surat Lolos Turnitin':
                                                $fieldName = 'surat_lolos_turnitin';
                                                break;
                                            case 'Sertifikat TOEFL':
                                                $fieldName = 'sertifikat_toefl';
                                                break;
                                            case 'Pas Foto':
                                                $fieldName = 'pas_foto';
                                                break;
                                            case 'Buku Bimbingan':
                                                $fieldName = 'buku_bimbingan';
                                                break;
                                            case 'Surat Perbaikan':
                                                $fieldName = 'surat_perbaikan';
                                                break;
                                            case 'Surat Ijin Sidang':
                                                $fieldName = 'surat_ijin_sidang';
                                                break;
                                            case 'Berkas Skripsi':
                                                $fieldName = 'berkas_skripsi';
                                                break;
                                        }
                                    @endphp
                                    <div class="mb-3">
                                        <label
                                            class="form-label fw-semibold required-field">{{ $revisi['dokumen'] }}</label>
                                        <div class="upload-area"
                                            onclick="document.getElementById('{{ $fieldName }}').click()">
                                            <i class="bi bi-cloud-upload"></i>
                                            <div>Klik atau drag file untuk upload</div>
                                            <small class="text-muted">PDF, JPG, JPEG, PNG (Max 2MB)</small>
                                        </div>
                                        <input type="file" class="form-control d-none" id="{{ $fieldName }}"
                                            name="{{ $fieldName }}" accept=".pdf,.jpg,.jpeg,.png">
                                        <div class="file-info" id="file-info-{{ $fieldName }}"></div>
                                        @error($fieldName)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between gap-3 mt-4">
                                <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary px-4">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary px-5" id="submitBtn">
                                    <i class="bi bi-upload"></i> Upload Ulang
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
            function setupFileUpload(inputId, infoId) {
                const input = document.getElementById(inputId);
                const info = document.getElementById(infoId);
                if (input) {
                    input.addEventListener('change', function() {
                        if (this.files && this.files[0]) {
                            const fileName = this.files[0].name;
                            const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2);
                            info.innerHTML =
                                `<i class="bi bi-check-circle-fill text-success"></i> ${fileName} (${fileSize} MB)`;
                            info.style.color = '#28a745';
                        } else {
                            info.innerHTML = '';
                        }
                    });
                }
            }

            @foreach ($revisiList as $revisi)
                @php
                    $fieldName = '';
                    switch ($revisi['dokumen']) {
                        case 'Bukti Pembayaran Registrasi Terakhir':
                            $fieldName = 'bukti_pembayaran_registrasi';
                            break;
                        case 'Bukti Pembayaran Sidang':
                            $fieldName = 'bukti_pembayaran_sidang';
                            break;
                        case 'Bukti Pembayaran Skripsi':
                            $fieldName = 'bukti_pembayaran_skripsi';
                            break;
                        case 'Formulir Rencana Studi (FRS)':
                            $fieldName = 'frs';
                            break;
                        case 'Transkrip Nilai':
                            $fieldName = 'transkrip_nilai';
                            break;
                        case 'Surat Bebas Perpustakaan':
                            $fieldName = 'surat_bebas_perpus';
                            break;
                        case 'Surat Bebas Alat Tes':
                            $fieldName = 'surat_bebas_alat_tes';
                            break;
                        case 'Sertifikat Pesantren':
                            $fieldName = 'sertifikat_pesantren';
                            break;
                        case 'Sertifikat SKS Non Akademik':
                            $fieldName = 'sertifikat_sks_non_akademik';
                            break;
                        case 'Surat Lolos Turnitin':
                            $fieldName = 'surat_lolos_turnitin';
                            break;
                        case 'Sertifikat TOEFL':
                            $fieldName = 'sertifikat_toefl';
                            break;
                        case 'Pas Foto':
                            $fieldName = 'pas_foto';
                            break;
                        case 'Buku Bimbingan':
                            $fieldName = 'buku_bimbingan';
                            break;
                        case 'Surat Perbaikan':
                            $fieldName = 'surat_perbaikan';
                            break;
                        case 'Surat Ijin Sidang':
                            $fieldName = 'surat_ijin_sidang';
                            break;
                        case 'Berkas Skripsi':
                            $fieldName = 'berkas_skripsi';
                            break;
                    }
                @endphp
                setupFileUpload('{{ $fieldName }}', 'file-info-{{ $fieldName }}');
            @endforeach

            document.getElementById('skripsiForm')?.addEventListener('submit', function() {
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="loading-spinner me-2"></span> Mengupload...';
            });
        </script>
    @endpush
@endsection
