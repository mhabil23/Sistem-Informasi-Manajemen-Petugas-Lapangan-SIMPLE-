<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SIMPLE | Sistem Informasi Manajemen Petugas Lapangan</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}?v={{ file_exists(public_path('css/app-custom.css')) ? filemtime(public_path('css/app-custom.css')) : time() }}">

    @stack('styles')
</head>
<body>
    <div class="sp-wrapper {{ !auth()->user()->isAdmin() ? 'sp-wrapper-petugas' : '' }}">
        @if(auth()->user()->isAdmin())
            <!-- Sidebar Backdrop for Mobile -->
            <div class="sp-sidebar-backdrop" id="sidebarBackdrop"></div>

            <!-- Sidebar Navigation (Admin Only) -->
            <aside class="sp-sidebar" id="sidebar">
                <div class="sp-sidebar-brand">
                    <div class="d-flex align-items-center">
                        <div class="sp-brand-logo">
                            <img src="{{ asset('images/logo.png') }}?v={{ file_exists(public_path('images/logo.png')) ? filemtime(public_path('images/logo.png')) : time() }}" alt="Logo BPS SIMPLE">
                        </div>
                        <div class="sp-brand-text">
                            <span class="sp-brand-title">SIMPLE</span>
                            <span class="sp-brand-sub">Portal Administrator</span>
                        </div>
                    </div>
                    <button class="sp-sidebar-close" id="sidebarCloseBtn" type="button" aria-label="Tutup menu">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="sp-sidebar-nav">
                    <!-- ADMIN MENU -->
                    <div class="sp-nav-heading">Utama</div>
                    <a href="{{ route('admin.dashboard') }}" class="sp-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high sp-nav-icon"></i>
                        <span>Dashboard</span>
                    </a>

                    <div class="sp-nav-heading">Administrasi & Penugasan</div>
                    <a href="{{ route('admin.users.index') }}" class="sp-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users-gear sp-nav-icon"></i>
                        <span>Manajemen Pengguna</span>
                    </a>
                    <a href="{{ route('admin.assignments.index') }}" class="sp-nav-link {{ request()->routeIs('admin.assignments.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-list-check sp-nav-icon"></i>
                        <span>Kegiatan & Tugas</span>
                    </a>
                    <a href="{{ route('admin.schedules.index') }}" class="sp-nav-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                        <i class="fa-regular fa-calendar-days sp-nav-icon"></i>
                        <span>Jadwal Kegiatan</span>
                    </a>

                    <div class="sp-nav-heading">Pendataan & Dokumen BAST</div>
                    <a href="{{ route('admin.respondents.index') }}" class="sp-nav-link {{ request()->routeIs('admin.respondents.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-id-card-clip sp-nav-icon"></i>
                        <span>Data Responden</span>
                    </a>
                    <a href="{{ route('admin.basts.respondents.index') }}" class="sp-nav-link {{ request()->routeIs('admin.basts.respondents.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-contract sp-nav-icon"></i>
                        <span>BAST Responden</span>
                    </a>
                    <a href="{{ route('admin.basts.officers.index') }}" class="sp-nav-link {{ request()->routeIs('admin.basts.officers.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-shield sp-nav-icon"></i>
                        <span>BAST Petugas</span>
                    </a>

                    <div class="sp-nav-heading">Laporan & Pelatihan</div>
                    <a href="{{ route('admin.reports.index') }}" class="sp-nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-clipboard-check sp-nav-icon"></i>
                        <span>Laporan Petugas</span>
                        @php
                            $pendingCount = \App\Models\OfficerReport::whereIn('status', ['submitted', 'reviewing'])->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="badge bg-warning text-dark sp-nav-badge">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.trainings.index') }}" class="sp-nav-link {{ request()->routeIs('admin.trainings.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-graduation-cap sp-nav-icon"></i>
                        <span>Administrasi Pelatihan</span>
                    </a>
                    <a href="{{ route('admin.recapitulation.index') }}" class="sp-nav-link {{ request()->routeIs('admin.recapitulation.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-pie sp-nav-icon"></i>
                        <span>Rekapitulasi & Export</span>
                    </a>

                    <div class="sp-nav-heading">Sistem & Audit</div>
                    <a href="{{ route('admin.spreadsheet.index') }}" class="sp-nav-link {{ request()->routeIs('admin.spreadsheet.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-table-cells sp-nav-icon text-success"></i>
                        <span>Database Spreadsheet</span>
                    </a>
                    <a href="{{ route('admin.templates.index') }}" class="sp-nav-link {{ request()->routeIs('admin.templates.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-lines sp-nav-icon"></i>
                        <span>Template Dokumen</span>
                    </a>
                    <a href="{{ route('admin.logs.index') }}" class="sp-nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left sp-nav-icon"></i>
                        <span>Audit Log Aktivitas</span>
                    </a>
                </div>

                <div class="sp-sidebar-footer">
                    <div class="d-flex align-items-center gap-2 overflow-hidden flex-grow-1 me-2">
                        <div class="sp-user-avatar flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <div class="text-white small fw-bold text-truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-light border-0 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#logoutModal" title="Keluar">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </div>
            </aside>
        @endif

        <!-- Main Wrapper -->
        <div class="sp-main {{ !auth()->user()->isAdmin() ? 'sp-main-petugas' : '' }}">
            <!-- Top Navbar -->
            <header class="sp-navbar">
                @if(auth()->user()->isAdmin())
                    <div class="d-flex align-items-center gap-3">
                        <button class="sp-nav-toggle" id="sidebarToggle" type="button" aria-label="Toggle navigation">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <div class="sp-breadcrumb-container overflow-hidden">
                            <h1 class="sp-page-title text-truncate">@yield('page_title', 'Halaman')</h1>
                            <nav aria-label="breadcrumb" class="d-none d-sm-block">
                                <ol class="breadcrumb sp-breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">SIMPLE</a></li>
                                    @yield('breadcrumb')
                                </ol>
                            </nav>
                        </div>
                    </div>
                @else
                    <!-- Petugas Navbar: Full-Width Clean Header (Zero Sidebar) -->
                    <a href="{{ route('petugas.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none" title="Beranda Petugas">
                        <div class="sp-brand-logo" style="width: 38px; height: 38px;">
                            <img src="{{ asset('images/logo.png') }}?v={{ file_exists(public_path('images/logo.png')) ? filemtime(public_path('images/logo.png')) : time() }}" alt="Logo BPS SIMPLE">
                        </div>
                        <div class="lh-1">
                            <div class="fw-bold text-dark fs-6" style="letter-spacing: 0.5px;">SIMPLE</div>
                            <small class="text-primary fw-semibold" style="font-size: 0.68rem; text-transform: uppercase;">Portal Petugas</small>
                        </div>
                    </a>
                @endif

                <div class="d-flex align-items-center gap-3">
                    <!-- Status / Role Badge -->
                    <span class="badge {{ auth()->user()->isAdmin() ? 'badge-role-admin' : 'badge-role-petugas' }} d-none d-md-inline-block">
                        <i class="fa-solid {{ auth()->user()->isAdmin() ? 'fa-shield-halved' : 'fa-user-check' }} me-1"></i>
                        {{ auth()->user()->isAdmin() ? 'Administrator' : 'Petugas Lapangan' }}
                    </span>

                    <!-- User Menu Dropdown -->
                    <div class="dropdown">
                        <div class="sp-user-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="sp-user-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="d-none d-lg-block text-start">
                                <span class="fw-semibold small d-block text-dark">{{ auth()->user()->name }}</span>
                                <span class="text-muted" style="font-size: 0.72rem;">{{ auth()->user()->email }}</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-muted small ms-1"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold small">{{ auth()->user()->name }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">@<span>{{ auth()->user()->username }}</span></div>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 small" href="{{ route('profile.edit') }}">
                                    <i class="fa-regular fa-user text-muted me-2"></i> Profil & Ganti Password
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <button type="button" class="dropdown-item py-2 small text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Keluar
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="sp-content flex-grow-1">
                <!-- Notifications Banner -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-2.5 py-2 px-3 shadow-sm" role="alert" style="font-size: 0.8125rem;">
                        <i class="fa-solid fa-circle-check fs-6 me-2 text-success"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-2.5 py-2 px-3 shadow-sm" role="alert" style="font-size: 0.8125rem;">
                        <i class="fa-solid fa-circle-exclamation fs-6 me-2 text-danger"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center mb-2.5 py-2 px-3 shadow-sm" role="alert" style="font-size: 0.8125rem;">
                        <i class="fa-solid fa-triangle-exclamation fs-6 me-2 text-warning"></i>
                        <div>{{ session('warning') }}</div>
                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-2.5 py-2 px-3 shadow-sm" role="alert" style="font-size: 0.8125rem;">
                        <div class="fw-bold mb-1"><i class="fa-solid fa-circle-exclamation me-1"></i> Terdapat kesalahan pengisian data:</div>
                        <ul class="mb-0 ps-3 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Bottom Application Footer -->
            <footer class="sp-footer py-2.5 px-4 mt-auto border-top bg-white text-muted small d-flex flex-wrap justify-content-between align-items-center" style="font-size: 0.78rem;">
                <div>
                    <strong>SIMPLE</strong> &copy; {{ date('Y') }} &mdash; Sistem Informasi Manajemen Pelayanan & Evaluasi Lapangan
                </div>
                <div class="d-none d-md-block text-secondary">
                    BPS Kabupaten &bull; Portal {{ auth()->user()->isAdmin() ? 'Administrator' : 'Petugas Lapangan' }}
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal Konfirmasi Logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center p-4">
                    <div class="mb-3 text-warning">
                        <i class="fa-solid fa-arrow-right-from-bracket fa-3x"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Konfirmasi Keluar</h5>
                    <p class="text-muted small mb-4">Apakah Anda yakin ingin keluar dari Sistem Informasi Manajemen Petugas Lapangan (SIMPLE)?</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger px-4">Ya, Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar Toggle for Mobile / HP
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function openSidebar() {
            if (sidebar && sidebarBackdrop) {
                sidebar.classList.add('show');
                sidebarBackdrop.classList.add('show');
                document.body.style.overflow = 'hidden'; // prevent background scrolling while drawer is open
            }
        }

        function closeSidebar() {
            if (sidebar && sidebarBackdrop) {
                sidebar.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', openSidebar);
        }

        if (sidebarCloseBtn) {
            sidebarCloseBtn.addEventListener('click', closeSidebar);
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', closeSidebar);
        }

        // Close when pressing Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && sidebar && sidebar.classList.contains('show')) {
                closeSidebar();
            }
        });

        // Close drawer when clicking menu items on mobile
        document.querySelectorAll('.sp-sidebar-nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    closeSidebar();
                }
            });
        });

        // Touch Swipe Left to close sidebar on mobile
        let touchStartX = 0;
        let touchEndX = 0;
        if (sidebar) {
            sidebar.addEventListener('touchstart', e => {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            sidebar.addEventListener('touchend', e => {
                touchEndX = e.changedTouches[0].screenX;
                if (touchStartX - touchEndX > 50) { // Swiped left by at least 50px
                    closeSidebar();
                }
            }, { passive: true });
        }
    </script>
    @stack('scripts')
</body>
</html>

