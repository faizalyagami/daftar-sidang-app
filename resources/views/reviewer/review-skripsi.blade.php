@extends('layouts.app')

@section('title', 'Review Pendaftaran Skripsi')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card fade-in">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        Review Pendaftaran Sidang Skripsi
                    </h5>
                    <small class="text-white-50">Periksa setiap dokumen dan berikan komentar jika ada yang tidak
                        sesuai</small>
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
                                <strong>Judul Skripsi:</strong> {{ $pendaftaran->judul_skripsi }}<br>
                                <strong>Dosen Pembimbing:</strong> {{ $pendaftaran->dosen_pembimbing }}
                            </div>
                        </div>
                    </div>

                    <!-- Tampilkan Status Review -->
                    @php
                        $isReviewCompleted = $pendaftaran->status != 'review';
                    @endphp

                    @if ($isReviewCompleted)
                        <div class="alert alert-success mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-2 me-3 text-success"></i>
                                <div>
                                    <h6 class="mb-1">✅ Review Telah Diselesaikan</h6>
                                    <p class="mb-0 text-muted">Review untuk pendaftaran ini sudah selesai.
                                        @if ($pendaftaran->status == 'approved')
                                            Pendaftaran telah disetujui untuk lanjut sidang.
                                        @elseif($pendaftaran->status == 'revision')
                                            Mahasiswa perlu melakukan revisi dokumen.
                                        @elseif($pendaftaran->status == 'rejected')
                                            Pendaftaran ditolak.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form id="reviewForm" action="{{ route('reviewer.skripsi.submit', $pendaftaran->id) }}" method="POST">
                        @csrf

                        <h6 class="mb-3">Review Dokumen</h6>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle"></i>
                            Silakan periksa setiap dokumen dan pilih status yang sesuai:
                            <strong>Valid</strong> (dokumen sesuai),
                            <strong>Invalid</strong> (dokumen tidak sesuai),
                            <strong>Perlu Upload Ulang</strong> (dokumen perlu diperbaiki)
                        </p>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30%">Jenis Dokumen</th>
                                        <th width="25%">Dokumen</th>
                                        <th width="20%">Status</th>
                                        <th width="25%">Komentar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendaftaran->dokumen as $dokumen)
                                        @php
                                            $review = $reviewMap[$dokumen->jenis_dokumen] ?? null;
                                            $isCompleted = $review && $review->is_completed;
                                            $dokumenName = '';
                                            switch ($dokumen->jenis_dokumen) {
                                                case 'bukti_pembayaran_registrasi':
                                                    $dokumenName = 'Bukti Pembayaran Registrasi Terakhir';
                                                    break;
                                                case 'bukti_pembayaran_sidang':
                                                    $dokumenName = 'Bukti Pembayaran Biaya Sidang';
                                                    break;
                                                case 'bukti_pembayaran_skripsi':
                                                    $dokumenName = 'Bukti Pembayaran Skripsi';
                                                    break;
                                                case 'frs':
                                                    $dokumenName = 'Formulir Rencana Studi (FRS)';
                                                    break;
                                                case 'transkrip_nilai':
                                                    $dokumenName = 'Transkrip Nilai Terakhir';
                                                    break;
                                                case 'surat_bebas_perpus':
                                                    $dokumenName =
                                                        'Surat Bebas Perpustakaan Pusat Universitas dan Perpustakaan BEM-F Psikologi Unisba';
                                                    break;
                                                case 'surat_bebas_alat_tes':
                                                    $dokumenName =
                                                        'Surat Bebas Peminjaman Alat Tes dan Material lain dari Lab. Psikologi Unisba';
                                                    break;
                                                case 'sertifikat_pesantren':
                                                    $dokumenName = 'Sertifikat Pesantren Calon Sarjana';
                                                    break;
                                                case 'sertifikat_sks_non_akademik':
                                                    $dokumenName = 'Sertifikat SKS Non Akademik';
                                                    break;
                                                case 'surat_lolos_turnitin':
                                                    $dokumenName = 'Surat Lolos Turn It In <25%';
                                                    break;
                                                case 'sertifikat_toefl':
                                                    $dokumenName = 'Sertifikat Lulus TOEFL (Min.475)';
                                                    break;
                                                case 'pas_foto':
                                                    $dokumenName = 'Pas Foto (Latar biru)';
                                                    break;
                                                case 'buku_bimbingan':
                                                    $dokumenName = 'Buku Bimbingan';
                                                    break;
                                                case 'surat_perbaikan':
                                                    $dokumenName = 'Surat perbaikan hasil seminar skripsi';
                                                    break;
                                                case 'surat_ijin_sidang':
                                                    $dokumenName = 'Surat Pernyataan Ijin Mengikuti Sidang Skripsi';
                                                    break;
                                                case 'berkas_skripsi':
                                                    $dokumenName = 'Berkas Skripsi Softcopy';
                                                    break;
                                                default:
                                                    $dokumenName = ucfirst(
                                                        str_replace('_', ' ', $dokumen->jenis_dokumen),
                                                    );
                                            }
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $dokumenName }}</strong>
                                                @if ($isReviewCompleted)
                                                    <br>
                                                    @if ($review && $review->status == 'valid')
                                                        <span class="badge bg-success mt-1">Sudah divalidasi</span>
                                                    @elseif($review && $review->status == 'invalid')
                                                        <span class="badge bg-danger mt-1">Sudah ditandai Invalid</span>
                                                    @elseif($review && $review->status == 'reupload')
                                                        <span class="badge bg-warning mt-1">Perlu Upload Ulang</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ Storage::url($dokumen->file_path) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Lihat Dokumen
                                                </a>
                                            </td>
                                            <td>
                                                <select name="reviews[{{ $dokumen->jenis_dokumen }}][status]"
                                                    class="form-select form-select-sm status-select"
                                                    data-dokumen="{{ $dokumen->jenis_dokumen }}"
                                                    onchange="toggleKomentar(this)"
                                                    {{ $isReviewCompleted ? 'disabled' : '' }} required>
                                                    <option value="" disabled {{ !$review ? 'selected' : '' }}>--
                                                        Pilih Status --</option>
                                                    <option value="valid"
                                                        {{ $review && $review->status == 'valid' ? 'selected' : '' }}>✓
                                                        Valid</option>
                                                    <option value="invalid"
                                                        {{ $review && $review->status == 'invalid' ? 'selected' : '' }}>✗
                                                        Invalid</option>
                                                    <option value="reupload"
                                                        {{ $review && $review->status == 'reupload' ? 'selected' : '' }}>↻
                                                        Perlu Upload Ulang</option>
                                                </select>
                                                @if ($isReviewCompleted && $review && $review->status)
                                                    <div class="small text-muted mt-1">
                                                        Status sudah ditetapkan, tidak dapat diubah.
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <textarea name="reviews[{{ $dokumen->jenis_dokumen }}][komentar]"
                                                    class="form-control form-control-sm komentar-textarea" rows="2"
                                                    placeholder="Berikan komentar jika dokumen tidak sesuai..." data-dokumen="{{ $dokumen->jenis_dokumen }}"
                                                    {{ $isReviewCompleted ? 'readonly' : '' }}>{{ $review->komentar ?? '' }}</textarea>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Catatan Keseluruhan -->
                        <div class="mt-4">
                            <label class="form-label fw-bold">Catatan Keseluruhan</label>
                            <textarea name="overall_notes" class="form-control" rows="3" placeholder="Berikan catatan umum untuk mahasiswa..."
                                {{ $isReviewCompleted ? 'readonly' : '' }}>{{ $pendaftaran->reviewer_notes }}</textarea>
                            @if ($isReviewCompleted && $pendaftaran->reviewer_notes)
                                <div class="small text-muted mt-1">
                                    Catatan sudah disimpan dan tidak dapat diubah.
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('reviewer.dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            @if (!$isReviewCompleted)
                                <button type="button" class="btn btn-primary" onclick="confirmReview()">
                                    <i class="bi bi-save"></i> Simpan Review
                                </button>
                            @else
                                <a href="{{ route('reviewer.dashboard') }}" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Kembali ke Dashboard
                                </a>
                            @endif
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
                komentarTextarea.placeholder = select.value === 'invalid' ?
                    'Wajib diisi: Jelaskan mengapa dokumen ini tidak valid...' :
                    'Wajib diisi: Jelaskan perbaikan yang diperlukan...';
                komentarTextarea.style.borderColor = '#dc3545';
                komentarTextarea.style.backgroundColor = '#fff3cd';
            } else {
                komentarTextarea.required = false;
                komentarTextarea.placeholder = 'Berikan komentar jika dokumen tidak sesuai...';
                komentarTextarea.style.borderColor = '';
                komentarTextarea.style.backgroundColor = '';
            }
        }

        function confirmReview() {
            let hasEmptyStatus = false;
            let hasEmptyComment = false;
            let errorMessages = [];

            // Reset styling
            document.querySelectorAll('.status-select').forEach(select => {
                select.style.borderColor = '';
            });
            document.querySelectorAll('.komentar-textarea').forEach(textarea => {
                textarea.style.borderColor = '';
                textarea.style.backgroundColor = '';
            });

            document.querySelectorAll('.status-select').forEach(select => {
                const dokumen = select.dataset.dokumen;
                const komentar = document.querySelector(`.komentar-textarea[data-dokumen="${dokumen}"]`);
                const row = select.closest('tr');
                const namaDokumen = row.querySelector('td:first-child strong')?.innerText || dokumen;

                if (!select.value || select.value === '') {
                    hasEmptyStatus = true;
                    select.style.borderColor = '#dc3545';
                    errorMessages.push(`- ${namaDokumen}: status belum dipilih`);
                }

                if (select.value === 'invalid' || select.value === 'reupload') {
                    if (!komentar.value || !komentar.value.trim()) {
                        hasEmptyComment = true;
                        komentar.style.borderColor = '#dc3545';
                        komentar.style.backgroundColor = '#fff3cd';
                        errorMessages.push(
                            `- ${namaDokumen}: komentar wajib diisi (status: ${select.value === 'invalid' ? 'Invalid' : 'Perlu Upload Ulang'})`
                        );
                    }
                }
            });

            if (hasEmptyStatus || hasEmptyComment) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Review Belum Lengkap',
                    html: `<div class="text-left">${errorMessages.join('<br>')}</div>`,
                    confirmButtonColor: '#6366f1'
                });
                return false;
            }

            // Jika semua sudah diisi
            Swal.fire({
                title: 'Simpan Hasil Review?',
                html: `
            <div class="text-start">
                <p class="mb-2">
                    <i class="bi bi-person-fill text-primary"></i>
                    Review untuk mahasiswa akan disimpan.
                </p>
                <p class="mb-0">
                    <i class="bi bi-bell-fill text-warning"></i>
                    Mahasiswa akan menerima notifikasi hasil review.
                </p>
            </div>
        `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-check-circle"></i> Ya, Simpan',
                cancelButtonText: '<i class="bi bi-x-circle"></i> Batal',
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form
                    document.getElementById('reviewForm').submit();
                }
            });

            return false;
        }

        // Initialize komentar fields on page load
        document.querySelectorAll('.status-select').forEach(select => {
            if (select.value) {
                toggleKomentar(select);
            }
        });
    </script>
@endpush
