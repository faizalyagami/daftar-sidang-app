<nav class="navbar navbar-custom">
    <div class="container-fluid">
        <!-- Left side - Page Title -->
        <div class="navbar-left">
            <button class="sidebar-toggle-mobile me-2" id="sidebarToggleMobile">
                <i class="bi bi-list"></i>
            </button>
            <div class="page-title">
                <h4 class="mb-0">@yield('title', 'Dashboard')</h4>
                <span class="text-muted small">Sistem Pendaftaran Sidang Skripsi & Metodologi</span>
            </div>
        </div>

        <!-- Right side - User Menu -->
        <div class="navbar-right">
            <!-- Notification Bell -->
            <div class="dropdown me-3">
                <button class="btn btn-icon dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-bell"></i>
                    @php
                        $unreadCount = App\Helpers\NotificationHelper::getUnreadCount(Auth::id());
                    @endphp
                    @if ($unreadCount > 0)
                        <span class="notification-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end notification-dropdown">
                    <li class="dropdown-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Notifikasi</span>
                            @if ($unreadCount > 0)
                                <a href="#" class="small" id="markAllRead">Tandai semua</a>
                            @endif
                        </div>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    @php
                        $notifications = App\Helpers\NotificationHelper::getLatest(Auth::id(), 10);
                    @endphp

                    @forelse($notifications as $notif)
                        <li>
                            <a class="dropdown-item notification-item {{ !$notif->is_read ? 'bg-light' : '' }}"
                                href="{{ $notif->link ?: '#' }}" data-id="{{ $notif->id }}">
                                <div
                                    class="notification-icon bg-{{ $notif->type == 'pendaftaran'
                                        ? 'primary'
                                        : ($notif->type == 'review'
                                            ? 'success'
                                            : ($notif->type == 'jadwal'
                                                ? 'info'
                                                : 'warning')) }}">
                                    <i
                                        class="bi 
                        {{ $notif->type == 'pendaftaran'
                            ? 'bi-file-earmark-text'
                            : ($notif->type == 'review'
                                ? 'bi-check-circle'
                                : ($notif->type == 'jadwal'
                                    ? 'bi-calendar'
                                    : 'bi-info-circle')) }}"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">{{ $notif->title }}</div>
                                    <div class="notification-text">{{ $notif->message }}</div>
                                    <div class="notification-time">{{ $notif->created_at->diffForHumans() }}</div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li>
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-4"></i>
                                <p class="mb-0 mt-2">Tidak ada notifikasi</p>
                            </div>
                        </li>
                    @endforelse

                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li class="dropdown-footer">
                        <a href="{{ route('notifications.index') }}" class="text-center d-block">Lihat semua
                            notifikasi</a>
                    </li>
                </ul>
            </div>

            <!-- User Dropdown -->
            <div class="dropdown">
                <button class="btn btn-user dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <div class="user-avatar-sm">
                        @php
                            $userRole = Auth::user()->role ? Auth::user()->role->role : null;
                            $avatarIcon = match ($userRole) {
                                'admin' => 'bi-shield-shaded',
                                'reviewer' => 'bi-star',
                                default => 'bi-mortarboard',
                            };
                        @endphp
                        <i class="bi {{ $avatarIcon }}"></i>
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role">
                            @if ($userRole == 'admin')
                                Administrator
                            @elseif($userRole == 'reviewer')
                                Reviewer
                            @elseif ($userRole == 'dosen')
                                Dosen
                            @else
                                Mahasiswa
                            @endif
                        </div>
                    </div>
                    <i class="bi bi-chevron-down user-chevron"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end user-dropdown">
                    <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                            <i class="bi bi-person-circle"></i>
                            <span>Profil Saya</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('change-password') }}">
                            <i class="bi bi-shield-lock"></i>
                            <span>Ganti Password</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<style>
    /* Navbar Styles */
    .navbar-custom {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        padding: 12px 24px;
        position: sticky;
        top: 0;
        z-index: 999;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .navbar-custom .container-fluid {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Left Side */
    .navbar-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .sidebar-toggle-mobile {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .sidebar-toggle-mobile:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .page-title h4 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
    }

    .page-title span {
        font-size: 0.75rem;
        color: #718096;
    }

    /* Right Side */
    .navbar-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    /* Notification Button */
    .btn-icon {
        background: transparent;
        border: none;
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        color: #4a5568;
    }

    .btn-icon:hover {
        background: #f0f4ff;
        color: #667eea;
    }

    .notification-badge {
        position: absolute;
        top: -2px;
        right: -2px;
        background: #ef4444;
        color: white;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 20px;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Notification Dropdown */
    .notification-dropdown {
        width: 360px;
        padding: 0;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        border: none;
        margin-top: 12px;
    }

    .notification-dropdown .dropdown-header {
        padding: 16px 20px;
        background: #f8f9fa;
        border-radius: 16px 16px 0 0;
        font-weight: 600;
    }

    .notification-dropdown .dropdown-footer {
        padding: 12px 20px;
        background: #f8f9fa;
        border-radius: 0 0 16px 16px;
    }

    .notification-dropdown .dropdown-footer a {
        color: #667eea;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
    }

    .notification-item {
        display: flex;
        gap: 12px;
        padding: 12px 20px;
        transition: all 0.3s;
    }

    .notification-item:hover {
        background: #f8f9fa;
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .notification-icon i {
        font-size: 18px;
        color: white;
    }

    .notification-content {
        flex: 1;
    }

    .notification-title {
        font-weight: 600;
        font-size: 14px;
        color: #2d3748;
        margin-bottom: 4px;
    }

    .notification-text {
        font-size: 12px;
        color: #718096;
        margin-bottom: 4px;
    }

    .notification-time {
        font-size: 11px;
        color: #a0aec0;
    }

    /* User Button */
    .btn-user {
        background: transparent;
        border: none;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 6px 12px;
        border-radius: 40px;
        transition: all 0.3s;
    }

    .btn-user:hover {
        background: #f0f4ff;
    }

    .user-avatar-sm {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .user-avatar-sm i {
        font-size: 20px;
    }

    .user-info {
        text-align: left;
    }

    .user-name {
        font-weight: 600;
        font-size: 14px;
        color: #2d3748;
        line-height: 1.3;
    }

    .user-role {
        font-size: 11px;
        color: #718096;
    }

    .user-chevron {
        font-size: 12px;
        color: #a0aec0;
        transition: transform 0.3s;
    }

    .btn-user[aria-expanded="true"] .user-chevron {
        transform: rotate(180deg);
    }

    /* User Dropdown */
    .user-dropdown {
        min-width: 240px;
        padding: 8px;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        border: none;
        margin-top: 12px;
    }

    .user-dropdown .dropdown-item {
        padding: 10px 16px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        transition: all 0.3s;
    }

    .user-dropdown .dropdown-item i {
        font-size: 18px;
        width: 20px;
    }

    .user-dropdown .dropdown-item:hover {
        background: #f0f4ff;
    }

    .user-dropdown .dropdown-item.text-danger:hover {
        background: #fee2e2;
        color: #dc2626;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .navbar-custom {
            padding: 10px 16px;
        }

        .page-title span {
            display: none;
        }

        .page-title h4 {
            font-size: 1rem;
        }

        .user-info {
            display: none;
        }

        .btn-user {
            padding: 6px;
        }

        .notification-dropdown {
            width: 320px;
            position: fixed;
            right: 16px;
            left: auto;
        }
    }

    @media (max-width: 480px) {
        .notification-dropdown {
            width: calc(100% - 32px);
            right: 16px;
        }
    }
</style>

<!-- Modal Profile (if not exists) -->
<div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Profil Saya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="user-avatar-lg mb-3">
                        <i class="bi {{ $avatarIcon }} fs-1 text-primary"></i>
                    </div>
                    <h5>{{ Auth::user()->name }}</h5>
                    <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
                </div>
                <hr>
                <table class="table table-borderless">
                    <tr>
                        <td width="35%"><strong>Username</strong></td>
                        <td>: {{ Auth::user()->username ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Role</strong></td>
                        <td>: {{ ucfirst($userRole) }}</td>
                    </tr>
                    @if ($userRole == 'mahasiswa' && Auth::user()->mahasiswa)
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
