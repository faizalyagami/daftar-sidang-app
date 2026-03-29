@extends('layouts.app')

@section('title', 'Detail Pendaftaran Skripsi')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card fade-in">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Detail Pendaftaran Sidang Skripsi
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Status:</div>
                    <div class="col-md-8">
                        <span class="status-badge status-{{ $pendaftaran->status }}">
                            {{ ucfirst($pendaftaran->status) }}
                        </span>
                        @if($pendaftaran->reviewer_notes)
                            <div class="alert alert-info mt-3">
                                <i class="bi bi-chat-dots me-2"></i>
                                <strong>Catatan Reviewer:</strong><br>
                                {{ $pendaftaran->reviewer_notes }}
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Tanggal Pendaftaran:</div>
                    <div class="col-md-8">{{ $pendaftaran->created_at->format('d F Y H:i') }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Nama Mahasiswa:</div>
                    <div class="col-md-8">{{ $pendaftaran->mahasiswa->user->name }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">NPM:</div>
                    <div class="col-md-8">{{ $pendaftaran->mahasiswa->npm }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Judul Skripsi:</div>
                    <div class="col-md-8">{{ $pendaftaran->judul_skripsi }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Dosen Pembimbing:</div>
                    <div class="col-md-8">{{ $pendaftaran->dosen_pembimbing }}</div>
                </div>
                
                @if($pendaftaran->narasumber)
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Narasumber:</div>
                    <div class="col-md-8">{{ $pendaftaran->narasumber }}</div>
                </div>
                @endif
                
                @if($pendaftaran->tanggal_seminar)
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Tanggal Seminar:</div>
                    <div class="col-md-8">{{ \Carbon\Carbon::parse($pendaftaran->tanggal_seminar)->format('d F Y') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-files me-2"></i>
                    Dokumen Pendukung
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($pendaftaran->dokumen as $dokumen)
                        <a href="{{ Storage::url($dokumen->file_path) }}" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                           target="_blank">
                            <div>
                                <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                @switch($dokumen->jenis_dokumen)
                                    @case('bukti_pembayaran_registrasi')
                                        Bukti Pembayaran Registrasi
                                        @break
                                    @case('bukti_pembayaran_sidang')
                                        Bukti Pembayaran Sidang
                                        @break
                                    @case('bukti_pembayaran_skripsi')
                                        Bukti Pembayaran Skripsi
                                        @break
                                    @case('frs')
                                        Formulir Rencana Studi (FRS)
                                        @break
                                    @case('transkrip_nilai')
                                        Transkrip Nilai
                                        @break
                                    @case('surat_bebas_perpus')
                                        Surat Bebas Perpustakaan
                                        @break
                                    @case('surat_bebas_alat_tes')
                                        Surat Bebas Alat Tes
                                        @break
                                    @case('sertifikat_pesantren')
                                        Sertifikat Pesantren
                                        @break
                                    @case('sertifikat_sks_non_akademik')
                                        Sertifikat SKS Non Akademik
                                        @break
                                    @case('surat_lolos_turnitin')
                                        Surat Lolos Turnitin
                                        @break
                                    @case('sertifikat_toefl')
                                        Sertifikat TOEFL
                                        @break
                                    @case('pas_foto')
                                        Pas Foto
                                        @break
                                    @case('buku_bimbingan')
                                        Buku Bimbingan
                                        @break
                                    @case('surat_perbaikan')
                                        Surat Perbaikan
                                        @break
                                    @case('surat_ijin_sidang')
                                        Surat Ijin Sidang
                                        @break
                                    @case('berkas_skripsi')
                                        Berkas Skripsi
                                        @break
                                    default:
                                        {{ $dokumen->jenis_dokumen }}
                                @endswitch
                            </div>
                            <i class="bi bi-download"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-body">
                <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary w-100">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection