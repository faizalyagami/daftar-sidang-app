@extends('layouts.app')

@section('title', 'Data Mahasiswa')

@section('content')
<style>
    .status-skripsi,
    .status-metodologi {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        margin: 2px 0;
    }

    .status-badge-pending {
        background: #e9ecef;
        color: #6c757d;
    }

    .status-badge-approved {
        background: #d4edda;
        color: #155724;
    }

    .status-badge-revision {
        background: #fff3cd;
        color: #856404;
    }

    .status-badge-review {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-badge-rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .status-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 3px;
        color: #6c757d;
    }

    .status-wrapper {
        min-width: 180px;
    }

    /* Custom Pagination */
    .custom-pagination {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 20px;
        padding: 15px 20px;
        background: #f8f9fa;
        border-radius: 12px;
    }

    .pagination-info {
        color: #6c757d;
        font-size: 14px;
        margin-right: auto;
    }

    .pagination {
        margin: 0;
        gap: 5px;
    }

    .pagination .page-link {
        border: none;
        padding: 8px 14px;
        font-size: 14px;
        color: #4a5568;
        background: white;
        border-radius: 10px;
        transition: all 0.3s;
        font-weight: 500;
    }

    .pagination .page-link:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
    }

    .pagination .active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .per-page-selector {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-left: 20px;
    }

    .per-page-selector label {
        margin: 0;
        font-size: 13px;
        color: #6c757d;
    }

    .duration-badge {
        font-size: 12px;
        font-weight: 500;
        color: #667eea;
        background: #f0f4ff;
        padding: 4px 8px;
        border-radius: 12px;
        display: inline-block;
    }
</style>

<div class="card fade-in border-0 shadow-sm">
    <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="mb-0">
            <i class="bi bi-people me-2"></i>
            Data Mahasiswa
        </h5>
        <div class="mt-2 mt-sm-0">
            <a href="{{ route('admin.mahasiswa.import') }}" class="btn btn-light btn-sm me-2">
                <i class="bi bi-upload"></i> Import Excel
            </a>
            <a href="{{ route('admin.mahasiswa.template') }}" class="btn btn-outline-light btn-sm">
                <i class="bi bi-download"></i> Download Template
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <!-- Filter Periode -->
        <div class="px-4 pt-4 pb-2">
            <form method="GET" action="{{ route('admin.mahasiswa.index') }}" class="row g-3 align-items-end">
                <div class="col-auto">
                    <label class="form-label fw-semibold">Filter Periode Pendaftaran</label>
                    <select name="period_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Periode</option>
                        @foreach($periods as $period)
                        <option value="{{ $period->id }}" {{ request('period_id') == $period->id ? 'selected' : '' }}>
                            {{ $period->semester }} {{ $period->tahun_akademik }}
                            <!-- ({{ $period->start_date->format('d/m/Y') }} - {{ $period->end_date->format('d/m/Y') }}) -->
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0" id="mahasiswaTable">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NPM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Email</th>
                        <th>Dosen Wali</th>
                        <th>IPK</th>
                        <th>Tempat/Tgl Lahir</th>
                        <th>Lama Skripsi</th>
                        <th>Lama Metodologi</th>
                        <th>Riwayat Skripsi</th>
                        <th>Riwayat Metodologi</th>
                        <th>Status Pendaftaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswas as $index => $mahasiswa)
                    {{-- baris data --}}
                    <tr>
                        <td class="align-middle">{{ ($mahasiswas->currentPage() - 1) * $mahasiswas->perPage() + $index + 1 }}</td>
                        <td class="align-middle"><strong>{{ $mahasiswa->npm }}</strong></td>
                        <td class="align-middle">{{ $mahasiswa->user->name }}</td>
                        <td class="align-middle">{{ $mahasiswa->user->email }}</td>
                        <td class="align-middle">{{ $mahasiswa->dosen_wali ?? '-' }}</td>
                        <td class="align-middle">
                            @if($mahasiswa->ipk)
                            <span class="badge bg-{{ $mahasiswa->ipk >= 3.0 ? 'success' : ($mahasiswa->ipk >= 2.5 ? 'warning' : 'danger') }}">
                                {{ number_format($mahasiswa->ipk, 3) }}
                            </span>
                            @else
                            -
                            @endif
                        </td>
                        <td class="align-middle">
                            @if($mahasiswa->tempat_lahir && $mahasiswa->tanggal_lahir)
                            {{ $mahasiswa->tempat_lahir }}, {{ $mahasiswa->tanggal_lahir->format('d/m/Y') }}
                            @else
                            -
                            @endif
                        </td>
                        <td class="align-middle">
                            @php $durasiSkripsi = $mahasiswa->getSkripsiDuration(); @endphp
                            @if($durasiSkripsi)
                            <span class="duration-badge">{{ $durasiSkripsi }}</span>
                            @else
                            -
                            @endif
                        </td>
                        <td class="align-middle">
                            @php $durasiMetodologi = $mahasiswa->getMetodologiDuration(); @endphp
                            @if($durasiMetodologi)
                            <span class="duration-badge">{{ $durasiMetodologi }}</span>
                            @else
                            -
                            @endif
                        </td>
                        <td class="align-middle">
                            @php $riwayatSkripsi = $mahasiswa->getSkripsiPeriods(); @endphp
                            @if($riwayatSkripsi->count())
                            <div class="d-flex align-items-center">
                                <div class="d-flex gap-1">
                                    @foreach($riwayatSkripsi as $period)
                                    <span class="badge bg-primary period-badge" title="{{ $period }}">{{ $loop->iteration }}</span>
                                    <span class="badge bg-primary period-badge">{{ $period }}</span>
                                    @endforeach
                                </div>
                                <i class="bi bi-info-circle info-icon" onclick="showPeriods('skripsi', {{ $mahasiswa->id }})"></i>
                            </div>
                            @else
                            -
                            @endif
                        </td>
                        <td class="align-middle">
                            @php $riwayatMetodologi = $mahasiswa->getMetodologiPeriods(); @endphp
                            @if($riwayatMetodologi->count())
                            <div class="d-flex align-items-center">
                                <div class="d-flex gap-1">
                                    @foreach($riwayatMetodologi as $period)
                                    <span class="badge bg-success period-badge" title="{{ $period }}">{{ $loop->iteration }}</span>
                                    @endforeach
                                </div>
                                <i class="bi bi-info-circle info-icon" onclick="showPeriods('metodologi', {{ $mahasiswa->id }})"></i>
                            </div>
                            @else
                            -
                            @endif
                        </td>
                        <td class="align-middle">
                            <div class="status-wrapper">
                                <div class="status-label">
                                    <i class="bi bi-file-earmark-text"></i> SIDANG SKRIPSI
                                </div>
                                <span class="status-skripsi {{ $skripsiStatusClass ?? 'status-badge-pending' }}">
                                    {{ $skripsiStatusText ?? 'Belum Mendaftar' }}
                                </span>
                                <div class="status-label mt-2">
                                    <i class="bi bi-book"></i> UJIAN METODOLOGI
                                </div>
                                <span class="status-metodologi {{ $metodologiStatusClass ?? 'status-badge-pending' }}">
                                    {{ $metodologiStatusText ?? 'Belum Mendaftar' }}
                                </span>
                            </div>
                        </td>
                        <td class="align-middle">
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-info" onclick="viewMahasiswa({{ $mahasiswa->id }})" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="resetPassword({{ $mahasiswa->user->id }})" title="Reset Password">
                                    <i class="bi bi-key"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Tidak ada data mahasiswa yang ditemukan</p>
                            @if(request('period_id'))
                            <p class="text-muted small mt-1">Tidak ada mahasiswa yang terdaftar pada periode yang dipilih.</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Custom Pagination -->
        <div class="custom-pagination">
            <div class="pagination-info">
                <i class="bi bi-info-circle me-1"></i>
                Menampilkan
                <strong>{{ $mahasiswas->firstItem() ?? 0 }}</strong>
                sampai
                <strong>{{ $mahasiswas->lastItem() ?? 0 }}</strong>
                dari
                <strong>{{ $mahasiswas->total() }}</strong>
                data
                @if(request('period_id'))
                <span class="text-muted"> - Filter periode: {{ $periods->where('id', request('period_id'))->first()->semester ?? '' }} {{ $periods->where('id', request('period_id'))->first()->tahun_akademik ?? '' }}</span>
                @endif
            </div>
            <div class="d-flex align-items-center">
                {{ $mahasiswas->appends(request()->query())->links('pagination::bootstrap-4') }}
                <div class="per-page-selector">
                    <label>Baris per halaman:</label>
                    <select id="perPageSelect" class="form-select form-select-sm">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal View User -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable hanya jika tabel memiliki data
        var table = $('#mahasiswaTable');
        var hasData = table.find('tbody tr:not(.no-data)').length > 0;

        if (hasData) {
            table.DataTable({
                paging: false,
                searching: true,
                ordering: true,
                columnDefs: [{
                        orderable: false,
                        targets: [11, 12]
                    } // kolom status dan aksi
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
                    emptyTable: 'Tidak ada data yang tersedia'
                }
            });
        } else {
            // Inisialisasi DataTable tanpa fitur sorting pada kolom tertentu
            table.DataTable({
                paging: false,
                searching: true,
                ordering: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
                    emptyTable: 'Tidak ada data mahasiswa yang ditemukan'
                }
            });
        }
    });

    // Per Page Selector
    document.getElementById('perPageSelect')?.addEventListener('change', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', this.value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    });

    // View Mahasiswa Detail
    function viewMahasiswa(id) {
        const modal = new bootstrap.Modal(document.getElementById('userModal'));
        const modalBody = document.getElementById('userModalBody');
        modal.show();
        modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        fetch('/admin/mahasiswa/' + id + '/detail')
            .then(response => response.text())
            .then(html => {
                modalBody.innerHTML = html;
            })
            .catch(() => {
                modalBody.innerHTML = '<div class="alert alert-danger">Gagal memuat data mahasiswa.</div>';
            });
    }

    // Reset Password
    function resetPassword(userId) {
        Swal.fire({
            title: 'Reset Password?',
            text: 'Password akan direset menjadi "password123"',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/admin/users/' + userId + '/reset-password', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat mereset password.', 'error');
                    });
            }
        });
    }

    // Show period details
    function showPeriods(jenis, mahasiswaId) {
        fetch(`/admin/mahasiswa/${mahasiswaId}/periods?jenis=${jenis}`)
            .then(response => response.json())
            .then(data => {
                let html = '<ul class="list-group">';
                if (data.length === 0) {
                    html += '<li class="list-group-item text-muted">Belum ada riwayat</li>';
                } else {
                    data.forEach((period, idx) => {
                        html += `<li class="list-group-item">${idx+1}. ${period}</li>`;
                    });
                }
                html += '</ul>';
                Swal.fire({
                    title: `Riwayat ${jenis == 'skripsi' ? 'Skripsi' : 'Metodologi'}`,
                    html: html,
                    icon: 'info',
                    confirmButtonText: 'Tutup'
                });
            })
            .catch(() => {
                Swal.fire('Error', 'Gagal memuat data riwayat', 'error');
            });
    }
</script>
@endpush