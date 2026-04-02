<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Sidebar toggle for mobile
    const sidebar = document.getElementById('sidebar');
    const sidebarToggleMobile = document.getElementById('sidebarToggleMobile');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');

    function toggleSidebarMobile() {
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
        if (sidebar.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (sidebarToggleMobile) {
        sidebarToggleMobile.addEventListener('click', toggleSidebarMobile);
    }

    if (sidebarClose) {
        sidebarClose.addEventListener('click', closeSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && sidebar.classList.contains('active')) {
            closeSidebar();
        }
    });

    sidebar?.addEventListener('touchmove', function(e) {
        if (window.innerWidth <= 768) {
            e.stopPropagation();
        }
    });

    // Inisialisasi Select2 untuk semua elemen dengan class .select2-dosen
    $(document).ready(function() {
        $('.select2-dosen').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Ketik nama dosen...',
            allowClear: true,
            language: {
                noResults: function() {
                    return 'Dosen tidak ditemukan. Hubungi admin.';
                },
                searching: function() {
                    return 'Mencari...';
                }
            }
        });
    });
</script>

@stack('scripts')