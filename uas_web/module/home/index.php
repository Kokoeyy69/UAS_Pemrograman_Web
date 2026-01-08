<?php
/**
 * 1. PROTEKSI AKSES & INISIALISASI
 */
if (!isset($_SESSION['is_login'])) {
    header("Location: /uas_web/index.php/user/login");
    exit;
}

$db = new Database();
$username_session = $_SESSION['username'];

/**
 * 2. LOGIKA SAPAAN WAKTU & KALENDER
 */
date_default_timezone_set('Asia/Jakarta');
$jam = date('H');
if ($jam >= 5 && $jam < 12) { 
    $salam = "Selamat Pagi"; $icon_salam = "fa-cloud-sun"; 
} elseif ($jam >= 12 && $jam < 15) { 
    $salam = "Selamat Siang"; $icon_salam = "fa-sun"; 
} elseif ($jam >= 15 && $jam < 18) { 
    $salam = "Selamat Sore"; $icon_salam = "fa-cloud-sun-rain"; 
} else { 
    $salam = "Selamat Malam"; $icon_salam = "fa-moon"; 
}

/**
 * 3. AMBIL DATA STATISTIK & USER (PDO)
 */
try {
    $total_user = $db->runQuery("SELECT id FROM user")->rowCount();
    $total_admin = $db->runQuery("SELECT id FROM user WHERE role = 'admin'")->rowCount();
    $total_artikel = $db->runQuery("SELECT id FROM artikel")->rowCount();
    $stmt_user = $db->runQuery("SELECT * FROM user WHERE username = :u", [':u' => $username_session]);
    $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);
    $latest_news = $db->runQuery("SELECT judul, tanggal FROM artikel ORDER BY tanggal DESC LIMIT 3")->fetchAll();
} catch (Exception $e) {
    $error_msg = "Gagal memuat statistik database.";
}

$foto_profil = !empty($user_data['foto']) 
    ? $user_data['foto'] 
    : "https://ui-avatars.com/api/?name=" . urlencode($user_data['nama']) . "&background=4e73df&color=fff&size=128&bold=true";
?>

<div class="fade-in px-3 mt-3 mb-5">
    
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden position-relative">
        <div class="card-body p-4 p-md-5 text-white" style="background: linear-gradient(120deg, #4e73df, #224abe);">
            <i class="fas fa-rocket position-absolute text-white opacity-10 d-none d-md-block" style="font-size: 10rem; right: -20px; bottom: -20px;"></i>
            <div class="position-relative z-1">
                <h2 class="fw-bold display-6 mb-2">
                    <i class="fas <?= $icon_salam ?> me-2 text-warning"></i> <?= $salam ?>, <?= htmlspecialchars($user_data['nama']) ?>!
                </h2>
                <p class="lead mb-0 opacity-75">Panel kendali Modular System anda berjalan optimal dengan standar keamanan PDO.</p>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4 text-dark">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 py-2 border-start border-4 border-primary rounded-3 transition-up">
                <div class="card-body">
                    <div class="text-xs fw-bold text-primary text-uppercase mb-1 small">Total Artikel</div>
                    <div class="h3 mb-0 fw-bold"><?= $total_artikel ?? 0 ?></div>
                    <div class="mt-2 small text-muted"><i class="fas fa-file-alt me-1"></i> Konten Publik</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 py-2 border-start border-4 border-success rounded-3 transition-up">
                <div class="card-body">
                    <div class="text-xs fw-bold text-success text-uppercase mb-1 small">Total Pengguna</div>
                    <div class="h3 mb-0 fw-bold"><?= $total_user ?? 0 ?></div>
                    <div class="mt-2 small text-muted"><i class="fas fa-user-check me-1"></i> Akun Terdaftar</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 py-2 border-start border-4 border-info rounded-3 transition-up">
                <div class="card-body">
                    <div class="text-xs fw-bold text-info text-uppercase mb-1 small">Administrator</div>
                    <div class="h3 mb-0 fw-bold"><?= $total_admin ?? 0 ?></div>
                    <div class="mt-2 small text-muted"><i class="fas fa-user-shield me-1"></i> Hak Akses Penuh</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 py-2 border-start border-4 border-warning rounded-3 transition-up">
                <div class="card-body">
                    <div class="text-xs fw-bold text-warning text-uppercase mb-1 small">Status Sistem</div>
                    <div class="h3 mb-0 fw-bold text-warning">Stabil</div>
                    <div class="mt-2 small text-muted"><i class="fas fa-server me-1"></i> Server Online</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-bolt me-2 text-warning"></i>Akses Cepat</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="/uas_web/index.php/artikel/tambah" class="btn btn-outline-primary w-100 p-3 rounded-3 text-start transition-up">
                                <i class="fas fa-pen-nib mb-2 d-block fa-lg"></i> Tulis Artikel
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="/uas_web/index.php/user/profile" class="btn btn-outline-dark w-100 p-3 rounded-3 text-start transition-up">
                                <i class="fas fa-user-cog mb-2 d-block fa-lg"></i> Pengaturan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-history me-2 text-primary"></i>Aktivitas Konten Terbaru</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach($latest_news as $news): ?>
                        <div class="list-group-item border-0 px-4 py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-light p-2 rounded-circle me-3"><i class="far fa-file-alt text-primary"></i></div>
                                <div>
                                    <div class="fw-bold small text-dark"><?= htmlspecialchars($news['judul']) ?></div>
                                    <small class="text-muted"><?= date('d M Y', strtotime($news['tanggal'])) ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="p-4 text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(120deg, #4e73df, #224abe);">
                    <div>
                        <h6 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-2"></i>Agenda Hari Ini</h6>
                        <small class="opacity-75"><?= date('l, d F Y'); ?></small>
                    </div>
                    <div class="text-end">
                        <h3 class="mb-0 fw-bold"><?= date('H:i'); ?></h3>
                        <small class="opacity-75">WIB</small>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="p-4 border-bottom bg-light bg-opacity-50">
                        <div class="d-flex align-items-center">
                            <img src="<?= $foto_profil ?>" class="rounded-circle border border-2 border-white shadow-sm" width="60" height="60" style="object-fit: cover;">
                            <div class="ms-3">
                                <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($user_data['nama']) ?></h6>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill mt-1" style="font-size: 0.7rem;">
                                    <i class="fas fa-user-shield me-1"></i> <?= strtoupper($user_data['role']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-4 py-3 bg-light border-top text-center mt-auto">
                        <p class="small text-muted mb-0">
                            <i class="fas fa-lock me-1 text-success"></i> Data sistem terenkripsi standar OOP.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.transition-up { transition: all 0.3s ease; }
.transition-up:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.1)!important; }
.fade-in { animation: fadeIn 0.8s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
</style>