@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card fade-in border-0 shadow-sm">
                    <div class="card-header-custom">
                        <h5 class="mb-0">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard Dosen
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Selamat datang, <strong>{{ Auth::user()->name }}</strong>! Anda login sebagai Dosen Penguji.
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6 mb-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body text-center py-5">
                                        <i class="bi bi-clipboard-check fs-1 text-primary"></i>
                                        <h5 class="mt-3">Penilaian Sidang Skripsi</h5>
                                        <p class="text-muted">Memberikan nilai kepada mahasiswa yang sedang sidang</p>
                                        <a href="{{ route('dosen.penilaian.index') }}" class="btn btn-primary mt-2">
                                            <i class="bi bi-arrow-right"></i> Mulai Penilaian
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body text-center py-5">
                                        <i class="bi bi-calendar-check fs-1 text-success"></i>
                                        <h5 class="mt-3">Jadwal Sidang</h5>
                                        <p class="text-muted">Lihat jadwal sidang yang akan datang</p>
                                        <a href="{{ route('dosen.penilaian.jadwal') }}" class="btn btn-success mt-2">
                                            <i class="bi bi-arrow-right"></i> Lihat Jadwal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header-custom">
                                        <h5 class="mb-0">Informasi Akun</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td width="30%"><strong>Nama</strong></td>
                                                        <td>: {{ Auth::user()->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Email</strong></td>
                                                        <td>: {{ Auth::user()->email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Username</strong></td>
                                                        <td>: {{ Auth::user()->username ?? '-' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td width="30%"><strong>Role</strong></td>
                                                        <td>: {{ ucfirst(Auth::user()->role->role ?? '-') }}</td>
                                                    </tr>
                                                    @if (Auth::user()->dosen)
                                                        <tr>
                                                            <td><strong>NIK</strong></td>
                                                            <td>: {{ Auth::user()->dosen->nik ?? '-' }}</td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
