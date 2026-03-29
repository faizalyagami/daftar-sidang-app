@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="card fade-in">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar User</h5>
        <a href="{{ route('admin.users.create') }}" class="btn btn-light btn-sm">
            <i class="bi bi-plus-circle"></i> Tambah User
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>NPM</th>
                        <th>Dosen Wali</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-{{ $user->role->role == 'admin' ? 'danger' : ($user->role->role == 'reviewer' ? 'warning' : 'info') }}">
                                {{ ucfirst($user->role->role) }}
                            </span>
                        </td>
                        <td>{{ $user->mahasiswa->npm ?? '-' }}</td>
                        <td>{{ $user->mahasiswa->dosen_wali ?? '-' }}</td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="viewUser({{ $user->id }})">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            }
        });
    });
    
    function viewUser(id) {
        // Implement view user detail modal
        alert('View user ' + id);
    }
</script>
@endpush