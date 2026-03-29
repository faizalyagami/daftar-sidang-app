@extends('layouts.app')

@section('title', 'Data Mahasiswa')

@section('content')
<style>
    .status-skripsi {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        margin: 2px 0;
    }
    
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
</style>

<div class="card fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-people me-2"></i>
            Data Mahasiswa
        </h5>
        <div>
            <a href="{{ route('admin.mahasiswa.import') }}" class="btn btn-light btn-sm me-2">
                <i class="bi bi-upload"></i> Import Excel
            </a>
            <a href="{{ route('admin.mahasiswa.template') }}" class="btn btn-outline-light btn-sm">
                <i class="bi bi-download"></i> Download Template
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="mahasiswaTable">
                <thead>
                    <tr>
                        <th>NPM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Email</th>
                        <th>Dosen Wali</th>
                        <th>IPK</th>
                        <th>Tempat/Tgl Lahir</th>
                        <th>Status Pendaftaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mahasiswas as $mahasiswa)
                    @php
                        $skripsiStatus = $mahasiswa->pendaftaranSkripsi->last();
                        $metodologiStatus = $mahasiswa->pendaftaranMetodologi->last();
                        
                        // Status labels
                        $skripsiStatusText = '';
                        $skripsiStatusClass = '';
                        $metodologiStatusText = '';
                        $metodologiStatusClass = '';
                        
                        // Determine Skripsi Status
                        if (!$skripsiStatus) {
                            $skripsiStatusText = 'Belum Mendaftar';
                            $skripsiStatusClass = 'status-badge-pending';
                        } elseif ($skripsiStatus->status == 'pending') {
                            $skripsiStatusText = 'Menunggu Review';
                            $skripsiStatusClass = 'status-badge-pending';
                        } elseif ($skripsiStatus->status == 'review') {
                            $skripsiStatusText = 'Sedang Direview';
                            $skripsiStatusClass = 'status-badge-review';
                        } elseif ($skripsiStatus->status == 'approved') {
                            $skripsiStatusText = '✅ Lanjut Sidang';
                            $skripsiStatusClass = 'status-badge-approved';
                        } elseif ($skripsiStatus->status == 'revision') {
                            $skripsiStatusText = '⚠️ Perlu Revisi';
                            $skripsiStatusClass = 'status-badge-revision';
                        } elseif ($skripsiStatus->status == 'rejected') {
                            $skripsiStatusText = '❌ Ditolak';
                            $skripsiStatusClass = 'status-badge-rejected';
                        }
                        
                        // Determine Metodologi Status
                        if (!$metodologiStatus) {
                            $metodologiStatusText = 'Belum Mendaftar';
                            $metodologiStatusClass = 'status-badge-pending';
                        } elseif ($metodologiStatus->status == 'pending') {
                            $metodologiStatusText = 'Menunggu Review';
                            $metodologiStatusClass = 'status-badge-pending';
                        } elseif ($metodologiStatus->status == 'review') {
                            $metodologiStatusText = 'Sedang Direview';
                            $metodologiStatusClass = 'status-badge-review';
                        } elseif ($metodologiStatus->status == 'approved') {
                            $metodologiStatusText = '✅ Lanjut Ujian';
                            $metodologiStatusClass = 'status-badge-approved';
                        } elseif ($metodologiStatus->status == 'revision') {
                            $metodologiStatusText = '⚠️ Perlu Revisi';
                            $metodologiStatusClass = 'status-badge-revision';
                        } elseif ($metodologiStatus->status == 'rejected') {
                            $metodologiStatusText = '❌ Ditolak';
                            $metodologiStatusClass = 'status-badge-rejected';
                        }
                    @endphp
                    <tr>
                        <td class="align-middle">
                            <strong>{{ $mahasiswa->npm }}</strong>
                        </td>
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
                            <div class="status-wrapper">
                                <div class="status-label">
                                    <i class="bi bi-file-earmark-text"></i> SIDANG SKRIPSI
                                </div>
                                <span class="status-skripsi {{ $skripsiStatusClass }}">
                                    {{ $skripsiStatusText }}
                                </span>
                                <div class="status-label mt-2">
                                    <i class="bi bi-book"></i> UJIAN METODOLOGI
                                </div>
                                <span class="status-metodologi {{ $metodologiStatusClass }}">
                                    {{ $metodologiStatusText }}
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
                                @if($skripsiStatus && $skripsiStatus->status == 'approved')
                                    <button class="btn btn-sm btn-success" onclick="printSkripsi({{ $skripsiStatus->id }})" title="Cetak Surat Sidang">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                @endif
                                @if($metodologiStatus && $metodologiStatus->status == 'approved')
                                    <button class="btn btn-sm btn-success" onclick="printMetodologi({{ $metodologiStatus->id }})" title="Cetak Surat Ujian">
                                        <i class="bi bi-printer"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $mahasiswas->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#mahasiswaTable').DataTable({
            pageLength: 20,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            searching: true,
            ordering: true,
            columnDefs: [
                { orderable: false, targets: [6, 7] }
            ]
        });
    });
    
    function viewMahasiswa(id) {
        // Implement view detail modal with more info
        Swal.fire({
            title: 'Detail Mahasiswa',
            html: 'Loading...',
            showConfirmButton: false,
            willOpen: () => {
                $.ajax({
                    url: '/admin/mahasiswa/' + id + '/detail',
                    method: 'GET',
                    success: function(response) {
                        Swal.fire({
                            title: 'Detail Mahasiswa',
                            html: response,
                            confirmButtonText: 'Tutup',
                            confirmButtonColor: '#667eea'
                        });
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Gagal memuat data',
                            icon: 'error',
                            confirmButtonText: 'Tutup'
                        });
                    }
                });
            }
        });
    }
    
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
                $.ajax({
                    url: '/admin/users/' + userId + '/reset-password',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Password berhasil direset menjadi: password123',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745'
                        });
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Gagal mereset password',
                            icon: 'error',
                            confirmButtonText: 'Tutup'
                        });
                    }
                });
            }
        });
    }
    
    function printSkripsi(id) {
        window.open('/admin/pendaftaran/skripsi/' + id + '/print', '_blank');
    }
    
    function printMetodologi(id) {
        window.open('/admin/pendaftaran/metodologi/' + id + '/print', '_blank');
    }
</script>
@endpush