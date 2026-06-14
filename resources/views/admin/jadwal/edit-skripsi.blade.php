@extends('layouts.app')
@section('title', 'Edit Jadwal Sidang Skripsi')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header-custom">
                <h5>Edit Jadwal Sidang Skripsi</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Mahasiswa:</strong> {{ $jadwal->pendaftaran->mahasiswa->user->name }}
                    ({{ $jadwal->pendaftaran->mahasiswa->npm }})<br>
                    <strong>Judul Skripsi:</strong> {{ $jadwal->pendaftaran->judul_skripsi }}
                </div>

                <form action="{{ route('admin.jadwal.skripsi.update', $jadwal->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Hari & Tanggal Sidang</label>
                            <input type="date" name="tanggal" class="form-control"
                                value="{{ $jadwal->tanggal->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" class="form-control"
                                value="{{ $jadwal->waktu_mulai->format('H:i') }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" class="form-control"
                                value="{{ $jadwal->waktu_selesai->format('H:i') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Ruang</label>
                            <input type="text" name="ruang" class="form-control" value="{{ $jadwal->ruang }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Dosen Penguji 1 (Pembimbing)</label>
                            <input type="text" class="form-control" value="{{ $jadwal->pendaftaran->dosen_pembimbing }}"
                                readonly disabled>
                        </div>
                        <div class="col-md-6" mb-3>
                            <label>Dosen Penguji 2</label>
                            <select class="form-select select2-dosen" name="dosen_penguji_2" style="width: 100%;">
                                <option value="">-- Cari Dosen Penguji 2 --</option>
                                @foreach ($dosens as $dosen)
                                    <option value="{{ $dosen->name }}"
                                        {{ $jadwal->dosen_penguji_2 == $dosen->name ? 'selected' : '' }}>
                                        {{ $dosen->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Dosen Penguji 3</label>
                            <select class="form-select select2-dosen" name="dosen_penguji_3" style="width: 100%;">
                                <option value="">-- Cari Dosen Penguji 3 --</option>
                                @foreach ($dosens as $dosen)
                                    <option value="{{ $dosen->name }}"
                                        {{ $jadwal->dosen_penguji_3 == $dosen->name ? 'selected' : '' }}>
                                        {{ $dosen->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Dosen Narasumber</label>
                            <input type="text" class="form-control"
                                value="{{ $jadwal->pendaftaran->narasumber ?? '-' }}" readonly disabled>
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
