@extends('layouts.app')
@section('title', 'Buat Jadwal Ujian Meotologi Penelitian')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header-custom">
            <h5>Buat Jadwal Ujian Metodologi Penelitian</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Mahasiswa:</strong> {{ $pendaftaran->mahasiswa->user->name }} ({{ $pendaftaran->mahasiswa->npm }})<br>
                <strong>Judul Metodologi Penelitian:</strong> {{ $pendaftaran->judul_metodologi }}
            </div>

            <form action="{{ route('admin.jadwal.metodologi.store', $pendaftaran->id) }}" method="POST">
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
                    <div class="col-md-6 mb-3">
                        <label>Dosen Penguji 1</label>
                        <select class="form-select select2-dosen @error('dosen_penguji_1') is-invalid @enderror"
                            name="narasumber"
                            style="width: 100%;">
                            <option value="">-- Cari Dosen Penguji 1 --</option>
                            @foreach($dosens as $dosen)
                            <option value="{{ $dosen->name }}" {{ old('narasumber') == $dosen->name ? 'selected' : '' }}>
                                {{ $dosen->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('narasumber')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Dosen Penguji 2</label>
                        <select class="form-select select2-dosen @error('dosen_penguji_2') is-invalid @enderror"
                            name="narasumber"
                            style="width: 100%;">
                            <option value="">-- Cari Dosen Penguji 2 --</option>
                            @foreach($dosens as $dosen)
                            <option value="{{ $dosen->name }}" {{ old('narasumber') == $dosen->name ? 'selected' : '' }}>
                                {{ $dosen->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('narasumber')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- <div class="col-md-6 mb-3">
                        <label>Dosen Penguji 3 (Opsional)</label>
                        <select class="form-select select2-dosen @error('dosen_penguji_3') is-invalid @enderror"
                            name="narasumber"
                            style="width: 100%;">
                            <option value="">-- Cari Dosen Penguji 3 --</option>
                            @foreach($dosens as $dosen)
                            <option value="{{ $dosen->name }}" {{ old('narasumber') == $dosen->name ? 'selected' : '' }}>
                                {{ $dosen->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('narasumber')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}
                    <div class="col-md-12 mb-3">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    $(document).ready(function() {
        $('.select2-dosen').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Ketik nama dosen...',
            allowClear: true,
            language: {
                noResults: function() {
                    return 'Dosen tidak ditemukan. Hubungi admin.';
                },
                searching: function() {
                    return 'Mencari...';
                }
            }
        });
    });
@endpush