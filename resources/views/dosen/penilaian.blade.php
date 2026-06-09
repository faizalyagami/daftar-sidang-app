@extends('layouts.app')

@section('title', 'Penilaian Sidang Skripsi')

@section('content')
    <div class="container-fluid">
        <div class="card fade-in border-0 shadow-sm">
            <div class="card-header-custom">
                <h5 class="mb-0">
                    <i class="bi bi-clipboard-check me-2"></i>
                    Daftar Sidang Skripsi yang Akan Dinilai
                </h5>
            </div>
            <div class="card-body">
                @if ($jadwal->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Belum ada jadwal sidang yang harus dinilai</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>NPM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Judul Skripsi</th>
                                    <th>Status Penilaian</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jadwal as $item)
                                    @php
                                        $penilaian = $item->penilaian
                                            ->where('nama_dosen_penguji', Auth::user()->dosen->nama_gelar)
                                            ->first();
                                        $status = $penilaian && $penilaian->is_completed ? 'Selesai' : 'Belum Dinilai';
                                        $statusClass = $status == 'Selesai' ? 'bg-success' : 'bg-warning';
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                        <td>{{ $item->waktu_mulai->format('H:i') }} -
                                            {{ $item->waktu_selesai->format('H:i') }}</td>
                                        <td>{{ $item->pendaftaran->mahasiswa->npm }}</td>
                                        <td>{{ $item->pendaftaran->mahasiswa->user->name }}</td>
                                        <td>{{ Str::limit($item->pendaftaran->judul_skripsi, 50) }}</td>
                                        <td><span class="badge {{ $statusClass }}">{{ $status }}</span></td>
                                        <td>
                                            <a href="{{ route('dosen.penilaian.create', $item->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil-square"></i> Beri Nilai
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
