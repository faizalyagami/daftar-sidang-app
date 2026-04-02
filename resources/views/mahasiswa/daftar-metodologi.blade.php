@extends('layouts.app')

@section('title', 'Daftar Ujian Metodologi Penelitian')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <!-- Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="mb-1">
                            <i class="bi bi-book me-2 text-primary"></i>
                            Formulir Pendaftaran Ujian Metodologi Penelitian
                        </h4>
                        <p class="text-muted mb-0">Silakan isi formulir berikut dengan lengkap</p>
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

        <!-- Cek Apakah Pendaftaran Dibuka -->
        @if(!$activePeriod || !$activePeriod->isMetodologiRegistrationOpen())
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center">
                <div class="alert-closed">
                    <i class="bi bi-exclamation-triangle-fill fs-1 text-danger mb-3 d-block"></i>
                    <h4 class="text-danger mb-3">Pendaftaran Ujian Metodologi Sedang Ditutup</h4>
                    <p class="text-muted mb-0">
                        @if(!$activePeriod)
                        Tidak ada periode akademik yang aktif. Silakan hubungi admin.
                        @else
                        Pendaftaran ujian metodologi untuk periode <strong>{{ $activePeriod->semester }} {{ $activePeriod->tahun_akademik }}</strong> sedang ditutup.
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
        <!-- Form Pendaftaran (hanya tampil jika dibuka) -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('mahasiswa.store-metodologi') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly disabled>
                            <small class="text-muted">Data diambil dari SIAKAD</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">NPM</label>
                            <input type="text" class="form-control" value="{{ $mahasiswa->npm }}" readonly disabled>
                            <small class="text-muted">Data diambil dari SIAKAD</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">No Handphone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('no_hp') is-invalid @enderror"
                                name="no_hp" value="{{ old('no_hp', $mahasiswa->no_hp) }}" required>
                            @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Judul Penelitian <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('judul_penelitian') is-invalid @enderror"
                                name="judul_penelitian" rows="3" required>{{ old('judul_penelitian') }}</textarea>
                            @error('judul_penelitian')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dosen Pembimbing 1 (dropdown searchable) -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dosen Pembimbing 1 <span class="text-danger">*</span></label>
                            <select class="form-select select2-dosen @error('dosen_pembimbing') is-invalid @enderror"
                                    name="dosen_pembimbing"
                                    style="width: 100%;"
                                    required>
                                <option value="">-- Cari Dosen Pembimbing 1 --</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->name }}" {{ old('dosen_pembimbing') == $dosen->name ? 'selected' : '' }}>
                                        {{ $dosen->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dosen_pembimbing')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dosen Pembimbing 2 (dropdown searchable) -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dosen Pembimbing 2 (Jika Ada)</label>
                            <select class="form-select select2-dosen @error('dosen_pembimbing_2') is-invalid @enderror"
                                    name="dosen_pembimbing_2"
                                    style="width: 100%;">
                                <option value="">-- Cari Dosen Pembimbing 2 --</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->name }}" {{ old('dosen_pembimbing_2') == $dosen->name ? 'selected' : '' }}>
                                        {{ $dosen->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dosen_pembimbing_2')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Kuliah Peminatan <span class="text-danger">*</span></label>
                            <select class="form-select @error('kuliah_peminatan') is-invalid @enderror"
                                name="kuliah_peminatan" required>
                                <option value="">Pilih Peminatan</option>
                                <option value="Psikologi Klinis" {{ old('kuliah_peminatan') == 'Psikologi Klinis' ? 'selected' : '' }}>Psikologi Klinis</option>
                                <option value="Psikologi Pendidikan" {{ old('kuliah_peminatan') == 'Psikologi Pendidikan' ? 'selected' : '' }}>Psikologi Pendidikan</option>
                                <option value="Psikologi Industri dan Organisasi" {{ old('kuliah_peminatan') == 'Psikologi Industri dan Organisasi' ? 'selected' : '' }}>Psikologi Industri dan Organisasi</option>
                                <option value="Psikologi Sosial" {{ old('kuliah_peminatan') == 'Psikologi Sosial' ? 'selected' : '' }}>Psikologi Sosial</option>
                                <option value="Psikologi Perkembangan" {{ old('kuliah_peminatan') == 'Psikologi Perkembangan' ? 'selected' : '' }}>Psikologi Perkembangan</option>
                            </select>
                            @error('kuliah_peminatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3">Dokumen Persyaratan</h5>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Pastikan dokumen yang diupload sudah lengkap dan sesuai dengan ketentuan.
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Berkas Proposal (Format Word) <span class="text-danger">*</span></label>
                            <small class="text-muted d-block">Format: .doc atau .docx (Max 5MB)</small>
                            <input type="file" class="form-control @error('proposal_word') is-invalid @enderror"
                                name="proposal_word" accept=".doc,.docx" required>
                            @error('proposal_word')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kartu Bimbingan <span class="text-danger">*</span></label>
                            <small class="text-muted d-block">Format: PDF (Max 2MB)</small>
                            <input type="file" class="form-control @error('kartu_bimbingan') is-invalid @enderror"
                                name="kartu_bimbingan" accept=".pdf" required>
                            @error('kartu_bimbingan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pernyataan Ijin Mengikuti Ujian Metpen <span class="text-danger">*</span></label>
                            <small class="text-muted d-block">Format: PDF (Max 2MB)</small>
                            <input type="file" class="form-control @error('surat_ijin_ujian') is-invalid @enderror"
                                name="surat_ijin_ujian" accept=".pdf" required>
                            @error('surat_ijin_ujian')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lembar Pengesahan (Ttd Pembimbing) <span class="text-danger">*</span></label>
                            <small class="text-muted d-block">Format: PDF (Max 2MB)</small>
                            <input type="file" class="form-control @error('lembar_pengesahan') is-invalid @enderror"
                                name="lembar_pengesahan" accept=".pdf" required>
                            @error('lembar_pengesahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-warning mt-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="persetujuan" required>
                            <label class="form-check-label" for="persetujuan">
                                <strong>Pernyataan:</strong> Dengan ini saya menyatakan bahwa semua data dan dokumen yang saya upload adalah benar dan asli. Saya bersedia menerima sanksi akademik apabila dikemudian hari ditemukan data palsu atau pemalsuan dokumen.
                            </label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Submit Pendaftaran
                        </button>
                        <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-fill email from user data
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.querySelector('input[name="email"]');
        const userEmail = "{{ Auth::user()->email }}";
        if (emailInput && userEmail && !emailInput.value) {
            emailInput.value = userEmail;
        }
    });

    // Inisialisasi Select2
    $(document).ready(function() {
        $('.select2-dosen').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Ketik nama dosen...',
            allowClear: true,
            language: {
                noResults: function() { return 'Dosen tidak ditemukan. Hubungi admin.'; },
                searching: function() { return 'Mencari...'; }
            }
        });
    });
</script>
@endpush