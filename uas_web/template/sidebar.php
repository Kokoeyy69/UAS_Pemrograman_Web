<?php
global $mod, $page;
function isActive($targetMod, $targetPage) {
    global $mod, $page;
    if ($mod == $targetMod && $page == $targetPage) { 
        return 'active-menu shadow-sm';
    } 
    return 'text-secondary hover-menu';
}
?>

<div class="d-lg-none mobile-header">
    <button onclick="toggleSidebar(event)" class="btn-mobile-menu">
        <i class="fas fa-bars"></i>
    </button>
    <div class="fw-bold text-primary" style="letter-spacing: 1px; font-size: 1.1rem;">
        <i class="fas fa-cube me-2"></i>MODULAR
    </div>
    <div style="width: 38px;"></div> 
</div>

<style>
/* STYLE HEADER PUTIH */
.mobile-header {
    position: fixed; top: 0; left: 0; right: 0;
    height: 70px; background: #ffffff;
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    z-index: 4000;
}
.btn-mobile-menu {
    width: 38px; height: 38px; background: #f0f2f5; color: #4e73df;
    border: none; border-radius: 10px; display: flex; align-items: center; justify-content: center;
    font-size: 16px; transition: all 0.2s;
}
.btn-mobile-menu:active { transform: scale(0.9); background: #e2e6ea; }

/* 🔥 CSS JARAK PRESISI (NO GAP LEBAR) 🔥 */
@media (max-width: 991px) {
    /* 1. Body Pas 70px (Sama dengan tinggi header) */
    body {
        padding-top: 70px !important;
    }
    
    /* 2. Wrapper Nol Jarak */
    .content-wrapper {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }

    /* 3. KUNCINYA DISINI: 
       Kita cari elemen div pertama (Banner Biru) dan atur margin atasnya.
       Saya kasih 15px biar rapi (ga nempel header, ga jauh juga).
    */
    .content-wrapper > div:first-child {
        margin-top: 15px !important;
    }

    /* Sidebar Fix */
    .sidebar {
        position: fixed !important; left: -280px !important; top: 0; bottom: 0;
        height: 100vh !important; width: 260px !important;
        z-index: 9999 !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .sidebar.show { left: 0 !important; box-shadow: 0 0 100px rgba(0,0,0,0.5) !important; }
}

/* DESKTOP STYLE */
.nav-label { font-size: 0.65rem; font-weight: 700; color: #adb5bd; letter-spacing: 1.5px; }
.active-menu { background-color: #4e73df !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2) !important; }
.active-menu .icon-box { color: #ffffff !important; }
.hover-menu:hover { background-color: #f8f9fc !important; color: #4e73df !important; }
.icon-box { width: 32px; font-size: 1.1rem; transition: all 0.3s; }
.list-group-item { background: transparent; transition: all 0.2s ease; font-size: 0.9rem; font-weight: 500; }
.logo-anim { animation: logoPulse 2s infinite; }
@keyframes logoPulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
.list-group::-webkit-scrollbar { width: 0; }
</style>

<div class="sidebar d-flex flex-column flex-shrink-0 bg-white shadow-sm h-100 border-end" id="mainSidebar">
    <div class="sidebar-brand d-flex align-items-center justify-content-between py-4 px-3 border-bottom mb-2">
        <div class="d-flex align-items-center">
            <div class="logo-wrapper bg-primary bg-opacity-10 p-2 rounded-3 me-2 logo-anim">
                <i class="fas fa-cube text-primary fa-lg"></i>
            </div>
            <span class="fs-5 fw-bold text-dark" style="letter-spacing: 1px;">MODULAR</span>
        </div>
        <button onclick="closeSidebar()" class="d-lg-none btn btn-sm btn-light text-danger shadow-sm rounded-circle border-0" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-times fa-lg"></i>
        </button>
    </div>
    
    <div class="list-group list-group-flush px-3 py-3 flex-grow-1 gap-1" style="overflow-y: auto;">
        <div class="nav-label mb-2 ms-2 mt-2">UTAMA</div>
        <a href="/uas_web/index.php/home/index" class="list-group-item list-group-item-action py-2 rounded-3 border-0 d-flex align-items-center <?= isActive('home', 'index') ?>">
            <i class="fas fa-tachometer-alt icon-box text-center"></i> <span class="ms-1">Dashboard</span>
        </a>
        <a href="/uas_web/index.php/artikel/list" class="list-group-item list-group-item-action py-2 rounded-3 border-0 d-flex align-items-center <?= isActive('artikel', 'list') ?>">
            <i class="fas fa-newspaper icon-box text-center"></i> <span class="ms-1">Berita</span>
        </a>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <div class="nav-label mb-2 mt-4 ms-2">ADMINISTRATOR</div>
            <a href="/uas_web/index.php/artikel/index" class="list-group-item list-group-item-action py-2 rounded-3 border-0 d-flex align-items-center <?= isActive('artikel', 'index') ?>">
                <i class="fas fa-edit icon-box text-center"></i> <span class="ms-1">Kelola Artikel</span>
            </a>
            <a href="/uas_web/index.php/user/index" class="list-group-item list-group-item-action py-2 rounded-3 border-0 d-flex align-items-center <?= isActive('user', 'index') ?>">
                <i class="fas fa-user-shield icon-box text-center"></i> <span class="ms-1">Kelola User</span>
            </a>
        <?php endif; ?>
        <div class="nav-label mb-2 mt-4 ms-2">PROFIL AKUN</div>
        <a href="/uas_web/index.php/user/profile" class="list-group-item list-group-item-action py-2 rounded-3 border-0 d-flex align-items-center <?= isActive('user', 'profile') ?>">
            <i class="fas fa-user-circle icon-box text-center"></i> <span class="ms-1">Profil Saya</span>
        </a>
    </div>

    <div class="p-3 border-top bg-light">
        <a href="/uas_web/index.php/user/logout" class="btn btn-outline-danger w-100 fw-bold shadow-sm py-2 d-flex align-items-center justify-content-center btn-logout" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')">
            <i class="fas fa-power-off me-2"></i> Logout
        </a>
    </div>
</div>

<script>
    const sidebar = document.getElementById('mainSidebar');
    function toggleSidebar(e) { e.stopPropagation(); sidebar.classList.toggle('show'); }
    function closeSidebar() { sidebar.classList.remove('show'); }
    document.addEventListener('click', function(e) {
        if (sidebar.classList.contains('show') && !sidebar.contains(e.target) && !e.target.closest('.btn-mobile-menu')) {
            sidebar.classList.remove('show');
        }
    });
</script>