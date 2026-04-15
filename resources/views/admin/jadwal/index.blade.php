@extends('layouts.app')
@section('title', 'Jadwal Sidang & Ujian')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#skripsi">Sidang Skripsi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#metodologi">Ujian Metodologi</a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- TAB SKRIPSI -->
                <div class="tab-pane active" id="skripsi">
                    <div class="card">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Daftar Mahasiswa yang Layak Sidang</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                            <th>Hari</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>NPM</th>
                                            <th>Nama</th>
                                            <th>Judul Skripsi</th>
                                            <th>Dosen Pembimbing</th>
                                            <th>Dosen Penguji</th>
                                            <th>Status Jadwal</th>
                                            <th>Aksi</th>
                                        </thead>
                                    <tbody>
                                        @forelse($skripsi as $item)
                                        @php
                                            $jadwal = $item->jadwal;
                                            $hari = $jadwal ? \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l') : '-';
                                            $tanggal = $jadwal ? $jadwal->tanggal->format('d/m/Y') : '-';
                                            $waktu = $jadwal ? $jadwal->waktu_mulai->format('H:i') . ' - ' . $jadwal->waktu_selesai->format('H:i') : '-';
                                            $penguji = [];
                                            if($jadwal) {
                                                if($jadwal->dosen_penguji_1) $penguji[] = $jadwal->dosen_penguji_1;
                                                if($jadwal->dosen_penguji_2) $penguji[] = $jadwal->dosen_penguji_2;
                                            }
                                        @endphp
                                        <tr>
                                            <td class="align-middle">{{ $hari }}</td>
                                            <td class="align-middle">{{ $tanggal }}</td>
                                            <td class="align-middle">{{ $waktu }}</td>
                                            <td class="align-middle">{{ $item->mahasiswa->npm }}</td>
                                            <td class="align-middle">{{ $item->mahasiswa->user->name }}</td>
                                            <td class="align-middle">{{ Str::limit($item->judul_skripsi, 50) }}</td>
                                            <td class="align-middle">{{ $item->dosen_pembimbing }}</td>
                                            <td class="align-middle">{{ implode(', ', $penguji) ?: '-' }}</td>
                                            <td class="align-middle">
                                                @if($jadwal)
                                                    <span class="badge bg-success">Terjadwal</span>
                                                @else
                                                    <span class="badge bg-warning">Belum</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if($jadwal)
                                                    <a href="{{ route('admin.jadwal.skripsi.edit', $jadwal->id) }}" class="btn btn-sm btn-info">Edit</a>
                                                    <form action="{{ route('admin.jadwal.skripsi.destroy', $jadwal->id) }}" method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus jadwal?')">Hapus</button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('admin.jadwal.skripsi.create', $item->id) }}" class="btn btn-sm btn-primary">Buat Jadwal</a>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="text-center">Belum ada mahasiswa yang layak sidang</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB METODOLOGI -->
                <div class="tab-pane" id="metodologi">
                    <div class="card">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Daftar Mahasiswa yang Layak Ujian Metodologi</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <th>Hari</th>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>NPM</th>
                                        <th>Nama</th>
                                        <th>Judul Penelitian</th>
                                        <th>Dosen Pembimbing</th>
                                        <th>Dosen Penguji</th>
                                        <th>Status Jadwal</th>
                                        <th>Aksi</th>
                                    </thead>
                                    <tbody>
                                        @forelse($metodologi as $item)
                                        @php
                                            $jadwal = $item->jadwal;
                                            $hari = $jadwal ? \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l') : '-';
                                            $tanggal = $jadwal ? $jadwal->tanggal->format('d/m/Y') : '-';
                                            $waktu = $jadwal ? $jadwal->waktu_mulai->format('H:i') . ' - ' . $jadwal->waktu_selesai->format('H:i') : '-';
                                            $penguji = [];
                                            if($jadwal) {
                                                if($jadwal->dosen_penguji_1) $penguji[] = $jadwal->dosen_penguji_1;
                                                if($jadwal->dosen_penguji_2) $penguji[] = $jadwal->dosen_penguji_2;
                                            }
                                        @endphp
                                        <tr>
                                            <td class="align-middle">{{ $hari }}</td>
                                            <td class="align-middle">{{ $tanggal }}</td>
                                            <td class="align-middle">{{ $waktu }}</td>
                                            <td class="align-middle">{{ $item->mahasiswa->npm }}</td>
                                            <td class="align-middle">{{ $item->mahasiswa->user->name }}</td>
                                            <td class="align-middle">{{ Str::limit($item->judul_penelitian, 50) }}</td>
                                            <td class="align-middle">{{ $item->dosen_pembimbing }}</td>
                                            <td class="align-middle">{{ implode(', ', $penguji) ?: '-' }}</td>
                                            <td class="align-middle">
                                                @if($jadwal)
                                                    <span class="badge bg-success">Terjadwal</span>
                                                @else
                                                    <span class="badge bg-warning">Belum</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if($jadwal)
                                                    <a href="{{ route('admin.jadwal.metodologi.edit', $jadwal->id) }}" class="btn btn-sm btn-info">Edit</a>
                                                    <form action="{{ route('admin.jadwal.metodologi.destroy', $jadwal->id) }}" method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus jadwal?')">Hapus</button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('admin.jadwal.metodologi.create', $item->id) }}" class="btn btn-sm btn-primary">Buat Jadwal</a>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="text-center">Belum ada mahasiswa yang layak ujian metodologi</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection