@extends('layouts.app')

@section('title', 'Detail Pendaftaran Skripsi')

@section('content')
    <style>
        .detail-section {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .detail-title {
            font-size: 16px;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        .detail-label {
            font-size: 13px;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 14px;
            color: #2d3748;
            margin-bottom: 15px;
            word-break: break-word;
        }

        .document-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 12px;
            transition: all 0.2s;
            border-left: 4px solid #667eea;
            position: relative;
        }

        .document-card:hover {
            transform: translateX(5px);
            background: #f0f4ff;
        }

        .document-card.revision {
            border-left-color: #dc3545;
            background: #fff5f5;
        }

        .document-card.revision:hover {
            background: #ffe0e0;
        }

        .revision-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 20px;
            font-weight: 600;
        }

        .document-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .document-date {
            font-size: 11px;
            color: #adb5bd;
        }

        .btn-back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px;
            border-radius: 12px;
            color: white;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .btn-revision {
            background: #dc3545;
            border: none;
            padding: 10px;
            border-radius: 12px;
            color: white;
            font-weight: 500;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-revision:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .revision-note {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 13px;
        }

        .revision-note strong {
            color: #856404;
        }
    </style>

    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="detail-section">
                    <div class="detail-title">
                        <i class="bi bi-file-earmark-text me-2"></i>Informasi Pendaftaran
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                <span class="status-badge status-{{ $pendaftaran->status }}">
                                    @if ($pendaftaran->status == 'revision')
                                        ⚠️ Perlu Revisi
                                    @else
                                        {{ ucfirst($pendaftaran->status) }}
                                    @endif
                                </span>
                            </div>

                            <div class="detail-label">Tanggal Pendaftaran</div>
                            <div class="detail-value">{{ $pendaftaran->created_at->format('d F Y H:i') }}</div>

                            <div class="detail-label">Nama Mahasiswa</div>
                            <div class="detail-value">{{ $pendaftaran->mahasiswa->user->name }}</div>

                            <div class="detail-label">NPM</div>
                            <div class="detail-value">{{ $pendaftaran->mahasiswa->npm }}</div>

                            <div class="detail-label">Dosen Pembimbing</div>
                            <div class="detail-value">{{ $pendaftaran->dosen_pembimbing }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Judul Skripsi</div>
                            <div class="detail-value">{{ $pendaftaran->judul_skripsi }}</div>

                            @if ($pendaftaran->narasumber)
                                <div class="detail-label">Narasumber Seminar</div>
                                <div class="detail-value">{{ $pendaftaran->narasumber }}</div>
                            @endif

                            @if ($pendaftaran->tanggal_seminar)
                                <div class="detail-label">Tanggal Seminar</div>
                                <div class="detail-value">
                                    {{ \Carbon\Carbon::parse($pendaftaran->tanggal_seminar)->format('d F Y') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($pendaftaran->reviewer_notes)
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-chat-dots me-2"></i>
                            <strong>Catatan Reviewer:</strong><br>
                            {{ $pendaftaran->reviewer_notes }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="detail-section">
                    <div class="detail-title">
                        <i class="bi bi-files me-2"></i>Dokumen yang Diupload
                        @if ($pendaftaran->status == 'revision')
                            <span class="badge bg-danger ms-2">Perlu Revisi</span>
                        @endif
                    </div>

                    @php
                        // Ambil daftar dokumen yang perlu direvisi
                        $revisiList = [];
                        if ($pendaftaran->status == 'revision') {
                            $revisiList = $pendaftaran->getCatatanRevisi();
                        }

                        $revisiMap = [];
                        foreach ($revisiList as $r) {
                            $revisiMap[$r['dokumen']] = $r['komentar'];
                        }
                    @endphp

                    @forelse($pendaftaran->dokumen as $dokumen)
                        @php
                            $namaDokumen = '';
                            switch ($dokumen->jenis_dokumen) {
                                case 'bukti_pembayaran_registrasi':
                                    $namaDokumen = 'Bukti Pembayaran Registrasi Terakhir';
                                    break;
                                case 'bukti_pembayaran_sidang':
                                    $namaDokumen = 'Bukti Pembayaran Sidang';
                                    break;
                                case 'bukti_pembayaran_skripsi':
                                    $namaDokumen = 'Bukti Pembayaran Skripsi';
                                    break;
                                case 'frs':
                                    $namaDokumen = 'Formulir Rencana Studi (FRS)';
                                    break;
                                case 'transkrip_nilai':
                                    $namaDokumen = 'Transkrip Nilai';
                                    break;
                                case 'surat_bebas_perpus':
                                    $namaDokumen = 'Surat Bebas Perpustakaan';
                                    break;
                                case 'surat_bebas_alat_tes':
                                    $namaDokumen = 'Surat Bebas Alat Tes';
                                    break;
                                case 'sertifikat_pesantren':
                                    $namaDokumen = 'Sertifikat Pesantren';
                                    break;
                                case 'sertifikat_sks_non_akademik':
                                    $namaDokumen = 'Sertifikat SKS Non Akademik';
                                    break;
                                case 'surat_lolos_turnitin':
                                    $namaDokumen = 'Surat Lolos Turnitin';
                                    break;
                                case 'sertifikat_toefl':
                                    $namaDokumen = 'Sertifikat TOEFL';
                                    break;
                                case 'pas_foto':
                                    $namaDokumen = 'Pas Foto';
                                    break;
                                case 'buku_bimbingan':
                                    $namaDokumen = 'Buku Bimbingan';
                                    break;
                                case 'surat_perbaikan':
                                    $namaDokumen = 'Surat Perbaikan';
                                    break;
                                case 'surat_ijin_sidang':
                                    $namaDokumen = 'Surat Ijin Sidang';
                                    break;
                                case 'berkas_skripsi':
                                    $namaDokumen = 'Berkas Skripsi';
                                    break;
                                default:
                                    $namaDokumen = str_replace('_', ' ', ucfirst($dokumen->jenis_dokumen));
                            }
                            $isRevisi = isset($revisiMap[$namaDokumen]);
                            $komentarRevisi = $isRevisi ? $revisiMap[$namaDokumen] : '';
                        @endphp
                        <a href="{{ Storage::url($dokumen->file_path) }}" target="_blank" class="text-decoration-none">
                            <div class="document-card {{ $isRevisi ? 'revision' : '' }}">
                                @if ($isRevisi)
                                    <span class="revision-badge">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Perbaiki
                                    </span>
                                @endif
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="document-name">
                                            @switch($dokumen->jenis_dokumen)
                                                @case('bukti_pembayaran_registrasi')
                                                    <i class="bi bi-receipt me-1"></i> Bukti Pembayaran Registrasi Terakhir
                                                @break

                                                @case('bukti_pembayaran_sidang')
                                                    <i class="bi bi-cash-stack me-1"></i> Bukti Pembayaran Sidang
                                                @break

                                                @case('bukti_pembayaran_skripsi')
                                                    <i class="bi bi-credit-card me-1"></i> Bukti Pembayaran Skripsi
                                                @break

                                                @case('frs')
                                                    <i class="bi bi-table me-1"></i> Formulir Rencana Studi (FRS)
                                                @break

                                                @case('transkrip_nilai')
                                                    <i class="bi bi-file-text me-1"></i> Transkrip Nilai
                                                @break

                                                @case('surat_bebas_perpus')
                                                    <i class="bi bi-building me-1"></i> Surat Bebas Perpustakaan
                                                @break

                                                @case('surat_bebas_alat_tes')
                                                    <i class="bi bi-flask me-1"></i> Surat Bebas Alat Tes
                                                @break

                                                @case('sertifikat_pesantren')
                                                    <i class="bi bi-mosque me-1"></i> Sertifikat Pesantren
                                                @break

                                                @case('sertifikat_sks_non_akademik')
                                                    <i class="bi bi-award me-1"></i> Sertifikat SKS Non Akademik
                                                @break

                                                @case('surat_lolos_turnitin')
                                                    <i class="bi bi-percent me-1"></i> Surat Lolos Turnitin
                                                @break

                                                @case('sertifikat_toefl')
                                                    <i class="bi bi-globe me-1"></i> Sertifikat TOEFL
                                                @break

                                                @case('pas_foto')
                                                    <i class="bi bi-image me-1"></i> Pas Foto
                                                @break

                                                @case('buku_bimbingan')
                                                    <i class="bi bi-book me-1"></i> Buku Bimbingan
                                                @break

                                                @case('surat_perbaikan')
                                                    <i class="bi bi-pencil-square me-1"></i> Surat Perbaikan
                                                @break

                                                @case('surat_ijin_sidang')
                                                    <i class="bi bi-file-check me-1"></i> Surat Ijin Sidang
                                                @break

                                                @case('berkas_skripsi')
                                                    <i class="bi bi-file-pdf me-1 text-danger"></i> Berkas Skripsi
                                                @break

                                                default:
                                                <i class="bi bi-file-earmark me-1"></i>
                                                {{ str_replace('_', ' ', ucfirst($dokumen->jenis_dokumen)) }}
                                            @endswitch
                                        </div>
                                        <div class="document-date">
                                            <i class="bi bi-clock"></i>
                                            {{ \Carbon\Carbon::parse($dokumen->created_at)->format('d/m/Y H:i') }}
                                        </div>
                                        @if ($isRevisi)
                                            <div class="revision-note mt-2">
                                                <i class="bi bi-chat-dots me-1"></i>
                                                <strong>Catatan Revisi:</strong> {{ $komentarRevisi }}
                                            </div>
                                        @endif
                                    </div>
                                    <i class="bi bi-download text-primary"></i>
                                </div>
                            </div>
                        </a>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2 mb-0">Tidak ada dokumen</p>
                            </div>
                        @endforelse
                    </div>

                    @if ($pendaftaran->status == 'revision')
                        <div class="detail-section">
                            <div class="detail-title">
                                <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>
                                Tindakan yang Diperlukan
                            </div>
                            <div class="alert alert-danger mb-3">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Dokumen yang diberi tanda <strong class="text-danger">"Perbaiki"</strong> wajib diupload ulang
                                sesuai catatan reviewer.
                            </div>
                            @php
                                $pendaftaranId = $pendaftaran->id;
                            @endphp
                            <a href="{{ route('mahasiswa.edit-skripsi', $pendaftaranId) }}" class="btn btn-revision w-100">
                                <i class="bi bi-upload"></i> Upload Ulang Dokumen Revisi
                            </a>
                        </div>
                    @endif

                    <div class="detail-section">
                        <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-back w-100">
                            <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endsection
