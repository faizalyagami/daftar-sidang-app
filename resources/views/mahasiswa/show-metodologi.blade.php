@extends('layouts.app')

@section('title', 'Detail Pendaftaran Metodologi')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Detail Pendaftaran Ujian Metodologi Penelitian</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Status:</div>
                    <div class="col-md-8">
                        <span class="status-badge status-{{ $pendaftaran->status }}">
                            {{ ucfirst($pendaftaran->status) }}
                        </span>
                        @if($pendaftaran->reviewer_notes)
                            <div class="alert alert-info mt-2">
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
                    <div class="col-md-4 fw-bold">Email:</div>
                    <div class="col-md-8">{{ $pendaftaran->email }}</div>
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
                    <div class="col-md-4 fw-bold">No Handphone:</div>
                    <div class="col-md-8">{{ $pendaftaran->mahasiswa->no_hp }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Judul Penelitian:</div>
                    <div class="col-md-8">{{ $pendaftaran->judul_penelitian }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Dosen Pembimbing 1:</div>
                    <div class="col-md-8">{{ $pendaftaran->dosen_pembimbing }}</div>
                </div>
                
                @if($pendaftaran->dosen_pembimbing_2)
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Dosen Pembimbing 2:</div>
                    <div class="col-md-8">{{ $pendaftaran->dosen_pembimbing_2 }}</div>
                </div>
                @endif
                
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Kuliah Peminatan:</div>
                    <div class="col-md-8">{{ $pendaftaran->kuliah_peminatan }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Dokumen yang Diupload</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($pendaftaran->dokumen as $dokumen)
                        <a href="{{ Storage::url($dokumen->file_path) }}" 
                           class="list-group-item list-group-item-action" 
                           target="_blank">
                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                            @switch($dokumen->jenis_dokumen)
                                @case('proposal_word')
                                    Proposal Penelitian (Word)
                                    @break
                                @case('kartu_bimbingan')
                                    Kartu Bimbingan
                                    @break
                                @case('surat_ijin_ujian')
                                    Surat Ijin Mengikuti Ujian
                                    @break
                                @case('lembar_pengesahan')
                                    Lembar Pengesahan
                                    @break
                                @default
                                    {{ $dokumen->jenis_dokumen }}
                            @endswitch
                            <small class="text-muted d-block mt-1">
                                {{ \Carbon\Carbon::parse($dokumen->created_at)->format('d/m/Y H:i') }}
                            </small>
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