<!-- Toggle Button untuk mobile -->
<button class="sidebar-toggle-mobile" id="sidebarToggleMobile">
    <i class="bi bi-list fs-4"></i>
</button>

<!-- Overlay untuk mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div class="logo-text">
                <h4>SIPASKA</h4>
                <p>Sistem Pendaftaran Sidang Skripsi</p>
            </div>
        </div>
        <button class="sidebar-close" id="sidebarClose">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    
    <div class="sidebar-user">
        <div class="user-avatar">
            <i class="bi bi-person-circle"></i>
        </div>
        <div class="user-info">
            <h6>{{ Auth::user()->name ?? 'User' }}</h6>
            <span class="user-role">
                @php
                    $userRole = Auth::user()->role ? Auth::user()->role->role : null;
                @endphp
                @if($userRole == 'admin')
                    <i class="bi bi-shield-shaded"></i> Administrator
                @elseif($userRole == 'reviewer')
                    <i class="bi bi-star"></i> Reviewer
                @elseif($userRole == 'mahasiswa')
                    <i class="bi bi-mortarboard"></i> Mahasiswa
                @endif
            </span>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <div class="menu-title">MAIN MENU</div>
        <ul class="nav flex-column">
            @auth
                @php
                    $userRole = Auth::user()->role ? Auth::user()->role->role : null;
                @endphp
                
                @if($userRole == 'admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                           href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                            @if(request()->routeIs('admin.dashboard'))
                                <span class="nav-indicator"></span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" 
                           href="{{ route('admin.users') }}">
                            <i class="bi bi-people"></i>
                            <span>Kelola User</span>
                            @if(request()->routeIs('admin.users*'))
                                <span class="nav-indicator"></span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.mahasiswa*') ? 'active' : '' }}" 
                           href="{{ route('admin.mahasiswa.index') }}">
                            <i class="bi bi-mortarboard"></i>
                            <span>Data Mahasiswa</span>
                            @if(request()->routeIs('admin.mahasiswa*'))
                                <span class="nav-indicator"></span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.academic-periods*') ? 'active' : '' }}" 
                           href="{{ route('admin.academic-periods.index') }}">
                            <i class="bi bi-calendar-week"></i>
                            <span>Periode Akademik</span>
                            @if(request()->routeIs('admin.academic-periods*'))
                                <span class="nav-indicator"></span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.pendaftaran*') ? 'active' : '' }}" 
                           href="{{ route('admin.pendaftaran') }}">
                            <i class="bi bi-file-text"></i>
                            <span>Semua Pendaftaran</span>
                            @if(request()->routeIs('admin.pendaftaran*'))
                                <span class="nav-indicator"></span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reviewers.stats') ? 'active' : '' }}" 
                        href="{{ route('admin.reviewers.stats') }}">
                            <i class="bi bi-bar-chart"></i>
                            <span>Statistik Reviewer</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.jadwal*') ? 'active' : '' }}" 
                        href="{{ route('admin.jadwal.index') }}">
                            <i class="bi bi-calendar-event"></i>
                            <span>Jadwal Sidang & Ujian</span>
                        </a>
                    </li>
                @elseif($userRole == 'reviewer')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reviewer.dashboard') ? 'active' : '' }}" 
                        href="{{ route('reviewer.dashboard') }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard Review</span>
                            @if(request()->routeIs('reviewer.dashboard'))
                                <span class="nav-indicator"></span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reviewer.history') ? 'active' : '' }}" 
                        href="{{ route('reviewer.reviewer.history') }}">
                            <i class="bi bi-clock-history"></i>
                            <span>Riwayat Review</span>
                            @if(request()->routeIs('reviewer.history'))
                                <span class="nav-indicator"></span>
                            @endif
                        </a>
                    </li>
                @elseif($userRole == 'mahasiswa')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}" 
                           href="{{ route('mahasiswa.dashboard') }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                            @if(request()->routeIs('mahasiswa.dashboard'))
                                <span class="nav-indicator"></span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mahasiswa.daftar-skripsi*') ? 'active' : '' }}" 
                           href="{{ route('mahasiswa.daftar-skripsi') }}">
                            <i class="bi bi-file-earmark-text"></i>
                            <span>Sidang Skripsi</span>
                            @if(request()->routeIs('mahasiswa.daftar-skripsi*'))
                                <span class="nav-indicator"></span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mahasiswa.daftar-metodologi*') ? 'active' : '' }}" 
                           href="{{ route('mahasiswa.daftar-metodologi') }}">
                            <i class="bi bi-book"></i>
                            <span>Ujian Metodologi</span>
                            @if(request()->routeIs('mahasiswa.daftar-metodologi*'))
                                <span class="nav-indicator"></span>
                            @endif
                        </a>
                    </li>
                @endif
            @endauth
        </ul>
    </div>
    
    <div class="sidebar-footer">
        <div class="menu-title">ACCOUNT</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                    <i class="bi bi-person-circle"></i>
                    <span>Profil Saya</span>
                </a>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" id="logout-form-sidebar">
                    @csrf
                    <button type="submit" class="nav-link logout-btn w-100 text-start border-0 bg-transparent">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>

<!-- Modal Profil -->
<div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Profil Saya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-person-circle fs-1 text-primary"></i>
                </div>
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Nama</strong></td>
                        <td>: {{ Auth::user()->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td>: {{ Auth::user()->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Username</strong></td>
                        <td>: {{ Auth::user()->username ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Role</strong></td>
                        <td>: {{ ucfirst($userRole) }}</td>
                    </tr>
                    @if($userRole == 'mahasiswa' && Auth::user()->mahasiswa)
                    <tr>
                        <td><strong>NPM</strong></td>
                        <td>: {{ Auth::user()->mahasiswa->npm ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Dosen Wali</strong></td>
                        <td>: {{ Auth::user()->mahasiswa->dosen_wali ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>IPK</strong></td>
                        <td>: {{ Auth::user()->mahasiswa->ipk ?? '-' }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>