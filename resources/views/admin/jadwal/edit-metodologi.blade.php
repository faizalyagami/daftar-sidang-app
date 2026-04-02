@extends('layouts.app')
@section('title', 'Edit Jadwal Ujian Metodologi')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header-custom">
            <h5>Edit Jadwal Ujian Metodologi</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Mahasiswa:</strong> {{ $jadwal->pendaftaran->mahasiswa->user->name }} ({{ $jadwal->pendaftaran->mahasiswa->npm }})<br>
                <strong>Judul Penelitian:</strong> {{ $jadwal->pendaftaran->judul_penelitian }}
            </div>

            <form action="{{ route('admin.jadwal.metodologi.update', $jadwal->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Tanggal Ujian</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ $jadwal->tanggal->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Waktu Mulai</label>
                        <input type="time" name="waktu_mulai" class="form-control" value="{{ $jadwal->waktu_mulai->format('H:i') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Waktu Selesai</label>
                        <input type="time" name="waktu_selesai" class="form-control" value="{{ $jadwal->waktu_selesai->format('H:i') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Ruang</label>
                        <input type="text" name="ruang" class="form-control" value="{{ $jadwal->ruang }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Dosen Penguji 1</label>
                        <input type="text" name="dosen_penguji_1" class="form-control" value="{{ $jadwal->dosen_penguji_1 }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Dosen Penguji 2</label>
                        <input type="text" name="dosen_penguji_2" class="form-control" value="{{ $jadwal->dosen_penguji_2 }}">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ $jadwal->keterangan }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Jadwal</button>
                <a href="{{ route('admin.jadwal.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection