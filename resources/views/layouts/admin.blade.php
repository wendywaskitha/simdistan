<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - SIM-Distan</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Custom Admin Styles -->
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --dark-sidebar: #0f172a;
            --dark-body: #1e293b;
            --card-bg: #ffffff;
            --text-main: #334155;
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar-wrapper {
            height: 100vh;
            width: 260px;
            background-color: var(--dark-sidebar);
            color: #94a3b8;
            transition: margin var(--transition-speed) ease-out;
            position: fixed;
            z-index: 1000;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
        }

        /* Slim Scrollbar for Webkit Browsers */
        #sidebar-wrapper::-webkit-scrollbar {
            width: 5px;
        }

        #sidebar-wrapper::-webkit-scrollbar-track {
            background: transparent;
        }

        #sidebar-wrapper::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }

        #sidebar-wrapper::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 1.5rem 1.25rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #sidebar-wrapper .list-group-item {
            background-color: transparent;
            color: #94a3b8;
            border: none;
            padding: 0.85rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            border-radius: 8px;
            margin: 0.2rem 0.75rem;
        }

        #sidebar-wrapper .list-group-item:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        #sidebar-wrapper .list-group-item.active {
            color: #fff;
            background-color: var(--primary);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        #sidebar-wrapper .sidebar-section-title {
            padding: 1rem 1.5rem 0.4rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #475569;
        }

        /* Page Content Wrapper */
        #page-content-wrapper {
            min-width: 100vw;
            margin-left: 0;
            transition: margin var(--transition-speed) ease-out;
            padding-left: 0;
        }

        @media (min-width: 768px) {
            #page-content-wrapper {
                min-width: 0;
                width: 100%;
                padding-left: 260px;
            }
        }

        @if(auth()->check() && auth()->user()->hasRole('Kepala Dinas'))
        @media (min-width: 768px) {
            #page-content-wrapper {
                padding-left: 0 !important;
            }
        }
        @endif

        /* Navbar Styling */
        .admin-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.85rem 1.5rem;
        }

        .toggle-btn {
            background: transparent;
            border: none;
            color: #475569;
            font-size: 1.5rem;
            padding: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        /* Profile Dropdown */
        .profile-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 500;
        }

        .profile-dropdown .dropdown-toggle::after {
            display: none;
        }

        .avatar-circle {
            width: 36px;
            height: 36px;
            background-color: #e2e8f0;
            color: #475569;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            border: 2px solid #cbd5e1;
        }

        /* Card Customizations */
        .custom-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .custom-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
        }

        /* Active Sidebar State Toggle for Mobile */
        .toggled #sidebar-wrapper {
            margin-left: -260px;
        }
        
        @media (max-width: 767.98px) {
            #sidebar-wrapper {
                margin-left: -260px;
            }
            .toggled #sidebar-wrapper {
                margin-left: 0;
            }
            #page-content-wrapper {
                padding-left: 0 !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        @unless(auth()->user()->hasRole('Kepala Dinas'))
        <div id="sidebar-wrapper">
            <div class="sidebar-heading">
                <i class="bi bi-flower1 text-success fs-3"></i>
                <div>
                    <span class="d-block lh-1">SIM-Distan</span>
                    <small class="fs-6 fw-normal text-muted">Kab. Muna Barat</small>
                </div>
            </div>
            
            <div class="list-group list-group-flush py-3">
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ Route::is('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
                <a href="{{ route('statistik.index') }}" class="list-group-item list-group-item-action {{ Route::is('statistik.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line-fill"></i> Statistik &amp; Grafik
                </a>
                <a href="{{ route('laporan-bps.index') }}" class="list-group-item list-group-item-action {{ Route::is('laporan-bps.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan
                </a>

                @can('akses penyuluhan')
                <!-- Section Penyuluhan -->
                <div class="sidebar-section-title">Penyuluhan</div>
                <a href="{{ route('penyuluhs.index') }}" class="list-group-item list-group-item-action {{ Route::is('penyuluhs.*') ? 'active' : '' }}">
                    <i class="bi bi-person-workspace"></i> Penyuluh
                </a>
                <a href="{{ route('gapoktans.index') }}" class="list-group-item list-group-item-action {{ Route::is('gapoktans.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3-fill"></i> Gapoktan
                </a>
                <a href="{{ route('kelompok-tanis.index') }}" class="list-group-item list-group-item-action {{ Route::is('kelompok-tanis.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Kelompok Tani
                </a>
                <a href="{{ route('petanis.index') }}" class="list-group-item list-group-item-action {{ Route::is('petanis.*') ? 'active' : '' }}">
                    <i class="bi bi-person-fill-gear"></i> Data Petani
                </a>
                <a href="{{ route('bpps.index') }}" class="list-group-item list-group-item-action {{ Route::is('bpps.*') ? 'active' : '' }}">
                    <i class="bi bi-building-fill-gear"></i> Data BPP
                </a>
                <a href="{{ route('penerima-bantuan.index') }}" class="list-group-item list-group-item-action {{ Route::is('penerima-bantuan.*') ? 'active' : '' }}">
                    <i class="bi bi-gift-fill"></i> Penerima Bantuan
                </a>
                @endcan

                <!-- Section Produksi -->
                @if(auth()->user()->hasAnyPermission(['akses tanaman pangan', 'akses hortikultura', 'akses perkebunan']))
                <div class="sidebar-section-title">Produksi & Komoditas</div>
                @endif
                
                @can('akses tanaman pangan')
                <a href="{{ route('tanaman-pangan.index') }}" class="list-group-item list-group-item-action {{ Route::is('tanaman-pangan.index') ? 'active' : '' }}">
                    <i class="bi bi-cone-striped"></i> Tanaman Pangan
                </a>
                <a href="{{ route('bantuan-benih-pangan.index') }}" class="list-group-item list-group-item-action {{ Route::is('bantuan-benih-pangan.*') ? 'active' : '' }} ps-4 small">
                    <i class="bi bi-gift-fill"></i> Kelola Bantuan Benih
                </a>
                @endcan
                
                @can('akses hortikultura')
                <a href="{{ route('hortikultura.index') }}" class="list-group-item list-group-item-action {{ Route::is('hortikultura.index') ? 'active' : '' }}">
                    <i class="bi bi-tree-fill"></i> Hortikultura
                </a>
                <a href="{{ route('bantuan-bibit-horti.index') }}" class="list-group-item list-group-item-action {{ Route::is('bantuan-bibit-horti.*') ? 'active' : '' }} ps-4 small">
                    <i class="bi bi-gift-fill"></i> Kelola Bantuan Bibit
                </a>
                @endcan
                
                @can('akses perkebunan')
                <a href="{{ route('perkebunan.index') }}" class="list-group-item list-group-item-action {{ Route::is('perkebunan.index') ? 'active' : '' }}">
                    <i class="bi bi-tree-fill"></i> Perkebunan
                </a>
                <a href="{{ route('bantuan-bibit-perkebunan.index') }}" class="list-group-item list-group-item-action {{ Route::is('bantuan-bibit-perkebunan.*') ? 'active' : '' }} ps-4 small">
                    <i class="bi bi-gift-fill"></i> Kelola Bantuan Bibit
                </a>
                @endcan

                <!-- Section PSP -->
                @can('akses psp')
                <div class="sidebar-section-title">Prasarana & Sarana (PSP)</div>
                <a href="{{ route('alsintans.index') }}" class="list-group-item list-group-item-action {{ Route::is('alsintans.*') ? 'active' : '' }}">
                    <i class="bi bi-truck-flatbed"></i> Bantuan Alsintan
                </a>
                <a href="{{ route('infrastrukturs.index') }}" class="list-group-item list-group-item-action {{ Route::is('infrastrukturs.*') ? 'active' : '' }}">
                    <i class="bi bi-water"></i> Infrastruktur & Irigasi
                </a>
                <a href="{{ route('distribusi-pupuk.index') }}" class="list-group-item list-group-item-action {{ Route::is('distribusi-pupuk.*') ? 'active' : '' }}">
                    <i class="bi bi-droplet-half"></i> Distribusi Pupuk
                </a>
                @endcan

                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->can('akses psp'))
                <!-- Section Admin/PSP Master -->
                <div class="sidebar-section-title">Master & System (PSP)</div>
                <a href="{{ route('kuota-tahunan.index') }}" class="list-group-item list-group-item-action {{ Route::is('kuota-tahunan.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i> Kuota Tahunan Pupuk
                </a>
                <a href="{{ route('toko-pupuks.index') }}" class="list-group-item list-group-item-action {{ Route::is('toko-pupuks.*') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i> Toko Distributor Pupuk
                </a>
                <a href="{{ route('jenis-pupuks.index') }}" class="list-group-item list-group-item-action {{ Route::is('jenis-pupuks.*') ? 'active' : '' }}">
                    <i class="bi bi-droplet-fill"></i> Jenis Pupuk
                </a>
                <a href="{{ route('jenis-alats.index') }}" class="list-group-item list-group-item-action {{ Route::is('jenis-alats.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-wide-connected"></i> Jenis Alat Alsintan
                </a>
                @endif

                @role('Super Admin')
                <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action {{ Route::is('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill text-info"></i> Kelola Pengguna
                </a>
                <!-- Section Admin -->
                <div class="sidebar-section-title">Master & System</div>
                <a href="{{ route('bidangs.index') }}" class="list-group-item list-group-item-action {{ Route::is('bidangs.*') ? 'active' : '' }}">
                    <i class="bi bi-briefcase-fill"></i> Data Bidang
                </a>
                <a href="{{ route('kecamatans.index') }}" class="list-group-item list-group-item-action {{ Route::is('kecamatans.*') ? 'active' : '' }}">
                    <i class="bi bi-map-fill"></i> Data Kecamatan
                </a>
                <a href="{{ route('desas.index') }}" class="list-group-item list-group-item-action {{ Route::is('desas.*') ? 'active' : '' }}">
                    <i class="bi bi-pin-map-fill"></i> Data Desa
                </a>
                <a href="{{ route('kategori-komoditas.index') }}" class="list-group-item list-group-item-action {{ Route::is('kategori-komoditas.*') ? 'active' : '' }}">
                    <i class="bi bi-tags-fill"></i> Kategori Komoditas
                </a>
                @endrole

                @hasanyrole('Super Admin|Tanaman Pangan|Hortikultura|Perkebunan')
                @if(!auth()->user()->hasRole('Super Admin'))
                <div class="sidebar-section-title">Master Data</div>
                @endif
                <a href="{{ route('komoditas.index') }}" class="list-group-item list-group-item-action {{ Route::is('komoditas.*') ? 'active' : '' }}">
                    <i class="bi bi-egg-fried"></i> Data Komoditas
                </a>
                @endhasanyrole

                @role('Super Admin')
                <a href="{{ route('varietas.index') }}" class="list-group-item list-group-item-action {{ Route::is('varietas.*') ? 'active' : '' }}">
                    <i class="bi bi-flower2"></i> Data Varietas
                </a>
                <a href="{{ route('satuans.index') }}" class="list-group-item list-group-item-action {{ Route::is('satuans.*') ? 'active' : '' }}">
                    <i class="bi bi-calculator"></i> Data Satuan
                </a>
                @endrole
            </div>
        </div>
        @endunless
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper" style="{{ auth()->user()->hasRole('Kepala Dinas') ? 'width: 100vw; min-width: 100vw;' : '' }}">
            @if(session()->has('impersonator_id'))
                <div class="alert alert-warning border-0 rounded-0 mb-0 py-2 px-4 d-flex align-items-center justify-content-between shadow-sm" style="z-index: 1040; background: linear-gradient(135deg, #f59e0b, #d97706); color:#fff;">
                    <div class="small fw-semibold">
                        <i class="bi bi-shield-exclamation me-2"></i>
                        Anda sedang masuk sebagai <strong>{{ auth()->user()->name }}</strong> (Sesi Impersonate).
                    </div>
                    <a href="{{ route('users.leave-impersonate') }}" class="btn btn-sm btn-light fw-bold text-dark rounded-pill px-3" style="font-size:11px;">
                        <i class="bi bi-box-arrow-left me-1"></i>Kembali ke Admin
                    </a>
                </div>
            @endif
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg admin-navbar sticky-top">
                <div class="container-fluid">
                    @unless(auth()->user()->hasRole('Kepala Dinas'))
                    <button class="toggle-btn me-3" id="menu-toggle">
                        <i class="bi bi-list"></i>
                    </button>
                    @endunless
                    
                    <span class="navbar-text fw-semibold d-none d-sm-inline-block text-dark">
                        Selamat Datang kembali, <span class="text-primary">{{ Auth::user()->name }}</span>
                    </span>

                    <div class="ms-auto d-flex align-items-center gap-3">
                        <!-- User Role Badge -->
                        <span class="badge bg-light text-success border border-success-subtle px-3 py-2 rounded-pill">
                            {{ Auth::user()->roles->first()?->name ?? 'Guest' }}
                        </span>

                        <!-- Profile Dropdown -->
                        <div class="dropdown profile-dropdown">
                            <a class="dropdown-toggle" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="avatar-circle">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-2 rounded-3" aria-labelledby="profileDropdown">
                                <li><a class="dropdown-item py-2" href="#"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item py-2 text-danger" type="submit">
                                            <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content Container -->
            <div class="container-fluid p-4">
                @yield('content')
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->

    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Script -->
    <script>
        window.addEventListener('DOMContentLoaded', event => {
            const menuToggle = document.body.querySelector('#menu-toggle');
            if (menuToggle) {
                menuToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('toggled');
                });
            }
        });

        // SweetAlert2 Flash Session Handler
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded-4'
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#10b981',
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'rounded-3'
                }
            });
        @endif

        // SweetAlert2 Global Delete Confirmation
        $(document).on('click', '.btn-delete-trigger', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus akan dipindahkan ke tempat pembuangan sementara!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'rounded-3 px-4 py-2',
                    cancelButton: 'rounded-3 px-4 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
