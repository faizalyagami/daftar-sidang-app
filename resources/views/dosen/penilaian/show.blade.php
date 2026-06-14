@extends('layouts.app')

@section('title', 'Detail Penilaian Sidang Skripsi')

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

        .nilai-display {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }

        .table-penilaian td,
        .table-penilaian th {
            vertical-align: middle;
        }
    </style>

    <div class="container-fluid">
        <div class="card fade-in border-0 shadow-sm">
            <div class="card-header-custom">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Detail Penilaian Sidang Skripsi
                </h5>
            </div>
            <div class="card-body">
                <!-- Informasi Sidang -->
                <div class="detail-section">
                    <div class="detail-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Informasi Sidang
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nama Penguji</strong></td>
                                    <td>: {{ $penilaian->nama_dosen_penguji }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Sidang</strong></td>
                                    <td>: {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Waktu</strong></td>
                                    <td>: {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }} WIB
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

                <!-- Hasil Penilaian -->
                <div class="detail-section">
                    <div class="detail-title">
                        <i class="bi bi-clipboard-check me-2"></i>
                        Hasil Penilaian
                    </div>

                    <div class="alert alert-success mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nilai Akhir:</strong>
                                <div class="nilai-display">{{ number_format($penilaian->nilai_akhir, 2) }}</div>
                            </div>
                            <div class="col-md-6">
                                <strong>Total Jumlah:</strong>
                                <div class="nilai-display">{{ number_format($penilaian->jumlah, 0) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-penilaian">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Aspek yang Dinilai</th>
                                    <th>Bobot</th>
                                    <th>Nilai</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-secondary">
                                    <td colspan="5"><strong>Skripsi</strong></td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>Fenomena</td>
                                    <td class="text-center">2</td>
                                    <td class="text-center">{{ $penilaian->nilai_fenomena ?? 0 }}</td>
                                    <td class="text-center">{{ ($penilaian->nilai_fenomena ?? 0) * 2 }}</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Variabel</td>
                                    <td class="text-center">2</td>
                                    <td class="text-center">{{ $penilaian->nilai_variabel ?? 0 }}</td>
                                    <td class="text-center">{{ ($penilaian->nilai_variabel ?? 0) * 2 }}</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Teori dan metode</td>
                                    <td class="text-center">2</td>
                                    <td class="text-center">{{ $penilaian->nilai_teori_metode ?? 0 }}</td>
                                    <td class="text-center">{{ ($penilaian->nilai_teori_metode ?? 0) * 2 }}</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Alat ukur</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">{{ $penilaian->nilai_alat_ukur ?? 0 }}</td>
                                    <td class="text-center">{{ $penilaian->nilai_alat_ukur ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Analisis</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">{{ $penilaian->nilai_analisis ?? 0 }}</td>
                                    <td class="text-center">{{ $penilaian->nilai_analisis ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>Simpulan saran</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">{{ $penilaian->nilai_simpulan_saran ?? 0 }}</td>
                                    <td class="text-center">{{ $penilaian->nilai_simpulan_saran ?? 0 }}</td>
                                </tr>
                                <tr class="table-secondary">
                                    <td colspan="5"><strong>Presentasi</strong></td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td>Presentasi (Verbalisasi & Argumentasi)</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">{{ $penilaian->nilai_presentasi ?? 0 }}</td>
                                    <td class="text-center">{{ $penilaian->nilai_presentasi ?? 0 }}</td>
                                </tr>
                                <tr class="table-secondary">
                                    <td colspan="4" class="text-end"><strong>TOTAL</strong></td>
                                    <td class="text-center"><strong>{{ number_format($penilaian->jumlah, 0) }}</strong>
                                    </td>
                                </tr>
                                <tr class="table-secondary">
                                    <td colspan="4" class="text-end"><strong>RATA-RATA</strong></td>
                                    <td class="text-center">
                                        <strong>{{ number_format($penilaian->nilai_akhir, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if ($penilaian->catatan)
                        <div class="mt-3">
                            <strong><i class="bi bi-chat-dots me-1"></i> Catatan:</strong>
                            <p class="mt-1">{{ $penilaian->catatan }}</p>
                        </div>
                    @endif
                </div>

                <!-- Tombol Kembali -->
                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('dosen.penilaian.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    @if ($penilaian->is_completed)
                        <a href="{{ route('dosen.penilaian.feedback', $jadwal->id) }}" class="btn btn-info">
                            <i class="bi bi-chat-dots"></i> Lihat Feedback
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
