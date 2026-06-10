@extends('layouts.app')

@section('title', 'Daftar Sidang Skripsi')

@section('content')
    <style>
        .form-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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

        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #fff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .is-invalid-file {
            border-color: #dc3545 !important;
            background-color: #fff0f0 !important;
        }

        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
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
                                @if ($activePeriod)
                                    {{ $activePeriod->semester }} {{ $activePeriod->tahun_akademik }}
                                @else
                                    Belum diatur
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tampilkan Error Global -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="bi bi-exclamation-triangle-fill"></i> Terjadi Kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Cek Apakah Pendaftaran Dibuka -->
                @if (!$activePeriod || !$activePeriod->isSkripsiRegistrationOpen())
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5 text-center">
                            <div class="alert-closed">
                                <i class="bi bi-exclamation-triangle-fill fs-1 text-danger mb-3 d-block"></i>
                                <h4 class="text-danger mb-3">Pendaftaran Sidang Skripsi Sedang Ditutup</h4>
                                <p class="text-muted mb-0">
                                    @if (!$activePeriod)
                                        Tidak ada periode akademik yang aktif. Silakan hubungi admin.
                                    @else
                                        Pendaftaran sidang skripsi untuk periode <strong>{{ $activePeriod->semester }}
                                            {{ $activePeriod->tahun_akademik }}</strong> sedang ditutup.
                                    @endif
                                </p>
                                <p class="text-muted mt-2">
                                    Untuk informasi lebih lanjut, silakan hubungi bagian akademik atau admin sistem.
                                </p>
                                <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Form Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <form action="{{ route('mahasiswa.store-skripsi') }}" method="POST"
                                enctype="multipart/form-data" id="skripsiForm" novalidate>
                                @csrf

                                <!-- Data Pendaftaran -->
                                <div class="form-section">
                                    <h5>
                                        <i class="bi bi-info-circle me-2"></i>
                                        Data Pendaftaran
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email Address <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                name="email" value="{{ old('email', Auth::user()->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nama Lengkap</label>
                                            <input type="text" class="form-control" value="{{ Auth::user()->name }}"
                                                readonly disabled>
                                            <small class="text-muted">Data diambil dari SIAKAD</small>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">NPM</label>
                                            <input type="text" class="form-control" value="{{ $mahasiswa->npm ?? '-' }}"
                                                readonly disabled>
                                            <small class="text-muted">Data diambil dari SIAKAD</small>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">No Handphone <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('no_hp') is-invalid @enderror"
                                                name="no_hp" value="{{ old('no_hp', $mahasiswa->no_hp ?? '') }}"
                                                required>
                                            @error('no_hp')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-semibold required-field">
                                                Judul Skripsi
                                            </label>
                                            <textarea class="form-control @error('judul_skripsi') is-invalid @enderror" name="judul_skripsi" rows="3"
                                                placeholder="Masukkan judul skripsi dengan lengkap dan jelas" required>{{ old('judul_skripsi') }}</textarea>
                                            <div class="file-info">
                                                <i class="bi bi-info-circle"></i> Gunakan huruf kapital di awal setiap kata
                                            </div>
                                            @error('judul_skripsi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Dosen Pembimbing -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold required-field">Dosen Pembimbing 1</label>
                                            <select
                                                class="form-select select2-dosen @error('dosen_pembimbing') is-invalid @enderror"
                                                name="dosen_pembimbing" style="width: 100%;" required>
                                                <option value="">-- Cari Dosen Pembimbing --</option>
                                                @foreach ($dosens as $dosen)
                                                    <option value="{{ $dosen->name }}"
                                                        {{ old('dosen_pembimbing') == $dosen->name ? 'selected' : '' }}>
                                                        {{ $dosen->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('dosen_pembimbing')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">Dosen Pembimbing 2</label>
                                            <select
                                                class="form-select select2-dosen @error('dosen_pembimbing_2') is-invalid @enderror"
                                                name="dosen_pembimbing_2" style="width: 100%;">
                                                <option value="">-- Cari Dosen Pembimbing 2 --</option>
                                                @foreach ($dosens as $dosen)
                                                    <option value="{{ $dosen->name }}"
                                                        {{ old('dosen_pembimbing_2') == $dosen->name ? 'selected' : '' }}>
                                                        {{ $dosen->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('dosen_pembimbing_2')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold">Narasumber Seminar Skripsi</label>
                                            <select
                                                class="form-select select2-dosen @error('narasumber') is-invalid @enderror"
                                                name="narasumber" style="width: 100%;">
                                                <option value="">-- Cari Narasumber --</option>
                                                @foreach ($dosens as $dosen)
                                                    <option value="{{ $dosen->name }}"
                                                        {{ old('narasumber') == $dosen->name ? 'selected' : '' }}>
                                                        {{ $dosen->name }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                                                name="tanggal_seminar" value="{{ old('tanggal_seminar') }}">
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
                                        Format file yang diterima: PDF, JPG, JPEG, PNG (Max 2MB per file, kecuali Berkas
                                        Skripsi Max 10MB)
                                    </div>

                                    <div class="row">
                                        @php
                                            $dokumenFields = [
                                                'bukti_pembayaran_registrasi' => 'Bukti Pembayaran Registrasi Terakhir',
                                                'bukti_pembayaran_sidang' => 'Bukti Pembayaran Biaya Sidang',
                                                'bukti_pembayaran_skripsi' => 'Bukti Pembayaran Skripsi',
                                                'frs' => 'Formulir Rencana Studi (FRS)',
                                                'transkrip_nilai' => 'Transkrip Nilai Terakhir',
                                                'surat_bebas_perpus' => 'Surat Bebas Perpustakaan',
                                                'surat_bebas_alat_tes' => 'Surat Bebas Peminjaman Alat Tes',
                                                'sertifikat_pesantren' => 'Sertifikat Pesantren Calon Sarjana',
                                                'sertifikat_sks_non_akademik' => 'Sertifikat SKS Non Akademik',
                                                'surat_lolos_turnitin' => 'Surat Lolos Turn It In',
                                                'sertifikat_toefl' => 'Sertifikat TOEFL',
                                                'pas_foto' => 'Pas Foto',
                                                'buku_bimbingan' => 'Buku Bimbingan',
                                                'surat_perbaikan' => 'Surat Perbaikan Hasil Seminar Skripsi',
                                                'surat_ijin_sidang' => 'Surat Pernyataan Ijin Mengikuti Sidang Skripsi',
                                                'berkas_skripsi' => 'Berkas Skripsi Softcopy',
                                            ];
                                        @endphp

                                        @foreach ($dokumenFields as $field => $label)
                                            <div class="{{ $field == 'berkas_skripsi' ? 'col-md-12' : 'col-md-6' }} mb-3">
                                                <label class="form-label fw-semibold required-field">
                                                    {{ $label }}
                                                </label>
                                                <div class="upload-area" id="upload-area-{{ $field }}"
                                                    onclick="document.getElementById('{{ $field }}').click()">
                                                    <i class="bi bi-cloud-upload"></i>
                                                    <div>Klik atau drag file untuk upload</div>
                                                    <small class="text-muted">
                                                        @if ($field == 'berkas_skripsi')
                                                            PDF (Max 10MB)
                                                        @elseif($field == 'pas_foto')
                                                            JPG, JPEG, PNG (Max 2MB)
                                                        @else
                                                            PDF, JPG, JPEG, PNG (Max 2MB)
                                                        @endif
                                                    </small>
                                                </div>
                                                <input type="file"
                                                    class="form-control d-none @error($field) is-invalid @enderror"
                                                    id="{{ $field }}" name="{{ $field }}"
                                                    accept="{{ $field == 'pas_foto' ? '.jpg,.jpeg,.png' : '.pdf,.jpg,.jpeg,.png' }}">
                                                <div class="file-info" id="file-info-{{ $field }}"></div>
                                                <div class="error-message" id="error-{{ $field }}"></div>
                                                @error($field)
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Pernyataan -->
                                <div class="form-section">
                                    <div class="alert alert-warning">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="persetujuan" required>
                                            <label class="form-check-label" for="persetujuan">
                                                <strong>Pernyataan:</strong> Dengan ini saya menyatakan bahwa dokumen
                                                pendaftaran
                                                SIDANG SKRIPSI yang saya ajukan adalah ASLI dan SUDAH MENDAPAT PERSETUJUAN
                                                DOSEN
                                                PEMBIMBING SKRIPSI. Apabila di kemudian hari ditemukan dokumen yang saya
                                                ajukan
                                                palsu atau tanpa seizin dosen pembimbing, SAYA BERSEDIA MENERIMA KONSEKUENSI
                                                yang
                                                ditentukan Fakultas Psikologi Universitas Islam Bandung.
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
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('.select2-dosen').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Ketik nama dosen...',
                allowClear: true,
            });
        });

        // Daftar semua field file yang diperlukan
        const fileFields = [
            'bukti_pembayaran_registrasi', 'bukti_pembayaran_sidang', 'bukti_pembayaran_skripsi',
            'frs', 'transkrip_nilai', 'surat_bebas_perpus', 'surat_bebas_alat_tes',
            'sertifikat_pesantren', 'sertifikat_sks_non_akademik', 'surat_lolos_turnitin',
            'sertifikat_toefl', 'pas_foto', 'buku_bimbingan', 'surat_perbaikan',
            'surat_ijin_sidang', 'berkas_skripsi'
        ];

        // File upload preview dan validasi
        fileFields.forEach(function(field) {
            const input = document.getElementById(field);
            const info = document.getElementById('file-info-' + field);
            const uploadArea = document.getElementById('upload-area-' + field);
            const errorDiv = document.getElementById('error-' + field);

            if (input && info) {
                input.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const fileName = this.files[0].name;
                        const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2);
                        info.innerHTML =
                            `<i class="bi bi-check-circle-fill text-success"></i> ${fileName} (${fileSize} MB)`;
                        info.style.color = '#28a745';
                        if (uploadArea) {
                            uploadArea.classList.remove('is-invalid-file');
                        }
                        if (errorDiv) {
                            errorDiv.innerHTML = '';
                        }
                    } else {
                        info.innerHTML = '';
                        if (uploadArea) {
                            uploadArea.classList.remove('is-invalid-file');
                        }
                    }
                });
            }
        });

        // Form submit dengan validasi manual
        const form = document.getElementById('skripsiForm');
        const submitBtn = document.getElementById('submitBtn');
        const checkbox = document.getElementById('persetujuan');

        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                console.log('Form submit triggered');

                let hasError = false;
                let errorMessages = [];

                // Validasi checkbox
                if (checkbox && !checkbox.checked) {
                    hasError = true;
                    errorMessages.push('Anda harus menyetujui pernyataan terlebih dahulu.');
                    checkbox.focus();
                }

                // Validasi file upload (manual, tanpa required attribute)
                const missingFiles = [];
                fileFields.forEach(function(field) {
                    const fileInput = document.getElementById(field);
                    const uploadArea = document.getElementById('upload-area-' + field);
                    const errorDiv = document.getElementById('error-' + field);

                    if (fileInput && (!fileInput.files || fileInput.files.length === 0)) {
                        missingFiles.push(field.replace(/_/g, ' ').toUpperCase());
                        if (uploadArea) {
                            uploadArea.classList.add('is-invalid-file');
                        }
                        if (errorDiv) {
                            errorDiv.innerHTML = 'File ini wajib diupload';
                        }
                    } else if (uploadArea) {
                        uploadArea.classList.remove('is-invalid-file');
                        if (errorDiv) {
                            errorDiv.innerHTML = '';
                        }
                    }
                });

                if (missingFiles.length > 0) {
                    hasError = true;
                    errorMessages.push('Dokumen yang wajib diupload: ' + missingFiles.join(', '));
                }

                // Jika ada error
                if (hasError) {
                    e.preventDefault();
                    alert(errorMessages.join('\n'));
                    console.log('Validation failed:', errorMessages);
                    return false;
                }

                console.log('Validation passed, submitting form...');

                // Loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="loading-spinner me-2"></span> Memproses...';

                return true;
            });
        }

        // Drag and drop
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

                // Get file input ID from onclick attribute
                const onclickAttr = this.getAttribute('onclick');
                if (onclickAttr) {
                    const match = onclickAttr.match(/document\.getElementById\('([^']+)'\)/);
                    if (match && match[1]) {
                        const input = document.getElementById(match[1]);
                        if (input && input.type === 'file') {
                            input.files = e.dataTransfer.files;
                            const event = new Event('change');
                            input.dispatchEvent(event);
                        }
                    }
                }
            });
        });

        console.log('Page loaded, form submit handler attached');
        console.log('Form action:', form ? form.action : 'Form not found');
    </script>
@endpush
