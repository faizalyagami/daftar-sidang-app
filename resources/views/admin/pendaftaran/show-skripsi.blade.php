@extends('layouts.app')

@section('title', 'Detail Pendaftaran Skripsi')

@section('content')
    <style>
        .detail-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .detail-title {
            font-size: 16px;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
        }

        .detail-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
            font-size: 13px;
        }

        .detail-value {
            color: #333;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .status-badge-large {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
        }

        .document-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s;
            border-left: 4px solid #667eea;
        }

        .document-card:hover {
            transform: translateX(5px);
            background: #f0f4ff;
        }

        .document-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .document-file {
            font-size: 12px;
            color: #666;
            word-break: break-all;
        }

        .btn-download {
            background: #667eea;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 8px;
            font-size: 12px;
            transition: all 0.3s;
        }

        .btn-download:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .btn-action {
            padding: 10px 25px;
            border-radius: 10px;
            font-weight: 600;
            margin: 0 5px;
        }

        @media (max-width: 768px) {
            .btn-action {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Informasi Mahasiswa -->
                <div class="detail-section">
                    <div class="detail-title">
                        <i class="bi bi-person-badge me-2"></i>
                        Informasi Mahasiswa
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-label">Nama Lengkap</div>
                            <div class="detail-value">
                                <strong>{{ $pendaftaran->mahasiswa->user->name }}</strong>
                            </div>

                            <div class="detail-label">NPM</div>
                            <div class="detail-value">{{ $pendaftaran->mahasiswa->npm }}</div>

                            <div class="detail-label">Email</div>
                            <div class="detail-value">{{ $pendaftaran->mahasiswa->user->email }}</div>

                            <div class="detail-label">No Handphone</div>
                            <div class="detail-value">{{ $pendaftaran->mahasiswa->no_hp ?: '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Dosen Wali</div>
                            <div class="detail-value">{{ $pendaftaran->mahasiswa->dosen_wali ?: '-' }}</div>

                            <div class="detail-label">IPK Terakhir</div>
                            <div class="detail-value">
                                {{ $pendaftaran->mahasiswa->ipk ? number_format($pendaftaran->mahasiswa->ipk, 3) : '-' }}
                            </div>

                            <div class="detail-label">Tempat/Tanggal Lahir</div>
                            <div class="detail-value">
                                @if ($pendaftaran->mahasiswa->tempat_lahir && $pendaftaran->mahasiswa->tanggal_lahir)
                                    {{ $pendaftaran->mahasiswa->tempat_lahir }},
                                    {{ $pendaftaran->mahasiswa->tanggal_lahir->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Pendaftaran -->
                <div class="detail-section">
                    <div class="detail-title">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        Informasi Pendaftaran Skripsi
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="detail-label">Judul Skripsi</div>
                            <div class="detail-value">
                                <strong>{{ $pendaftaran->judul_skripsi }}</strong>
                            </div>

                            <div class="detail-label">Dosen Pembimbing</div>
                            <div class="detail-value">{{ $pendaftaran->dosen_pembimbing }}</div>

                            @if ($pendaftaran->narasumber)
                                <div class="detail-label">Narasumber Seminar</div>
                                <div class="detail-value">{{ $pendaftaran->narasumber }}</div>
                            @endif

                            @if ($pendaftaran->tanggal_seminar)
                                <div class="detail-label">Tanggal Seminar</div>
                                <div class="detail-value">
                                    {{ \Carbon\Carbon::parse($pendaftaran->tanggal_seminar)->format('d F Y') }}</div>
                            @endif

                            <div class="detail-label">Tanggal Pendaftaran</div>
                            <div class="detail-value">{{ $pendaftaran->created_at->format('d F Y H:i:s') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Status Box -->
                <div class="detail-section">
                    <div class="detail-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Status Pendaftaran
                    </div>

                    @if ($pendaftaran->status == 'pending')
                        <div class="warning-box">
                            <i class="bi bi-clock-history me-2"></i>
                            <strong>Menunggu Review</strong><br>
                            Pendaftaran ini sedang menunggu untuk direview oleh reviewer.
                        </div>
                    @elseif($pendaftaran->status == 'review')
                        <div class="info-box">
                            <i class="bi bi-eye me-2"></i>
                            <strong>Sedang Direview</strong><br>
                            Pendaftaran ini sedang dalam proses review.
                        </div>
                    @elseif($pendaftaran->status == 'approved')
                        <div class="success-box">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>Disetujui - Lanjut Sidang</strong><br>
                            Pendaftaran ini telah disetujui. Mahasiswa dapat melanjutkan ke tahap sidang.
                        </div>
                    @elseif($pendaftaran->status == 'rejected')
                        <div class="warning-box">
                            <i class="bi bi-x-circle-fill me-2"></i>
                            <strong>Ditolak</strong><br>
                            Pendaftaran ini ditolak. Silakan cek catatan reviewer.
                        </div>
                    @elseif($pendaftaran->status == 'revision')
                        <div class="warning-box">
                            <i class="bi bi-pencil-square me-2"></i>
                            <strong>Perlu Revisi</strong><br>
                            Pendaftaran ini perlu direvisi. Silakan cek catatan reviewer.
                        </div>
                    @endif

                    @if ($pendaftaran->reviewer_notes)
                        <div class="mt-3 p-3 bg-light rounded">
                            <strong><i class="bi bi-chat-dots"></i> Catatan Reviewer:</strong>
                            <p class="mt-2 mb-0">{{ $pendaftaran->reviewer_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <!-- Aksi -->
                <div class="detail-section">
                    <div class="detail-title">
                        <i class="bi bi-gear me-2"></i>
                        Aksi
                    </div>

                    @if ($pendaftaran->status == 'pending')
                        {{-- PERBAIKAN DI SINI --}}
                        <a href="{{ route('admin.pendaftaran.assign-form', ['type' => 'skripsi', 'id' => $pendaftaran->id]) }}"
                            class="btn btn-primary btn-action w-100 mb-2">
                            <i class="bi bi-send"></i> Assign ke Reviewer
                        </a>
                    @endif

                    <a href="{{ route('admin.pendaftaran') }}" class="btn btn-secondary btn-action w-100">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <!-- Review Details (Jika sudah direview) -->
                @if ($pendaftaran->reviewDetails && $pendaftaran->reviewDetails->count() > 0)
                    <div class="detail-section">
                        <div class="detail-title">
                            <i class="bi bi-check2-square me-2"></i>
                            Hasil Review Dokumen
                        </div>

                        @foreach ($pendaftaran->reviewDetails as $review)
                            <div class="document-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="document-name">
                                            @switch($review->jenis_dokumen)
                                                @case('bukti_pembayaran_registrasi')
                                                    Bukti Pembayaran Registrasi Terakhir
                                                @break

                                                @case('bukti_pembayaran_sidang')
                                                    Bukti Pembayaran Sidang
                                                @break

                                                @case('bukti_pembayaran_skripsi')
                                                    Bukti Pembayaran Skripsi
                                                @break

                                                @case('frs')
                                                    Formulir Rencana Studi (FRS)
                                                @break

                                                @case('transkrip_nilai')
                                                    Transkrip Nilai
                                                @break

                                                @case('surat_bebas_perpus')
                                                    Surat Bebas Perpustakaan
                                                @break

                                                @case('surat_bebas_alat_tes')
                                                    Surat Bebas Alat Tes
                                                @break

                                                @case('sertifikat_pesantren')
                                                    Sertifikat Pesantren
                                                @break

                                                @case('sertifikat_sks_non_akademik')
                                                    Sertifikat SKS Non Akademik
                                                @break

                                                @case('surat_lolos_turnitin')
                                                    Surat Lolos Turnitin
                                                @break

                                                @case('sertifikat_toefl')
                                                    Sertifikat TOEFL
                                                @break

                                                @case('pas_foto')
                                                    Pas Foto
                                                @break

                                                @case('buku_bimbingan')
                                                    Buku Bimbingan
                                                @break

                                                @case('surat_perbaikan')
                                                    Surat Perbaikan
                                                @break

                                                @case('surat_ijin_sidang')
                                                    Surat Ijin Sidang
                                                @break

                                                @case('berkas_skripsi')
                                                    Berkas Skripsi
                                                @break

                                                default:
                                                {{ str_replace('_', ' ', ucfirst($review->jenis_dokumen)) }}
                                            @endswitch
                                        </div>
                                        @if ($review->komentar)
                                            <div class="document-file text-muted mt-1">
                                                <i class="bi bi-chat"></i> {{ $review->komentar }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        @if ($review->status == 'valid')
                                            <span class="badge bg-success">✓ Valid</span>
                                        @elseif($review->status == 'invalid')
                                            <span class="badge bg-danger">✗ Invalid</span>
                                        @else
                                            <span class="badge bg-warning">↻ Perbaiki</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Dokumen yang Diupload -->
        <div class="detail-section">
            <div class="detail-title">
                <i class="bi bi-files me-2"></i>
                Dokumen yang Diupload
            </div>

            <div class="row">
                @foreach ($pendaftaran->dokumen as $dokumen)
                    <div class="col-md-6 col-lg-4">
                        <div class="document-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="document-name">
                                        @switch($dokumen->jenis_dokumen)
                                            @case('bukti_pembayaran_registrasi')
                                                <i class="bi bi-receipt text-primary"></i> Bukti Pembayaran Registrasi Terakhir
                                            @break

                                            @case('bukti_pembayaran_sidang')
                                                <i class="bi bi-cash-stack text-primary"></i> Bukti Pembayaran Sidang
                                            @break

                                            @case('bukti_pembayaran_skripsi')
                                                <i class="bi bi-credit-card text-primary"></i> Bukti Pembayaran Skripsi
                                            @break

                                            @case('frs')
                                                <i class="bi bi-table text-primary"></i> Formulir Rencana Studi (FRS)
                                            @break

                                            @case('transkrip_nilai')
                                                <i class="bi bi-file-text text-primary"></i> Transkrip Nilai
                                            @break

                                            @case('surat_bebas_perpus')
                                                <i class="bi bi-building text-primary"></i> Surat Bebas Perpustakaan
                                            @break

                                            @case('surat_bebas_alat_tes')
                                                <i class="bi bi-flask text-primary"></i> Surat Bebas Alat Tes
                                            @break

                                            @case('sertifikat_pesantren')
                                                <i class="bi bi-mosque text-primary"></i> Sertifikat Pesantren
                                            @break

                                            @case('sertifikat_sks_non_akademik')
                                                <i class="bi bi-award text-primary"></i> Sertifikat SKS Non Akademik
                                            @break

                                            @case('surat_lolos_turnitin')
                                                <i class="bi bi-percent text-primary"></i> Surat Lolos Turnitin
                                            @break

                                            @case('sertifikat_toefl')
                                                <i class="bi bi-globe text-primary"></i> Sertifikat TOEFL
                                            @break

                                            @case('pas_foto')
                                                <i class="bi bi-image text-primary"></i> Pas Foto
                                            @break

                                            @case('buku_bimbingan')
                                                <i class="bi bi-book text-primary"></i> Buku Bimbingan
                                            @break

                                            @case('surat_perbaikan')
                                                <i class="bi bi-pencil-square text-primary"></i> Surat Perbaikan
                                            @break

                                            @case('surat_ijin_sidang')
                                                <i class="bi bi-file-check text-primary"></i> Surat Ijin Sidang
                                            @break

                                            @case('berkas_skripsi')
                                                <i class="bi bi-file-pdf text-danger"></i> Berkas Skripsi
                                            @break

                                            default:
                                            <i class="bi bi-file-earmark text-primary"></i>
                                            {{ str_replace('_', ' ', ucfirst($dokumen->jenis_dokumen)) }}
                                        @endswitch
                                    </div>
                                    <div class="document-file mt-1">
                                        <small>{{ basename($dokumen->file_path) }}</small>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($dokumen->file_path) }}" target="_blank"
                                    class="btn-download btn-sm">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
