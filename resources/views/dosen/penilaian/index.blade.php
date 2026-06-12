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
                        <a href="{{ route('dosen.dashboard') }}" class="btn btn-primary mt-3">
                            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 100px;">Tanggal</th>
                                    <th style="min-width: 120px;">Waktu</th>
                                    <th style="min-width: 120px;">NPM</th>
                                    <th style="min-width: 180px;">Nama Mahasiswa</th>
                                    <th style="min-width: 250px;">Judul Skripsi</th>
                                    <th style="min-width: 140px;">Status Penilaian</th>
                                    <th style="min-width: 220px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jadwal as $item)
                                    @php
                                        $namaDosen = Auth::user()->dosen->name ?? Auth::user()->name;

                                        // Cek apakah feedback sudah diisi
                                        $feedback = $item->feedback->where('nama_dosen', $namaDosen)->first();
                                        $feedbackCompleted = $feedback && $feedback->is_completed;

                                        // Cek apakah penilaian sudah diisi (hanya bisa diisi setelah feedback)
                                        $penilaian = $item->penilaian->where('nama_dosen_penguji', $namaDosen)->first();
                                        $penilaianCompleted = $penilaian && $penilaian->is_completed;

                                        // Logika Status Penilaian:
                                        // 1. Jika penilaian sudah selesai -> "Selesai"
                                        // 2. Jika feedback sudah diisi tapi penilaian belum -> "Siap Dinilai"
                                        // 3. Jika feedback belum diisi -> "Belum Dinilai"
                                        if ($penilaianCompleted) {
                                            $status = 'Selesai';
                                            $statusClass = 'bg-success';
                                        } elseif ($feedbackCompleted) {
                                            $status = 'Siap Dinilai';
                                            $statusClass = 'bg-primary';
                                        } else {
                                            $status = 'Belum Dinilai';
                                            $statusClass = 'bg-warning';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                        <td>{{ $item->waktu_mulai->format('H:i') }} -
                                            {{ $item->waktu_selesai->format('H:i') }}</td>
                                        <td>{{ $item->pendaftaran->mahasiswa->npm }}</td>
                                        <td>{{ $item->pendaftaran->mahasiswa->user->name }}</td>
                                        <td>{{ Str::limit($item->pendaftaran->judul_skripsi, 50) }}</td>
                                        <td>
                                            <span class="badge {{ $statusClass }}">{{ $status }}</span>
                                            @if ($status == 'Siap Dinilai')
                                                <small class="text-muted d-block mt-1">(Feedback sudah diisi)</small>
                                            @elseif($status == 'Belum Dinilai')
                                                <small class="text-muted d-block mt-1">(Isi feedback terlebih
                                                    dahulu)</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 flex-wrap">
                                                @if (!$feedbackCompleted)
                                                    {{-- Feedback belum diisi --}}
                                                    <a href="{{ route('dosen.penilaian.feedback', $item->id) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="bi bi-file-earmark-text"></i> Isi Feedback
                                                    </a>
                                                    <button class="btn btn-sm btn-secondary" disabled
                                                        title="Isi feedback terlebih dahulu">
                                                        <i class="bi bi-clipboard-check"></i> Penilaian
                                                    </button>
                                                @elseif (!$penilaianCompleted)
                                                    {{-- Feedback sudah diisi, penilaian belum --}}
                                                    <a href="{{ route('dosen.penilaian.feedback', $item->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i> Lihat Feedback
                                                    </a>
                                                    <a href="{{ route('dosen.penilaian.create', $item->id) }}"
                                                        class="btn btn-sm btn-success">
                                                        <i class="bi bi-clipboard-check"></i> Isi Penilaian
                                                    </a>
                                                @else
                                                    {{-- Feedback dan penilaian sudah selesai --}}
                                                    <a href="{{ route('dosen.penilaian.feedback', $item->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i> Lihat Feedback
                                                    </a>
                                                    <a href="{{ route('dosen.penilaian.show', $item->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="bi bi-file-text"></i> Lihat Penilaian
                                                    </a>
                                                @endif
                                            </div>
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

@push('styles')
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }

        .d-flex.gap-2 {
            gap: 0.5rem;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Untuk tampilan mobile */
        @media (max-width: 768px) {

            .table th,
            .table td {
                white-space: nowrap;
            }
        }
    </style>
@endpush
