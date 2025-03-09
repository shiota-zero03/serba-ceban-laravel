<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link w-100 text-center d-flex align-items-center justify-content-center">
            <h4 class="text-center fw-bolder ms-2">Serba Ceban</h4>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
      <!-- Dashboard -->
        <li class="menu-item" id="dashboard">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Master Data</span>
        </li>
        @if(auth()->user()->role !== 'MITRA')
            <li class="menu-item" id="mitra">
                <a href="{{ route('mitra.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bxs-user-account"></i>
                    <div data-i18n="Basic">Manajemen Mitra</div>
                </a>
            </li>
        @endif
        <li class="menu-item" id="produk">
            <a href="{{ route('produk.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-category"></i>
                <div data-i18n="Basic">Manajemen Produk</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Transaksi</span>
        </li>

        @if(auth()->user()->role !== 'MITRA')
            <li class="menu-item" id="pemesanan">
                <a href="{{ route('pemesanan.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-archive-out"></i>
                    <div data-i18n="Basic">Pemesanan Barang</div>
                </a>
            </li>
            <li class="menu-item" id="penerimaan">
                <a href="{{ route('penerimaan.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-archive-in"></i>
                    <div data-i18n="Basic">Penerimaan Barang</div>
                </a>
            </li>
            <li class="menu-item" id="penjualan">
                <a href="{{ route('penjualan.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bxs-cart-add"></i>
                    <div data-i18n="Basic">Penjualan</div>
                </a>
            </li>
            <li class="menu-item" id="pembayaran">
                <a href="{{ route('pembayaran.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-wallet"></i>
                    <div data-i18n="Basic">Pembayaran</div>
                </a>
            </li>
            <li class="menu-item" id="laporan">
                <a href="{{ route('laporan.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-bar-chart-alt"></i>
                    <div data-i18n="Basic">Laporan Transaksi</div>
                </a>
            </li>
        @else
        <li class="menu-item" id="pesan-terima">
            <a href="{{ route('pesan-terima.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-archive"></i>
                <div data-i18n="Basic">Pesan dan Terima</div>
            </a>
        </li>
            <li class="menu-item" id="data-pembayaran">
                <a href="{{ route('data-pembayaran.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-wallet"></i>
                    <div data-i18n="Basic">Data Pembayaran</div>
                </a>
            </li>
        @endif

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pengaturan</span>
        </li>
        <li class="menu-item" id="profile">
            <a href="{{ route('profile.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-cog"></i>
                <div data-i18n="Basic">Kelola Profil</div>
            </a>
        </li>
    </ul>
  </aside>
