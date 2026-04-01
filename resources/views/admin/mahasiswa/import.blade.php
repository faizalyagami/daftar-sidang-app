@extends('layouts.app')

@section('title', 'Import Data Mahasiswa')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card fade-in">
            <div class="card-header-custom">
                <h5 class="mb-0">
                    <i class="bi bi-upload me-2"></i>
                    Import Data Mahasiswa dari SIAKAD
                </h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Error:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Panduan Import Data:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Download template Excel terlebih dahulu</li>
                        <li>Isi data sesuai dengan format yang telah ditentukan</li>
                        <li><strong class="text-danger">NPM dan Nama Mahasiswa wajib diisi</strong></li>
                        <li>Format tanggal lahir: <strong>dd-mm-yy</strong> atau <strong>dd-mm-yyyy</strong> (contoh: 23-12-00)</li>
                        <li>File harus berformat <strong>.xlsx, .xls, atau .csv</strong> dengan maksimal 10MB</li>
                        <li>Data yang sudah ada akan diupdate, data baru akan ditambahkan</li>
                        <li>Password default untuk akun baru: <strong>password123</strong></li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Catatan Penting:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Pastikan file Excel tidak memiliki baris kosong di tengah data</li>
                        <li>Hindari penggunaan karakter khusus yang tidak diperlukan</li>
                        <li>NPM harus unik dan tidak boleh ada duplikat</li>
                    </ul>
                </div>

                <form action="{{ route('admin.mahasiswa.import.process') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    id="importForm">
                    @csrf

                    <!-- Pilihan Periode Akademik dan Jenis Perwalian -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Periode Akademik <span class="text-danger">*</span></label>
                            <select name="academic_period_id" class="form-select" required>
                                <option value="">-- Pilih Periode Akademik --</option>
                                @foreach($periods as $period)
                                <option value="{{ $period->id }}" {{ old('academic_period_id') == $period->id ? 'selected' : '' }}>
                                    {{ $period->semester }} {{ $period->tahun_akademik }}
                                    <!-- ({{ $period->start_date->format('d/m/Y') }} - {{ $period->end_date->format('d/m/Y') }}) -->
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pilih periode akademik yang sedang berjalan</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jenis Perwalian <span class="text-danger">*</span></label>
                            <select name="jenis_perwalian" class="form-select" required>
                                <option value="">-- Pilih Jenis Perwalian --</option>
                                <option value="skripsi" {{ old('jenis_perwalian') == 'skripsi' ? 'selected' : '' }}>Skripsi</option>
                                <option value="metodologi" {{ old('jenis_perwalian') == 'metodologi' ? 'selected' : '' }}>Metodologi Penelitian</option>
                            </select>
                            <small class="text-muted">Mahasiswa akan otomatis didaftarkan ke jenis perwalian ini pada periode yang dipilih</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Pilih File Excel <span class="text-danger">*</span></label>
                        <input type="file"
                            class="form-control @error('file') is-invalid @enderror"
                            name="file"
                            accept=".xlsx,.xls,.csv"
                            required>
                        @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format yang didukung: .xlsx, .xls, .csv (Max 10MB)</small>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmImport" required>
                            <label class="form-check-label" for="confirmImport">
                                Saya mengkonfirmasi bahwa data yang diimport sudah sesuai dengan format template dan periode yang dipilih.
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.mahasiswa.template') }}" class="btn btn-outline-primary">
                            <i class="bi bi-download"></i> Download Template
                        </a>
                        <div>
                            <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary me-2">
                                <i class="bi bi-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-upload"></i> Import Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header-custom">
                <h6 class="mb-0">Preview Format Template Excel</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            32
                            <th>NPM</th>
                            <th>Nama Mahasiswa</th>
                            <th>NIK Dosen Wali</th>
                            <th>Dosen Wali</th>
                            <th>SKS Lulus</th>
                            <th>SKS Tempuh</th>
                            <th>SKS Sisa</th>
                            <th>IPK</th>
                            <th>IPK (3 digit)</th>
                            <th>Tmpt Lahir</th>
                            <th>Tgl Lahir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>10050019026</td>
                                <td>MOCHAMAD AZMI FAUZAN MUSYAFA</td>
                                <td>D150673</td>
                                <td>RIZKA HADIAN PERMANA., S.PSI, M.PSI.</td>
                                <td>107</td>
                                <td>121</td>
                                <td>39</td>
                                <td>2.57</td>
                                <td>2.572</td>
                                <td>BANDUNG</td>
                                <td>23-12-00</td>
                            </tr>
                            <tr>
                                <td>10050019027</td>
                                <td>M. ARIQ ZAHID BAIHAQI</td>
                                <td>D150673</td>
                                <td>RIZKA HADIAN PERMANA., S.PSI, M.PSI.</td>
                                <td>108</td>
                                <td>123</td>
                                <td>38</td>
                                <td>2.55</td>
                                <td>2.549</td>
                                <td>SUKABUMI</td>
                                <td>17-09-00</td>
                            </tr>
                            <tr>
                                <td colspan="11" class="text-muted text-center">... dan seterusnya</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('importForm')?.addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner me-2"></span> Mengimport data...';
    });
</script>
@endpush