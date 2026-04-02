@extends('layouts.app')
@section('title', 'Buat Jadwal Sidang Skripsi')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header-custom">
            <h5>Buat Jadwal Sidang Skripsi</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Mahasiswa:</strong> {{ $pendaftaran->mahasiswa->user->name }} ({{ $pendaftaran->mahasiswa->npm }})<br>
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
                    <div class="col-md-6 mb-3">
                        <label>Dosen Penguji 1</label>
                        <input type="text" name="dosen_penguji_1" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Dosen Penguji 2</label>
                        <input type="text" name="dosen_penguji_2" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Dosen Penguji 3 (Opsional)</label>
                        <input type="text" name="dosen_penguji_3" class="form-control">
                    </div>
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