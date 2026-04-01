@extends('layouts.app')

@section('title', 'Detail Pendaftaran Metodologi')

@section('content')
<style>
    .detail-section {
        background: white;
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .detail-title {
        font-size: 16px;
        font-weight: 600;
        color: #667eea;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }
    .detail-label {
        font-size: 13px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 5px;
    }
    .detail-value {
        font-size: 14px;
        color: #2d3748;
        margin-bottom: 15px;
        word-break: break-word;
    }
    .document-card {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 15px;
        margin-bottom: 12px;
        transition: all 0.2s;
        border-left: 4px solid #667eea;
    }
    .document-card:hover {
        transform: translateX(5px);
        background: #f0f4ff;
    }
    .document-name {
        font-weight: 600;
        margin-bottom: 5px;
    }
    .document-date {
        font-size: 11px;
        color: #adb5bd;
    }
    .btn-back {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 10px;
        border-radius: 12px;
        color: white;
        font-weight: 500;
        transition: all 0.3s;
    }
    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102,126,234,0.3);
        color: white;
    }
</style>

<div class="container-fluid px-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="detail-section">
                <div class="detail-title">
                    <i class="bi bi-book me-2"></i>Informasi Pendaftaran
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <span class="status-badge status-{{ $pendaftaran->status }}">
                                {{ ucfirst($pendaftaran->status) }}
                            </span>
                        </div>

                        <div class="detail-label">Tanggal Pendaftaran</div>
                        <div class="detail-value">{{ $pendaftaran->created_at->format('d F Y H:i') }}</div>

                        <div class="detail-label">Nama Mahasiswa</div>
                        <div class="detail-value">{{ $pendaftaran->mahasiswa->user->name }}</div>

                        <div class="detail-label">NPM</div>
                        <div class="detail-value">{{ $pendaftaran->mahasiswa->npm }}</div>

                        <div class="detail-label">Email</div>
                        <div class="detail-value">{{ $pendaftaran->email }}</div>

                        <div class="detail-label">No Handphone</div>
                        <div class="detail-value">{{ $pendaftaran->mahasiswa->no_hp }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-label">Judul Penelitian</div>
                        <div class="detail-value">{{ $pendaftaran->judul_penelitian }}</div>

                        <div class="detail-label">Dosen Pembimbing 1</div>
                        <div class="detail-value">{{ $pendaftaran->dosen_pembimbing }}</div>

                        @if($pendaftaran->dosen_pembimbing_2)
                        <div class="detail-label">Dosen Pembimbing 2</div>
                        <div class="detail-value">{{ $pendaftaran->dosen_pembimbing_2 }}</div>
                        @endif

                        <div class="detail-label">Kuliah Peminatan</div>
                        <div class="detail-value">{{ $pendaftaran->kuliah_peminatan }}</div>
                    </div>
                </div>

                @if($pendaftaran->reviewer_notes)
                <div class="alert alert-info mt-3">
                    <i class="bi bi-chat-dots me-2"></i>
                    <strong>Catatan Reviewer:</strong><br>
                    {{ $pendaftaran->reviewer_notes }}
                </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="detail-section">
                <div class="detail-title">
                    <i class="bi bi-files me-2"></i>Dokumen yang Diupload
                </div>
                @forelse($pendaftaran->dokumen as $dokumen)
                <a href="{{ Storage::url($dokumen->file_path) }}" target="_blank" class="text-decoration-none">
                    <div class="document-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="document-name">
                                    @switch($dokumen->jenis_dokumen)
                                        @case('proposal_word')
                                            <i class="bi bi-file-word me-1"></i> Proposal Penelitian (Word)
                                            @break
                                        @case('kartu_bimbingan')
                                            <i class="bi bi-card-list me-1"></i> Kartu Bimbingan
                                            @break
                                        @case('surat_ijin_ujian')
                                            <i class="bi bi-file-check me-1"></i> Surat Ijin Mengikuti Ujian
                                            @break
                                        @case('lembar_pengesahan')
                                            <i class="bi bi-file-text me-1"></i> Lembar Pengesahan
                                            @break
                                        default:
                                            {{ str_replace('_', ' ', ucfirst($dokumen->jenis_dokumen)) }}
                                    @endswitch
                                </div>
                                <div class="document-date">
                                    <i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($dokumen->created_at)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <i class="bi bi-download text-primary"></i>
                        </div>
                    </div>
                </a>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2 mb-0">Tidak ada dokumen</p>
                </div>
                @endforelse
            </div>

            <div class="detail-section">
                <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-back w-100">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection