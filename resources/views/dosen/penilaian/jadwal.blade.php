@extends('layouts.app')

@section('title', 'Jadwal Sidang Skripsi')

@section('content')
    <div class="container-fluid">
        <div class="card fade-in border-0 shadow-sm">
            <div class="card-header-custom">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-check me-2"></i>
                    Jadwal Sidang Skripsi
                </h5>
            </div>
            <div class="card-body">
                @if ($jadwal->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Belum ada jadwal sidang</p>
                        <a href="{{ route('dosen.dashboard') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Hari, Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Ruang</th>
                                    <th>NPM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Judul Skripsi</th>
                                    <th>Status Penilaian</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jadwal as $index => $item)
                                    @php
                                        $namaDosen = Auth::user()->dosen->name ?? Auth::user()->name;
                                        $penilaian = $item->penilaian->where('nama_dosen_penguji', $namaDosen)->first();
                                        $status =
                                            $penilaian && $penilaian->is_completed
                                                ? 'Selesai Dinilai'
                                                : 'Belum Dinilai';
                                        $statusClass = $status == 'Selesai Dinilai' ? 'bg-success' : 'bg-warning';
                                    @endphp
                                    <tr>
                                        <td class="align-middle">{{ $loop->iteration }}</td>
                                        <td class="align-middle">
                                            {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l') }},<br>
                                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                        </td>
                                        <td class="align-middle">
                                            {{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}
                                        </td>
                                        <td class="align-middle">{{ $item->ruang ?? '-' }}</td>
                                        <td class="align-middle">{{ $item->pendaftaran->mahasiswa->npm }}</td>
                                        <td class="align-middle">{{ $item->pendaftaran->mahasiswa->user->name }}</td>
                                        <td class="align-middle">{{ Str::limit($item->pendaftaran->judul_skripsi, 50) }}
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge {{ $statusClass }}">{{ $status }}</span>
                                        </td>
                                        <td class="align-middle">
                                            @if ($penilaian && $penilaian->is_completed)
                                                <a href="{{ route('dosen.penilaian.create', $item->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> Lihat Nilai
                                                </a>
                                            @else
                                                <a href="{{ route('dosen.penilaian.create', $item->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil-square"></i> Beri Nilai
                                                </a>
                                            @endif
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
