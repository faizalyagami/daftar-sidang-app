@extends('layouts.app')

@section('title', 'Form Penilaian Sidang Skripsi')

@section('content')
    <style>
        .nilai-input {
            width: 80px;
            text-align: center;
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
                    <i class="bi bi-clipboard-check me-2"></i>
                    Lembar Penilaian Sidang Skripsi
                </h5>
            </div>
            <div class="card-body">
                <!-- Informasi Mahasiswa -->
                <div class="alert alert-info mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nama Mahasiswa:</strong> {{ $jadwal->pendaftaran->mahasiswa->user->name }}<br>
                            <strong>NPM:</strong> {{ $jadwal->pendaftaran->mahasiswa->npm }}
                        </div>
                        <div class="col-md-6">
                            <strong>Judul Skripsi:</strong> {{ $jadwal->pendaftaran->judul_skripsi }}<br>
                            <strong>Tanggal Sidang:</strong>
                            {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}
                        </div>
                    </div>
                </div>

                <form action="{{ route('dosen.penilaian.store', $penilaian->id) }}" method="POST">
                    @csrf

                    <h6 class="mb-3">Lembar Individual Sidang Skripsi</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-penilaian">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Aspek yang Dinilai</th>
                                    <th>Bobot</th>
                                    <th>Nilai (0-100)</th>
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
                                    <td class="text-center">
                                        <input type="number" name="nilai_fenomena" class="form-control nilai-input"
                                            value="{{ old('nilai_fenomena', $penilaian->nilai_fenomena) }}" min="0"
                                            max="100" required>
                                    </td>
                                    <td class="text-center jumlah-fenomena">0</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Variabel</td>
                                    <td class="text-center">2</td>
                                    <td class="text-center">
                                        <input type="number" name="nilai_variabel" class="form-control nilai-input"
                                            value="{{ old('nilai_variabel', $penilaian->nilai_variabel) }}" min="0"
                                            max="100" required>
                                    </td>
                                    <td class="text-center jumlah-variabel">0</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Teori dan metode</td>
                                    <td class="text-center">2</td>
                                    <td class="text-center">
                                        <input type="number" name="nilai_teori_metode" class="form-control nilai-input"
                                            value="{{ old('nilai_teori_metode', $penilaian->nilai_teori_metode) }}"
                                            min="0" max="100" required>
                                    </td>
                                    <td class="text-center jumlah-teori">0</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Alat ukur</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">
                                        <input type="number" name="nilai_alat_ukur" class="form-control nilai-input"
                                            value="{{ old('nilai_alat_ukur', $penilaian->nilai_alat_ukur) }}" min="0"
                                            max="100" required>
                                    </td>
                                    <td class="text-center jumlah-alat">0</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Analisis</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">
                                        <input type="number" name="nilai_analisis" class="form-control nilai-input"
                                            value="{{ old('nilai_analisis', $penilaian->nilai_analisis) }}" min="0"
                                            max="100" required>
                                    </td>
                                    <td class="text-center jumlah-analisis">0</td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>Simpulan saran</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">
                                        <input type="number" name="nilai_simpulan_saran" class="form-control nilai-input"
                                            value="{{ old('nilai_simpulan_saran', $penilaian->nilai_simpulan_saran) }}"
                                            min="0" max="100" required>
                                    </td>
                                    <td class="text-center jumlah-simpulan">0</td>
                                </tr>
                                <tr class="table-secondary">
                                    <td colspan="5"><strong>Presentasi</strong></td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td>Presentasi (Verbalisasi & Argumentasi)</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">
                                        <input type="number" name="nilai_presentasi" class="form-control nilai-input"
                                            value="{{ old('nilai_presentasi', $penilaian->nilai_presentasi) }}"
                                            min="0" max="100" required>
                                    </td>
                                    <td class="text-center jumlah-presentasi">0</td>
                                </tr>
                                <tr class="table-secondary">
                                    <td colspan="4" class="text-end"><strong>TOTAL</strong></td>
                                    <td class="text-center"><strong id="total_nilai">0</strong></td>
                                </tr>
                                <tr class="table-secondary">
                                    <td colspan="4" class="text-end"><strong>RATA-RATA</strong></td>
                                    <td class="text-center"><strong id="rata_rata">0</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Catatan -->
                    <div class="mb-3">
                        <label class="form-label">Catatan (Kritik/Saran)</label>
                        <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $penilaian->catatan) }}</textarea>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('dosen.penilaian.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Penilaian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function hitungJumlah() {
            const fenomena = parseFloat($('input[name="nilai_fenomena"]').val()) || 0;
            const variabel = parseFloat($('input[name="nilai_variabel"]').val()) || 0;
            const teori = parseFloat($('input[name="nilai_teori_metode"]').val()) || 0;
            const alat = parseFloat($('input[name="nilai_alat_ukur"]').val()) || 0;
            const analisis = parseFloat($('input[name="nilai_analisis"]').val()) || 0;
            const simpulan = parseFloat($('input[name="nilai_simpulan_saran"]').val()) || 0;
            const presentasi = parseFloat($('input[name="nilai_presentasi"]').val()) || 0;

            $('.jumlah-fenomena').text(fenomena * 2);
            $('.jumlah-variabel').text(variabel * 2);
            $('.jumlah-teori').text(teori * 2);
            $('.jumlah-alat').text(alat * 1);
            $('.jumlah-analisis').text(analisis * 1);
            $('.jumlah-simpulan').text(simpulan * 1);
            $('.jumlah-presentasi').text(presentasi * 1);

            const total = (fenomena * 2) + (variabel * 2) + (teori * 2) + (alat * 1) +
                (analisis * 1) + (simpulan * 1) + (presentasi * 1);
            const rata = total / 10;

            $('#total_nilai').text(total);
            $('#rata_rata').text(rata.toFixed(2));
        }

        $(document).ready(function() {
            $('.nilai-input').on('input', hitungJumlah);
            hitungJumlah();
        });
    </script>
@endpush
