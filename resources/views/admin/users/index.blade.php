@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<style>
    .users-table-container {
        background: white;
        border-radius: 20px;
        overflow: hidden;
    }
    
    .table-users {
        margin-bottom: 0;
    }
    
    .table-users thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        color: #4a5568;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 15px;
    }
    
    .table-users tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        color: #4a5568;
    }
    
    .table-users tbody tr:hover {
        background: #f8f9fa;
    }
    
    /* Custom Pagination Styles */
    .custom-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 20px;
        padding: 15px 20px;
        background: #f8f9fa;
        border-radius: 12px;
    }
    
    .pagination-info {
        color: #6c757d;
        font-size: 14px;
    }
    
    .pagination {
        margin: 0;
        gap: 5px;
    }
    
    .pagination .page-item {
        list-style: none;
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
    
    .pagination .disabled .page-link {
        color: #cbd5e0;
        background: #f1f3f5;
        cursor: not-allowed;
    }
    
    .pagination .page-link i {
        font-size: 12px;
    }
    
    /* Per Page Selector */
    .per-page-selector {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .per-page-selector label {
        margin: 0;
        font-size: 13px;
        color: #6c757d;
    }
    
    .per-page-selector select {
        padding: 6px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: white;
        font-size: 13px;
        cursor: pointer;
    }
    
    .badge-role {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .badge-admin {
        background: #dc2626;
        color: white;
    }
    
    .badge-reviewer {
        background: #f59e0b;
        color: white;
    }
    
    .badge-mahasiswa {
        background: #06b6d4;
        color: white;
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
    }
    
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
    }
    
    .user-avatar-mini {
        width: 32px;
        height: 32px;
        background: #e9ecef;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
    }
    
    /* Nomor urut styling */
    .no-column {
        width: 60px;
        text-align: center;
        font-weight: 600;
        color: #667eea;
    }
    
    @media (max-width: 768px) {
        .custom-pagination {
            flex-direction: column;
            align-items: center;
        }
        
        .pagination-info {
            text-align: center;
        }
    }
</style>

<div class="card fade-in border-0 shadow-sm">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-people me-2"></i>
            Kelola User
        </h5>
        <a href="{{ route('admin.users.create') }}" class="btn btn-light btn-sm">
            <i class="bi bi-plus-circle"></i> Tambah User
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-users mb-0">
                <thead>
                    <tr>
                        <th class="no-column">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>NPM</th>
                        <th>Dosen Wali</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    @php
                        $nomor = ($users->currentPage() - 1) * $users->perPage() + $index + 1;
                    @endphp
                    <tr>
                        <td class="no-column">{{ $nomor }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar-mini">
                                    <i class="bi bi-person-circle"></i>
                                </div>
                                <strong>{{ $user->name }}</strong>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge-role 
                                @if($user->role->role == 'admin') badge-admin
                                @elseif($user->role->role == 'reviewer') badge-reviewer
                                @else badge-mahasiswa
                                @endif">
                                <i class="bi 
                                    @if($user->role->role == 'admin') bi-shield-shaded
                                    @elseif($user->role->role == 'reviewer')
                                    @else bi-mortarboard
                                    @endif me-1"></i>
                                {{ ucfirst($user->role->role) }}
                            </span>
                        </td>
                        <td>
                            @if($user->role->role == 'mahasiswa')
                                <span class="fw-bold text-primary">{{ $user->npm ?? '-' }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($user->role->role == 'mahasiswa')
                                {{ $user->mahasiswa->dosen_wali ?? '-' }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-info btn-action" onclick="viewUser({{ $user->id }})" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if($user->role->role == 'mahasiswa')
                                <button class="btn btn-sm btn-warning btn-action" onclick="resetPassword({{ $user->id }})" title="Reset Password">
                                    <i class="bi bi-key"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada data user</p>
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
                <strong>{{ $users->firstItem() ?? 0 }}</strong> 
                sampai 
                <strong>{{ $users->lastItem() ?? 0 }}</strong> 
                dari 
                <strong>{{ $users->total() }}</strong> 
                data
                @if(request('search'))
                    <span class="text-muted"> - Hasil pencarian</span>
                @endif
            </div>
            
            <div class="d-flex align-items-center gap-3">
                {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
                
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
                <h5 class="modal-title">
                    <i class="bi bi-person-badge me-2"></i>
                    Detail User
                </h5>
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

<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

@endsection

@push('scripts')
<script>
    // Per Page Selector
    document.getElementById('perPageSelect')?.addEventListener('change', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', this.value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    });
    
    // View User Detail
    function viewUser(id) {
        const modal = new bootstrap.Modal(document.getElementById('userModal'));
        const modalBody = document.getElementById('userModalBody');
        
        modal.show();
        
        // Simulate loading user data
        modalBody.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        
        // Fetch user data via AJAX
        fetch(`/admin/users/${id}/detail`)
            .then(response => response.json())
            .then(data => {
                modalBody.innerHTML = `
                    <table class="table table-borderless">
                        <tr>
                            <td width="35%"><strong>Nama Lengkap</strong></td>
                            <td>: ${data.name}</td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong></td>
                            <td>: ${data.email}</td>
                        </tr>
                        <tr>
                            <td><strong>Username</strong></td>
                            <td>: ${data.username || '-'}</td>
                        </tr>
                        <tr>
                            <td><strong>Role</strong></td>
                            <td>: ${data.role}</td>
                        </tr>
                        ${data.npm ? `
                        <tr>
                            <td><strong>NPM</strong></td>
                            <td>: ${data.npm}</td>
                        </tr>
                        <tr>
                            <td><strong>Dosen Wali</strong></td>
                            <td>: ${data.dosen_wali || '-'}</td>
                        </tr>
                        <tr>
                            <td><strong>IPK</strong></td>
                            <td>: ${data.ipk || '-'}</td>
                        </tr>
                        ` : ''}
                    </table>
                `;
            })
            .catch(error => {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        Gagal memuat data user
                    </div>
                `;
            });
    }
    
    // Reset Password with SweetAlert
    function resetPassword(userId) {
        Swal.fire({
            title: 'Reset Password?',
            html: 'Password akan direset menjadi <strong>NPM mahasiswa</strong>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/users/${userId}/reset-password`, {
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
                .catch(error => {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat mereset password',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    }
</script>
@endpush