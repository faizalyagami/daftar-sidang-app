<nav class="navbar navbar-custom">
    <div class="container-fluid">
        <div class="ms-auto d-flex align-items-center">
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-4"></i>
                    <span class="ms-2 d-none d-md-inline">{{ Auth::user()->name ?? 'User' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <span class="dropdown-item-text">
                            <strong>Nama:</strong> {{ Auth::user()->name ?? '-' }}
                        </span>
                    </li>
                    <li>
                        <span class="dropdown-item-text">
                            <strong>Email:</strong> {{ Auth::user()->email ?? '-' }}
                        </span>
                    </li>
                    @if(Auth::user() && Auth::user()->role)
                    <li>
                        <span class="dropdown-item-text">
                            <strong>Role:</strong> {{ ucfirst(Auth::user()->role->role) }}
                        </span>
                    </li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>