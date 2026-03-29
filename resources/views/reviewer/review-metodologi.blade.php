@extends('layouts.app')

@section('title', 'Review Pendaftaran Metodologi')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card fade-in">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-book me-2"></i>
                    Review Pendaftaran Ujian Metodologi Penelitian
                </h5>
                <small class="text-white-50">Periksa setiap dokumen dan berikan komentar jika ada yang tidak sesuai</small>
            </div>
            <div class="card-body">
                <!-- Informasi Mahasiswa -->
                <div class="alert alert-info mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nama:</strong> {{ $pendaftaran->mahasiswa->user->name }}<br>
                            <strong>NPM:</strong> {{ $pendaftaran->mahasiswa->npm }}
                        </div>
                        <div class="col-md-6">
                            <strong>Judul Penelitian:</strong> {{ $pendaftaran->judul_penelitian }}<br>
                            <strong>Dosen Pembimbing:</strong> {{ $pendaftaran->dosen_pembimbing }}
                            @if($pendaftaran->dosen_pembimbing_2)
                                <br><strong>Pembimbing 2:</strong> {{ $pendaftaran->dosen_pembimbing_2 }}
                            @endif
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('reviewer.metodologi.submit', $pendaftaran->id) }}" method="POST">
                    @csrf
                    
                    <h6 class="mb-3">Review Dokumen</h6>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Jenis Dokumen</th>
                                    <th width="30%">Dokumen</th>
                                    <th width="20%">Status</th>
                                    <th width="20%">Komentar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendaftaran->dokumen as $dokumen)
                                @php
                                    $review = $reviewMap[$dokumen->jenis_dokumen] ?? null;
                                    $dokumenName = '';
                                    switch($dokumen->jenis_dokumen) {
                                        case 'proposal_word':
                                            $dokumenName = 'Berkas Proposal (Format Word)';
                                            break;
                                        case 'kartu_bimbingan':
                                            $dokumenName = 'Kartu Bimbingan';
                                            break;
                                        case 'surat_ijin_ujian':
                                            $dokumenName = 'Pernyataan Ijin Mengikuti Ujian Metpen';
                                            break;
                                        case 'lembar_pengesahan':
                                            $dokumenName = 'Lembar Pengesahan (Ttd Pembimbing)';
                                            break;
                                        default:
                                            $dokumenName = ucfirst(str_replace('_', ' ', $dokumen->jenis_dokumen));
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $dokumenName }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ Storage::url($dokumen->file_path) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Lihat Dokumen
                                        </a>
                                    </td>
                                    <td>
                                        <select name="reviews[{{ $dokumen->jenis_dokumen }}][status]" 
                                                class="form-select form-select-sm status-select"
                                                data-dokumen="{{ $dokumen->jenis_dokumen }}"
                                                onchange="toggleKomentar(this)">
                                            <option value="valid" {{ $review && $review->status == 'valid' ? 'selected' : '' }}>
                                                ✓ Valid
                                            </option>
                                            <option value="invalid" {{ $review && $review->status == 'invalid' ? 'selected' : '' }}>
                                                ✗ Invalid
                                            </option>
                                            <option value="reupload" {{ $review && $review->status == 'reupload' ? 'selected' : '' }}>
                                                ↻ Perlu Upload Ulang
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <textarea name="reviews[{{ $dokumen->jenis_dokumen }}][komentar]" 
                                                  class="form-control form-control-sm komentar-textarea" 
                                                  rows="2"
                                                  placeholder="Berikan komentar jika dokumen tidak sesuai..."
                                                  data-dokumen="{{ $dokumen->jenis_dokumen }}">{{ $review->komentar ?? '' }}</textarea>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Catatan Keseluruhan -->
                    <div class="mt-4">
                        <label class="form-label fw-bold">Catatan Keseluruhan (Opsional)</label>
                        <textarea name="overall_notes" 
                                  class="form-control" 
                                  rows="3"
                                  placeholder="Berikan catatan umum untuk mahasiswa...">{{ $pendaftaran->reviewer_notes }}</textarea>
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('reviewer.dashboard') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" onclick="return confirmReview()">
                            <i class="bi bi-save"></i> Simpan Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleKomentar(select) {
        const dokumen = select.getAttribute('data-dokumen');
        const komentarTextarea = document.querySelector(`.komentar-textarea[data-dokumen="${dokumen}"]`);
        
        if (select.value === 'invalid' || select.value === 'reupload') {
            komentarTextarea.required = true;
            komentarTextarea.placeholder = select.value === 'invalid' 
                ? 'Wajib diisi: Jelaskan mengapa dokumen ini tidak valid...' 
                : 'Wajib diisi: Jelaskan perbaikan yang diperlukan...';
        } else {
            komentarTextarea.required = false;
            komentarTextarea.placeholder = 'Berikan komentar jika dokumen tidak sesuai...';
        }
    }
    
    function confirmReview() {
        let hasEmptyComment = false;
        document.querySelectorAll('.status-select').forEach(select => {
            const dokumen = select.getAttribute('data-dokumen');
            const komentar = document.querySelector(`.komentar-textarea[data-dokumen="${dokumen}"]`);
            
            if ((select.value === 'invalid' || select.value === 'reupload') && !komentar.value.trim()) {
                hasEmptyComment = true;
                komentar.style.borderColor = 'red';
                alert(`Dokumen "${select.closest('tr').querySelector('td:first-child strong').innerText}" membutuhkan komentar.`);
            } else {
                komentar.style.borderColor = '';
            }
        });
        
        if (hasEmptyComment) {
            return false;
        }
        
        return confirm('Apakah Anda yakin dengan review ini? Mahasiswa akan menerima notifikasi.');
    }
    
    // Initialize komentar fields on page load
    document.querySelectorAll('.status-select').forEach(select => {
        toggleKomentar(select);
    });
</script>
@endpush