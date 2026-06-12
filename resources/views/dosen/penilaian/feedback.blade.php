@extends('layouts.app')

@section('title', 'Lembar Feedback Skripsi')

@section('content')
    <style>
        .feedback-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .feedback-title {
            font-size: 16px;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
        }

        .feedback-textarea {
            width: 100%;
            resize: vertical;
        }
    </style>

    <div class="container-fluid">
        <div class="card fade-in border-0 shadow-sm">
            <div class="card-header-custom">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Lembar Feedback Skripsi
                </h5>
            </div>
            <div class="card-body">
                <!-- Informasi Sidang -->
                <div class="feedback-section">
                    <div class="feedback-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Informasi Sidang
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nama Penguji</strong></td>
                                    <td>: {{ $feedback->nama_dosen }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jadwal Seminar</strong></td>
                                    <td>: {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }},
                                        {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Ruang</strong></td>
                                    <td>: {{ $jadwal->ruang ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nama Mahasiswa</strong></td>
                                    <td>: {{ $jadwal->pendaftaran->mahasiswa->user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>NPM</strong></td>
                                    <td>: {{ $jadwal->pendaftaran->mahasiswa->npm }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Judul Skripsi</strong></td>
                                    <td>: {{ $jadwal->pendaftaran->judul_skripsi }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <form action="{{ route('dosen.penilaian.feedback.store', $feedback->id) }}" method="POST">
                    @csrf

                    <!-- Catatan Perbaikan per BAB -->
                    <div class="feedback-section">
                        <div class="feedback-title">
                            <i class="bi bi-chat-text me-2"></i>
                            Catatan Perbaikan Skripsi
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th style="width: 30%">Komponen</th>
                                        <th style="width: 65%">Catatan Feedback</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td><strong>BAGIAN DEPAN</strong><br><small>(Cover, Judul, Lembar Pengesahan, Motto,
                                                Kata Pengantar, Daftar Isi/Tabel/Gambar/Lampiran, Abstrak)</small></td>
                                        <td>
                                            <textarea name="catatan_bagian_depan" class="form-control feedback-textarea" rows="2"
                                                {{ $isReadOnly ? 'readonly disabled' : '' }}>{{ old('catatan_bagian_depan', $feedback->catatan_bagian_depan ?? '') }}</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">2</td>
                                        <td><strong>BAB I</strong><br><small>(Pendahuluan)</small></td>
                                        <td>
                                            <textarea name="catatan_bab1" class="form-control feedback-textarea" rows="2"
                                                {{ $isReadOnly ? 'readonly disabled' : '' }}>{{ old('catatan_bab1', $feedback->catatan_bab1 ?? '') }}</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">3</td>
                                        <td><strong>BAB II</strong><br><small>(Landasan Teoritis)</small></td>
                                        <td>
                                            <textarea name="catatan_bab2" class="form-control feedback-textarea" rows="2"
                                                {{ $isReadOnly ? 'readonly disabled' : '' }}>{{ old('catatan_bab2', $feedback->catatan_bab2 ?? '') }}</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">4</td>
                                        <td><strong>BAB III</strong><br><small>(Metode Penelitian)</small></td>
                                        <td>
                                            <textarea name="catatan_bab3" class="form-control feedback-textarea" rows="2"
                                                {{ $isReadOnly ? 'readonly disabled' : '' }}>{{ old('catatan_bab3', $feedback->catatan_bab3 ?? '') }}</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">5</td>
                                        <td><strong>BAB IV</strong><br><small>(Hasil dan Pembahasan)</small></td>
                                        <td>
                                            <textarea name="catatan_bab4" class="form-control feedback-textarea" rows="2"
                                                {{ $isReadOnly ? 'readonly disabled' : '' }}>{{ old('catatan_bab4', $feedback->catatan_bab4 ?? '') }}</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">6</td>
                                        <td><strong>BAB V</strong><br><small>(Kesimpulan dan Saran)</small></td>
                                        <td>
                                            <textarea name="catatan_bab5" class="form-control feedback-textarea" rows="2"
                                                {{ $isReadOnly ? 'readonly disabled' : '' }}>{{ old('catatan_bab5', $feedback->catatan_bab5 ?? '') }}</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">7</td>
                                        <td><strong>DAFTAR PUSTAKA</strong></td>
                                        <td>
                                            <textarea name="catatan_daftar_pustaka" class="form-control feedback-textarea" rows="2"
                                                {{ $isReadOnly ? 'readonly disabled' : '' }}>{{ old('catatan_daftar_pustaka', $feedback->catatan_daftar_pustaka ?? '') }}</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">8</td>
                                        <td><strong>PRESENTASI</strong><br><small>(Verbalisasi & Argumentasi)</small></td>
                                        <td>
                                            <textarea name="catatan_presentasi" class="form-control feedback-textarea" rows="2"
                                                {{ $isReadOnly ? 'readonly disabled' : '' }}>{{ old('catatan_presentasi', $feedback->catatan_presentasi ?? '') }}</textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- File Skripsi (Link untuk lihat) -->
                    <div class="feedback-section">
                        <div class="feedback-title">
                            <i class="bi bi-paperclip me-2"></i>
                            Dokumen Skripsi
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">File Skripsi Mahasiswa</label>
                                <div>
                                    @php
                                        $berkasSkripsi = $jadwal->pendaftaran->dokumen
                                            ->where('jenis_dokumen', 'berkas_skripsi')
                                            ->first();
                                    @endphp
                                    @if ($berkasSkripsi)
                                        <a href="{{ Storage::url($berkasSkripsi->file_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-file-pdf"></i> Lihat File Skripsi
                                        </a>
                                    @else
                                        <span class="text-muted">Tidak ada file skripsi</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rekomendasi -->
                    <div class="feedback-section">
                        <div class="feedback-title">
                            <i class="bi bi-chat-dots me-2"></i>
                            Rekomendasi / Perbaikan
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Rekomendasi</label>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="perbaikan_minor" class="form-check-input"
                                            id="minorCheck"
                                            {{ old('perbaikan_minor', $feedback->perbaikan_minor) ? 'checked' : '' }}
                                            {{ $isReadOnly ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="minorCheck">Perbaikan Minor</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="perbaikan_mayor" class="form-check-input"
                                            id="mayorCheck"
                                            {{ old('perbaikan_mayor', $feedback->perbaikan_mayor) ? 'checked' : '' }}
                                            {{ $isReadOnly ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="mayorCheck">Perbaikan Mayor</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Catatan Perbaikan Umum</label>
                                <textarea name="catatan_perbaikan" class="form-control feedback-textarea" rows="4"
                                    {{ $isReadOnly ? 'readonly disabled' : '' }}>{{ old('catatan_perbaikan', $feedback->catatan_perbaikan) }}</textarea>
                            </div>
                        </div>
                    </div>

                    @if (!$isReadOnly)
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('dosen.penilaian.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Feedback
                            </button>
                        </div>
                    @else
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('dosen.penilaian.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <a href="{{ route('dosen.penilaian.create', $jadwal->id) }}" class="btn btn-success">
                                <i class="bi bi-clipboard-check"></i> Lanjut ke Penilaian Sidang
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection
