@extends('layouts.app')

@section('title', 'Buat Jadwal Sidang Skripsi')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header-custom">
                <h5 class="mb-0">Buat Jadwal Sidang Skripsi</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Mahasiswa:</strong> {{ $pendaftaran->mahasiswa->user->name }}
                    ({{ $pendaftaran->mahasiswa->npm }})<br>
                    <strong>Judul Skripsi:</strong> {{ $pendaftaran->judul_skripsi }}
                </div>

                <form action="{{ route('admin.jadwal.skripsi.store', $pendaftaran->id) }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Sidang</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Ruang</label>
                            <input type="text" name="ruang" class="form-control" placeholder="Contoh: Ruang A.2.1">
                        </div>

                        <!-- Dosen Penguji 1 (Pembimbing) - menggunakan ID -->
                        <div class="col-md-6 mb-3">
                            <label>Dosen Penguji 1 (Pembimbing) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ $jadwal->pendaftaran->dosen_pembimbing }}"
                                readonly disabled>
                            <small class="text-muted">Dosen Pembimbing mahasiswa ini</small>
                        </div>

                        <!-- Dosen Penguji 2 - menggunakan ID -->
                        <div class="col-md-6 mb-3">
                            <label>Dosen Penguji 2</label>
                            <select name="dosen_penguji_2_id" class="form-select select2-dosen">
                                <option value="">-- Pilih Dosen Penguji 2 --</option>
                                @foreach ($dosens as $dosen)
                                    <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dosen Penguji 3 - menggunakan ID -->
                        <div class="col-md-6 mb-3">
                            <label>Dosen Penguji 3 (Opsional)</label>
                            <select name="dosen_penguji_3_id" class="form-select select2-dosen">
                                <option value="">-- Pilih Dosen Penguji 3 --</option>
                                @foreach ($dosens as $dosen)
                                    <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2-dosen').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Ketik nama dosen...',
                allowClear: true
            });
        });
    </script>
@endpush
