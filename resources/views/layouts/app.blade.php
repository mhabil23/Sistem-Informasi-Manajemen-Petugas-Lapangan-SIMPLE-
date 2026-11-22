<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SIMPEL | Sistem Informasi Manajemen Pelayanan & Evaluasi Lapangan</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Font Awesome 6 (Fallback & Extended Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}?v={{ file_exists(public_path('css/app-custom.css')) ? filemtime(public_path('css/app-custom.css')) : time() }}">

    @stack('styles')
</head>
<body>
    <div class="sp-wrapper {{ auth()->user()->isPetugas() ? 'sp-wrapper-petugas' : '' }}">
        @if(auth()->user()->canAccessMonitoring())
            <!-- Sidebar Backdrop for Mobile -->
            <div class="sp-sidebar-backdrop" id="sidebarBackdrop"></div>

            <!-- Sidebar Navigation (Admin & Pegawai / PML) -->
            <aside class="sp-sidebar" id="sidebar">
                <div class="sp-sidebar-brand">
                    <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center text-decoration-none">
                        <div class="sp-brand-logo">
                            <img src="{{ asset('images/logo.png') }}?v={{ file_exists(public_path('images/logo.png')) ? filemtime(public_path('images/logo.png')) : time() }}" alt="Logo BPS SIMPEL">
                        </div>
                        <div class="sp-brand-text">
                            <span class="sp-brand-title">SIMPEL</span>
                            <span class="sp-brand-sub">{{ auth()->user()->isAdmin() ? 'Portal Administrator' : 'Portal Pegawai / PML' }}</span>
                        </div>
                    </a>
                    <button class="sp-sidebar-close" id="sidebarCloseBtn" type="button" aria-label="Tutup menu">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="sp-sidebar-nav">
                    @if(auth()->user()->isAdmin())
                        <!-- ADMIN MENU -->
                        <div class="sp-nav-heading">Utama</div>
                        <a href="{{ route('admin.dashboard') }}" class="sp-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2 sp-nav-icon"></i>
                            <span>Dashboard</span>
                        </a>

                        <div class="sp-nav-heading">Manajemen &amp; Penugasan</div>
                        <a href="{{ route('admin.users.index') }}" class="sp-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill sp-nav-icon"></i>
                            <span>Manajemen Pengguna</span>
                        </a>
                        <a href="{{ route('admin.assignments.index') }}" class="sp-nav-link {{ request()->routeIs('admin.assignments.*') ? 'active' : '' }}">
                            <i class="bi bi-check2-square sp-nav-icon"></i>
                            <span>Kegiatan &amp; Tugas</span>
                        </a>
                        <a href="{{ route('admin.schedules.index') }}" class="sp-nav-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar3 sp-nav-icon"></i>
                            <span>Jadwal Kegiatan</span>
                        </a>

                        <div class="sp-nav-heading">Pendataan Responden</div>
                        <a href="{{ route('admin.respondents.index') }}" class="sp-nav-link {{ request()->routeIs('admin.respondents.*') ? 'active' : '' }}">
                            <i class="bi bi-person-vcard sp-nav-icon"></i>
                            <span>Data Responden</span>
                        </a>
                        <a href="{{ route('admin.basts.respondents.index') }}" class="sp-nav-link {{ request()->routeIs('admin.basts.respondents.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-check sp-nav-icon"></i>
                            <span>Tanda Terima Responden</span>
                        </a>

                        <div class="sp-nav-heading">Tanda Terima &amp; Presensi</div>
                        <a href="{{ route('admin.basts.officers.index') }}" class="sp-nav-link {{ request()->routeIs('admin.basts.officers.*') ? 'active' : '' }}">
                            <i class="bi bi-shield-check sp-nav-icon"></i>
                            <span>Tanda Terima Petugas</span>
                        </a>
                        <a href="{{ route('admin.trainings.index') }}" class="sp-nav-link {{ request()->routeIs('admin.trainings.*') ? 'active' : '' }}">
                            <i class="bi bi-person-check sp-nav-icon"></i>
                            <span>Daftar Hadir</span>
                        </a>

                        <div class="sp-nav-heading">Laporan &amp; Rekapitulasi</div>
                        <a href="{{ route('admin.reports.index') }}" class="sp-nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard2-data sp-nav-icon"></i>
                            <span>Laporan Petugas</span>
                            @php
                                $sidebarReportBadge = max((int) ($adminPendingReportsCount ?? 0), (int) ($adminUnreadNotifCount ?? 0));
                            @endphp
                            @if($sidebarReportBadge > 0)
                                <span id="sidebarReportBadge" class="badge bg-warning text-dark sp-nav-badge">{{ $sidebarReportBadge }}</span>
                            @else
                                <span id="sidebarReportBadge" class="badge bg-warning text-dark sp-nav-badge d-none">0</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.recapitulation.index') }}" class="sp-nav-link {{ request()->routeIs('admin.recapitulation.*') ? 'active' : '' }}">
                            <i class="bi bi-pie-chart sp-nav-icon"></i>
                            <span>Rekapitulasi &amp; Export</span>
                        </a>

                        <div class="sp-nav-heading">Sistem &amp; Dokumen</div>
                        <a href="{{ route('admin.spreadsheet.index') }}" class="sp-nav-link {{ request()->routeIs('admin.spreadsheet.*') ? 'active' : '' }}">
                            <i class="bi bi-table sp-nav-icon text-success"></i>
                            <span>Database Spreadsheet</span>
                        </a>
                        <a href="{{ route('admin.templates.index') }}" class="sp-nav-link {{ request()->routeIs('admin.templates.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-richtext sp-nav-icon"></i>
                            <span>Template Dokumen</span>
                        </a>
                    @elseif(auth()->user()->isPegawai())
                        <!-- PEGAWAI / PML MENU (Serasi & Selaras dengan Admin) -->
                        <div class="sp-nav-heading">Utama</div>
                        <a href="{{ route('admin.dashboard') }}" class="sp-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2 sp-nav-icon"></i>
                            <span>Dashboard Monitoring</span>
                        </a>

                        <div class="sp-nav-heading">Manajemen &amp; Penugasan</div>
                        <a href="{{ route('admin.assignments.index') }}" class="sp-nav-link {{ request()->routeIs('admin.assignments.*') ? 'active' : '' }}">
                            <i class="bi bi-check2-square sp-nav-icon"></i>
                            <span>Kegiatan &amp; Tugas</span>
                        </a>
                        <a href="{{ route('admin.schedules.index') }}" class="sp-nav-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar3 sp-nav-icon"></i>
                            <span>Jadwal Kegiatan</span>
                        </a>

                        <div class="sp-nav-heading">Pendataan Responden</div>
                        <a href="{{ route('admin.respondents.index') }}" class="sp-nav-link {{ request()->routeIs('admin.respondents.*') ? 'active' : '' }}">
                            <i class="bi bi-person-vcard sp-nav-icon"></i>
                            <span>Data Responden</span>
                        </a>
                        <a href="{{ route('admin.basts.respondents.index') }}" class="sp-nav-link {{ request()->routeIs('admin.basts.respondents.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-check sp-nav-icon"></i>
                            <span>Tanda Terima Responden</span>
                        </a>

                        <div class="sp-nav-heading">Tanda Terima &amp; Presensi</div>
                        <a href="{{ route('admin.basts.officers.index') }}" class="sp-nav-link {{ request()->routeIs('admin.basts.officers.*') ? 'active' : '' }}">
                            <i class="bi bi-shield-check sp-nav-icon"></i>
                            <span>Tanda Terima Petugas</span>
                        </a>

                        <div class="sp-nav-heading">Laporan &amp; Rekapitulasi</div>
                        <a href="{{ route('admin.reports.index') }}" class="sp-nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard2-data sp-nav-icon"></i>
                            <span>Laporan Petugas</span>
                            @php
                                $sidebarReportBadge = max((int) ($adminPendingReportsCount ?? 0), (int) ($adminUnreadNotifCount ?? 0));
                            @endphp
                            @if($sidebarReportBadge > 0)
                                <span id="sidebarReportBadge" class="badge bg-warning text-dark sp-nav-badge">{{ $sidebarReportBadge }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.recapitulation.index') }}" class="sp-nav-link {{ request()->routeIs('admin.recapitulation.*') ? 'active' : '' }}">
                            <i class="bi bi-pie-chart sp-nav-icon"></i>
                            <span>Rekapitulasi &amp; Export</span>
                        </a>

                        <div class="sp-nav-heading">Administrasi Pribadi</div>
                        <a href="{{ route('petugas.reports.index') }}" class="sp-nav-link {{ request()->routeIs('petugas.reports.*') ? 'active' : '' }}">
                            <i class="bi bi-signpost-2 sp-nav-icon"></i>
                            <span>Perjadin Saya</span>
                        </a>
                        <a href="{{ route('petugas.basts.officers.index') }}" class="sp-nav-link {{ request()->routeIs('petugas.basts.officers.*') ? 'active' : '' }}">
                            <i class="bi bi-shield-check sp-nav-icon"></i>
                            <span>Tanda Terima Saya</span>
                        </a>
                    @else
                        <!-- PETUGAS LAPANGAN FALLBACK MENU -->
                        <div class="sp-nav-heading">Utama</div>
                        <a href="{{ route('petugas.dashboard') }}" class="sp-nav-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-house-door sp-nav-icon"></i>
                            <span>Beranda Saya</span>
                        </a>
                        <a href="{{ route('petugas.assignments.index') }}" class="sp-nav-link {{ request()->routeIs('petugas.assignments.*') ? 'active' : '' }}">
                            <i class="bi bi-check2-square sp-nav-icon"></i>
                            <span>Tugas Saya</span>
                        </a>
                        <a href="{{ route('petugas.schedules.index') }}" class="sp-nav-link {{ request()->routeIs('petugas.schedules.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar3 sp-nav-icon"></i>
                            <span>Jadwal Saya</span>
                        </a>

                        <div class="sp-nav-heading">Pendataan Responden</div>
                        <a href="{{ route('petugas.respondents.index') }}" class="sp-nav-link {{ request()->routeIs('petugas.respondents.*') ? 'active' : '' }}">
                            <i class="bi bi-person-vcard sp-nav-icon"></i>
                            <span>Responden Saya</span>
                        </a>
                        <a href="{{ route('petugas.basts.respondents.index') }}" class="sp-nav-link {{ request()->routeIs('petugas.basts.respondents.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-check sp-nav-icon"></i>
                            <span>Tanda Terima Responden</span>
                        </a>

                        <div class="sp-nav-heading">Administrasi Petugas</div>
                        <a href="{{ route('petugas.basts.officers.index') }}" class="sp-nav-link {{ request()->routeIs('petugas.basts.officers.*') ? 'active' : '' }}">
                            <i class="bi bi-shield-check sp-nav-icon"></i>
                            <span>Tanda Terima Petugas</span>
                        </a>
                        <a href="{{ route('petugas.reports.index') }}" class="sp-nav-link {{ request()->routeIs('petugas.reports.*') ? 'active' : '' }}">
                            <i class="bi bi-signpost-2 sp-nav-icon"></i>
                            <span>Perjadin Saya</span>
                        </a>
                    @endif
                </div>

                <div class="sp-sidebar-footer">
                    <div class="d-flex align-items-center gap-2 overflow-hidden flex-grow-1 me-2">
                        <div class="sp-user-avatar flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <div class="text-dark small fw-bold text-truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</div>
                            <div class="text-muted" style="font-size: 0.72rem; font-weight: 500;">{{ auth()->user()->roleLabel() }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm text-danger border-0 flex-shrink-0 rounded-circle" style="width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; background: #fee2e2; transition: all 0.2s;" data-bs-toggle="modal" data-bs-target="#logoutModal" title="Keluar">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </div>
            </aside>
        @endif

        <!-- Main Wrapper -->
        <div class="sp-main {{ auth()->user()->isPetugas() ? 'sp-main-petugas' : '' }}">
            <!-- Top Navbar -->
            <header class="sp-navbar">
                @if(auth()->user()->canAccessMonitoring())
                    <div class="d-flex align-items-center gap-3">
                        <button class="sp-nav-toggle" id="sidebarToggle" type="button" aria-label="Toggle navigation">
                            <i class="bi bi-list fs-5"></i>
                        </button>
                        <div class="sp-breadcrumb-container overflow-hidden">
                            <h1 class="sp-page-title text-truncate">@yield('page_title', 'Halaman')</h1>
                        </div>
                    </div>
                @else
                    <!-- Petugas Navbar: Full-Width Clean Header (Zero Sidebar) -->
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('petugas.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none me-2" title="Beranda Petugas">
                            <div class="sp-brand-logo" style="width: 38px; height: 38px;">
                                <img src="{{ asset('images/logo.png') }}?v={{ file_exists(public_path('images/logo.png')) ? filemtime(public_path('images/logo.png')) : time() }}" alt="Logo BPS SIMPEL">
                            </div>
                            <div class="lh-1">
                                <div class="fw-bold text-dark fs-6" style="letter-spacing: 0.5px;">SIMPEL</div>
                                <small class="text-primary fw-semibold" style="font-size: 0.68rem; text-transform: uppercase;">Portal Petugas</small>
                            </div>
                        </a>
                        @if(!request()->routeIs('petugas.dashboard'))
                            <div class="vr text-muted opacity-25 d-none d-sm-block" style="height: 20px;"></div>
                            <span class="fw-bold text-dark d-none d-sm-inline" style="font-size: 0.95rem;">@yield('page_title', 'Halaman')</span>
                        @endif
                    </div>
                @endif

                <div class="d-flex align-items-center gap-3">
                    @if(auth()->user()->isAdmin())
                        <!-- Notifikasi Laporan Perjadin Masuk (Admin) -->
                        <div class="dropdown">
                            <button type="button"
                                    id="adminNotifDropdownBtn"
                                    class="btn btn-light btn-sm position-relative border rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 36px; height: 36px;"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                    title="Informasi Laporan Perjadin Petugas">
                                <i class="bi bi-bell text-secondary"></i>
                                @php $unreadBadge = (int) ($adminUnreadNotifCount ?? 0); @endphp
                                <span id="adminNotifBadge"
                                      class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $unreadBadge > 0 ? '' : 'd-none' }}"
                                      style="font-size: 0.65rem;">
                                    {{ $unreadBadge }}
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-0" style="width: 350px; max-width: 92vw;">
                                <div class="px-3 py-2.5 bg-light border-bottom d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold small text-dark">Notifikasi Laporan Perjadin</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                             <span id="adminPendingSubtitle">{{ (int) ($adminPendingReportsCount ?? 0) }} menunggu ACC</span>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm text-decoration-none p-0 small" style="font-size: 0.72rem;">
                                            Tandai dibaca
                                        </button>
                                    </form>
                                </div>
                                <div id="adminNotifList" class="list-group list-group-flush" style="max-height: 320px; overflow-y: auto;">
                                    @if(isset($adminRecentNotifs) && $adminRecentNotifs->isNotEmpty())
                                        @foreach($adminRecentNotifs as $notif)
                                            <a href="{{ $notif->report_id ? route('admin.reports.index', ['inspect' => $notif->report_id, 'notif_id' => $notif->id]) : route('admin.reports.index') }}"
                                               class="list-group-item list-group-item-action py-2 px-3 {{ !$notif->is_read ? 'bg-primary-subtle bg-opacity-25' : '' }}">
                                                <div class="d-flex justify-content-between align-items-start gap-2">
                                                    <div class="fw-bold small text-dark text-truncate">
                                                        @if(!$notif->is_read)
                                                            <span class="badge bg-danger me-1" style="font-size: 0.6rem;">Baru</span>
                                                        @endif
                                                        {{ $notif->officer?->name ?? 'Petugas' }}
                                                    </div>
                                                    <small class="text-muted text-nowrap" style="font-size: 0.68rem;">{{ $notif->created_at?->diffForHumans() }}</small>
                                                </div>
                                                <div class="small text-primary fw-semibold text-truncate">{{ $notif->report?->activity_title ?? $notif->title }}</div>
                                                <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ $notif->message }}</div>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4 px-3 text-muted small">
                                            <i class="bi bi-bell-slash d-block mb-1 fs-5 opacity-50"></i>
                                            Belum ada pemberitahuan laporan perjadin baru.
                                        </div>
                                    @endif
                                </div>
                                <div class="px-3 py-2 bg-light border-top text-center">
                                    <a href="{{ route('admin.reports.index') }}" class="small fw-semibold text-decoration-none">
                                        Buka Halaman Laporan Petugas &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Status / Role Badge -->
                    @php
                        $currentActiveRole = auth()->user()->activeRole();
                        $navRoleBadgeClass = match($currentActiveRole) {
                            'admin' => 'badge-role-admin',
                            'pegawai' => 'badge-role-pegawai',
                            default => 'badge-role-petugas',
                        };
                        $navRoleIconClass = match($currentActiveRole) {
                            'admin' => auth()->user()->isMasterAdmin() ? 'bi-shield-shaded text-warning' : 'bi-shield-check',
                            'pegawai' => 'bi-person-badge',
                            default => 'bi-person-check',
                        };
                        $navRoleLabel = auth()->user()->isMasterAdmin() && $currentActiveRole === 'admin'
                            ? 'Master Admin'
                            : match($currentActiveRole) {
                                'admin' => 'Administrator',
                                'pegawai' => 'Pegawai / PML',
                                default => 'Petugas Lapangan',
                            };
                    @endphp
                    @if(auth()->user()->canSwitchRole())
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle d-none d-md-inline-block py-1.5 px-2.5 rounded-pill" title="Akun memiliki hak Dual Role (bisa beralih peran)">
                            <i class="bi bi-arrow-left-right me-1 text-primary"></i> Dual Role
                        </span>
                    @endif
                    <span class="badge {{ $navRoleBadgeClass }} d-none d-md-inline-block">
                        <i class="bi {{ $navRoleIconClass }} me-1"></i>
                        {{ $navRoleLabel }}
                    </span>

                    <!-- User Menu Dropdown (Dukungan Pemilihan Role seperti SSO BPS) -->
                    <div class="dropdown">
                        <div class="sp-user-dropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                            <div class="sp-user-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="d-none d-lg-block text-start">
                                <span class="fw-semibold small d-block text-dark">{{ auth()->user()->name }}</span>
                                <span class="text-muted" style="font-size: 0.72rem;">{{ auth()->user()->email }}</span>
                            </div>
                            <i class="bi bi-chevron-down text-muted small ms-1"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-0" style="min-width: 270px; border-radius: 14px; overflow: hidden;">
                            <!-- Header Profil Info (Seperti pada SSO BPS) -->
                            <li class="px-3 py-2.5 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #0284c7 100%);">
                                <div class="fw-bold small text-white lh-sm">{{ auth()->user()->name }}</div>
                                <div class="text-white-50 mt-0.5" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                    {{ auth()->user()->nik ?: (auth()->user()->sobat_id ?: auth()->user()->username) }}
                                </div>
                            </li>

                            @if(auth()->user()->canSwitchRole())
                                <li class="px-3 pt-2 pb-1 bg-light border-bottom">
                                    <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                        <i class="bi bi-arrow-left-right me-1 text-primary"></i> Pilih Peran Aktif:
                                    </span>
                                </li>
                                <!-- Mode Administrator -->
                                <li>
                                    <form method="POST" action="{{ route('role.switch') }}" class="m-0">
                                        @csrf
                                        <input type="hidden" name="role" value="admin">
                                        <button type="submit" class="dropdown-item py-2.5 px-3 d-flex align-items-center justify-content-between {{ auth()->user()->activeRole() === 'admin' ? 'bg-primary text-white fw-bold' : '' }}" style="transition: all 0.15s ease;">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-shield-check fs-6 {{ auth()->user()->activeRole() === 'admin' ? 'text-white' : 'text-primary' }}"></i>
                                                <div>
                                                    <div class="small lh-1">Administrator (Satker)</div>
                                                    <div class="{{ auth()->user()->activeRole() === 'admin' ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.68rem;">Dashboard &amp; Manajemen Penuh</div>
                                                </div>
                                            </div>
                                            @if(auth()->user()->activeRole() === 'admin')
                                                <i class="bi bi-check2-circle fs-5 text-white"></i>
                                            @endif
                                        </button>
                                    </form>
                                </li>
                                <!-- Mode Pegawai / Petugas Lapangan -->
                                <li>
                                    <form method="POST" action="{{ route('role.switch') }}" class="m-0">
                                        @csrf
                                        <input type="hidden" name="role" value="pegawai">
                                        <button type="submit" class="dropdown-item py-2.5 px-3 d-flex align-items-center justify-content-between {{ auth()->user()->activeRole() === 'pegawai' ? 'bg-primary text-white fw-bold' : '' }}" style="transition: all 0.15s ease;">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-person-badge fs-6 {{ auth()->user()->activeRole() === 'pegawai' ? 'text-white' : 'text-success' }}"></i>
                                                <div>
                                                    <div class="small lh-1">Pegawai / Petugas (Satker)</div>
                                                    <div class="{{ auth()->user()->activeRole() === 'pegawai' ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.68rem;">Portal Lapangan &amp; Administrasi</div>
                                                </div>
                                            </div>
                                            @if(auth()->user()->activeRole() === 'pegawai')
                                                <i class="bi bi-check2-circle fs-5 text-white"></i>
                                            @endif
                                        </button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider my-0"></li>
                            @endif

                            <li class="py-1">
                                <a class="dropdown-item py-2 px-3 small d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person-gear text-secondary"></i> Profil &amp; Ganti Password
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-0"></li>
                            <li class="p-1">
                                <button type="button" class="dropdown-item py-2 px-3 rounded-2 small text-danger d-flex align-items-center gap-2" style="background-color: #fee2e2;" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span class="fw-bold">Sign Out / Keluar</span>
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
                    <strong>SIMPEL</strong> &copy; {{ date('Y') }} &mdash; Sistem Informasi Manajemen Pelayanan & Evaluasi Lapangan
                </div>
                <div class="d-none d-md-block text-secondary">
                    BPS Kabupaten &bull; Portal {{ auth()->user()->isAdmin() ? 'Administrator' : (auth()->user()->isPegawai() ? 'Pegawai / PML' : 'Petugas Lapangan') }}
                </div>
            </footer>
        </div>
    </div>

    @if(auth()->user()->isPetugas())
        <!-- Mobile Bottom Navigation for Petugas (HP View) -->
        <nav class="sp-bottom-nav" aria-label="Navigasi Bawah Petugas">
            <a href="{{ route('petugas.dashboard') }}" class="sp-bottom-nav-item {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door-fill"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ route('petugas.assignments.index') }}" class="sp-bottom-nav-item {{ request()->routeIs('petugas.assignments.*') ? 'active' : '' }}">
                <i class="bi bi-check2-square"></i>
                <span>Tugas</span>
            </a>
            <a href="{{ route('petugas.basts.respondents.index') }}" class="sp-bottom-nav-item {{ request()->routeIs('petugas.basts.respondents.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-check-fill"></i>
                <span>BAST</span>
            </a>
            <a href="{{ route('petugas.basts.officers.index') }}" class="sp-bottom-nav-item {{ request()->routeIs('petugas.basts.officers.*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i>
                <span>Tanda Terima</span>
            </a>
            <a href="{{ route('petugas.reports.index') }}" class="sp-bottom-nav-item {{ request()->routeIs('petugas.reports.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-check-fill"></i>
                <span>Perjadin</span>
            </a>
        </nav>
    @endif

    <!-- Modal Konfirmasi Logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center p-4">
                    <div class="mb-3 text-warning">
                        <i class="fa-solid fa-arrow-right-from-bracket fa-3x"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Konfirmasi Keluar</h5>
                    <p class="text-muted small mb-4">Apakah Anda yakin ingin keluar dari Sistem Informasi Manajemen Pelayanan & Evaluasi Lapangan (SIMPEL)?</p>
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
    @if(auth()->user()->isAdmin())
        <div class="toast-container position-fixed bottom-0 end-0 p-3" id="adminNotifToastContainer" style="z-index: 1085;"></div>
        <script>
            (function() {
                let lastUnreadCount = {{ (int) ($adminUnreadNotifCount ?? 0) }};
                let lastTopNotifId = {{ (int) ($adminRecentNotifs->first()?->id ?? 0) }};

                function renderNotifItems(items) {
                    const list = document.getElementById('adminNotifList');
                    if (!list) return;
                    list.innerHTML = '';

                    if (!Array.isArray(items) || items.length === 0) {
                        const empty = document.createElement('div');
                        empty.className = 'text-center py-4 px-3 text-muted small';
                        empty.innerHTML = '<i class="fa-regular fa-bell-slash d-block mb-1 fs-5 opacity-50"></i>Belum ada pemberitahuan laporan perjadin baru.';
                        list.appendChild(empty);
                        return;
                    }

                    items.forEach(item => {
                        const a = document.createElement('a');
                        a.href = item.url || '#';
                        a.className = 'list-group-item list-group-item-action py-2 px-3' + (!item.is_read ? ' bg-primary-subtle bg-opacity-25' : '');

                        const topRow = document.createElement('div');
                        topRow.className = 'd-flex justify-content-between align-items-start gap-2';

                        const nameDiv = document.createElement('div');
                        nameDiv.className = 'fw-bold small text-dark text-truncate';
                        if (!item.is_read) {
                            const b = document.createElement('span');
                            b.className = 'badge bg-danger me-1';
                            b.style.fontSize = '0.6rem';
                            b.textContent = 'Baru';
                            nameDiv.appendChild(b);
                        }
                        nameDiv.appendChild(document.createTextNode(item.officer_name || 'Petugas'));

                        const timeEl = document.createElement('small');
                        timeEl.className = 'text-muted text-nowrap';
                        timeEl.style.fontSize = '0.68rem';
                        timeEl.textContent = item.time_ago || '';

                        topRow.appendChild(nameDiv);
                        topRow.appendChild(timeEl);

                        const titleEl = document.createElement('div');
                        titleEl.className = 'small text-primary fw-semibold text-truncate';
                        titleEl.textContent = item.report_title || item.title || '-';

                        const msgEl = document.createElement('div');
                        msgEl.className = 'text-muted text-truncate';
                        msgEl.style.fontSize = '0.72rem';
                        msgEl.textContent = item.message || '';

                        a.appendChild(topRow);
                        a.appendChild(titleEl);
                        a.appendChild(msgEl);
                        list.appendChild(a);
                    });
                }

                function showIncomingReportToast(item) {
                    const container = document.getElementById('adminNotifToastContainer');
                    if (!container || typeof bootstrap === 'undefined') return;
                    const toastEl = document.createElement('div');
                    toastEl.className = 'toast border-0 shadow-lg';
                    toastEl.setAttribute('role', 'alert');

                    const header = document.createElement('div');
                    header.className = 'toast-header bg-primary text-white';
                    header.innerHTML = '<i class="fa-solid fa-bell me-2"></i><strong class="me-auto small">Laporan Perjadin Baru</strong><button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast"></button>';

                    const body = document.createElement('div');
                    body.className = 'toast-body bg-white small';

                    const msg = document.createElement('div');
                    msg.className = 'mb-2 text-dark';
                    msg.textContent = `${item.officer_name || 'Petugas'} membuat laporan "${item.report_title || '-'}".`;

                    const action = document.createElement('a');
                    action.href = item.url || '{{ route('admin.reports.index') }}';
                    action.className = 'btn btn-xs btn-sp-primary py-1 px-2';
                    action.style.fontSize = '0.75rem';
                    action.innerHTML = '<i class="fa-solid fa-eye me-1"></i> Lihat, Edit &amp; ACC';

                    body.appendChild(msg);
                    body.appendChild(action);
                    toastEl.appendChild(header);
                    toastEl.appendChild(body);
                    container.appendChild(toastEl);

                    const toast = new bootstrap.Toast(toastEl, { delay: 9000 });
                    toast.show();
                    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
                }

                async function pollAdminNotifications() {
                    if (document.hidden) return;
                    try {
                        const res = await fetch('{{ route('admin.notifications.poll') }}', {
                            headers: { 'Accept': 'application/json' },
                            credentials: 'same-origin'
                        });
                        if (!res.ok) return;
                        const data = await res.json();

                        const badge = document.getElementById('adminNotifBadge');
                        if (badge) {
                            badge.textContent = data.unread_count;
                            badge.classList.toggle('d-none', data.unread_count <= 0);
                        }

                        const sidebarBadge = document.getElementById('sidebarReportBadge');
                        const sbCount = Math.max(Number(data.pending_count || 0), Number(data.unread_count || 0));
                        if (sidebarBadge) {
                            sidebarBadge.textContent = sbCount;
                            sidebarBadge.classList.toggle('d-none', sbCount <= 0);
                        }

                        const subtitle = document.getElementById('adminPendingSubtitle');
                        if (subtitle) {
                            subtitle.textContent = `${data.pending_count || 0} menunggu ACC`;
                        }

                        if (Array.isArray(data.notifications)) {
                            renderNotifItems(data.notifications);
                            const topItem = data.notifications[0];
                            if (topItem && topItem.id > lastTopNotifId && data.unread_count > lastUnreadCount) {
                                showIncomingReportToast(topItem);
                            }
                            lastTopNotifId = topItem ? topItem.id : lastTopNotifId;
                        }
                        lastUnreadCount = Number(data.unread_count || 0);
                    } catch (e) {
                        // silent background poll
                    }
                }

                setInterval(pollAdminNotifications, 20000);
            })();
        </script>
    @endif
    @stack('scripts')
</body>
</html>

