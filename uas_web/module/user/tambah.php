<?php
/**
 * 1. PROTEKSI AKSES (ACL)
 * Memastikan hanya Administrator yang memiliki wewenang menambah pengguna baru.
 */
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses Ditolak: Anda tidak memiliki izin untuk menambah pengguna!'); window.location.href='/uas_web/index.php/home/index';</script>";
    exit;
}

$message = "";

/**
 * 2. PROSES SIMPAN DATA
 */
if (isset($_POST['submit'])) {
    $db = new Database();
    
    // Ambil dan bersihkan input (Sanitasi dasar)
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role     = $_POST['role'];
    $email    = trim($_POST['email']);
    $no_hp    = trim($_POST['no_hp']);

    try {
        // A. Validasi: Pastikan field utama tidak kosong
        if (empty($nama) || empty($username) || empty($password)) {
            $message = "<div class='alert alert-danger rounded-4 shadow-sm'><i class='fas fa-exclamation-circle me-2'></i>Nama, Username, dan Password wajib diisi!</div>";
        } 
        // B. Cek duplikasi username menggunakan PDO
        else {
            $check = $db->runQuery("SELECT id FROM user WHERE username = :u", [':u' => $username]);
            
            if ($check->rowCount() > 0) {
                $message = "<div class='alert alert-danger rounded-4 shadow-sm'><i class='fas fa-user-times me-2'></i>Username '<b>$username</b>' sudah digunakan!</div>";
            } else {
                /**
                 * C. ENKRIPSI PASSWORD
                 * Menggunakan BCRYPT (default) agar password aman di database.
                 */
                $pass_hash = password_hash($password, PASSWORD_DEFAULT);
                
                /**
                 * D. Eksekusi INSERT ke tabel 'user'
                 * Kolom 'foto' dibiarkan NULL agar user bisa upload sendiri di halaman profil.
                 */
                $sql = "INSERT INTO user (username, password, nama, email, no_hp, role, is_active, foto) 
                        VALUES (:u, :p, :n, :e, :h, :r, 1, NULL)";
                $params = [
                    ':u' => $username,
                    ':p' => $pass_hash,
                    ':n' => $nama,
                    ':e' => $email,
                    ':h' => $no_hp,
                    ':r' => $role
                ];
                
                if ($db->runQuery($sql, $params)) {
                    echo "<script>alert('User berhasil ditambahkan!'); window.location='/uas_web/index.php/user/index';</script>";
                    exit;
                }
            }
        }
    } catch (Exception $e) {
        // Sembunyikan detail teknis database dari layar
        $message = "<div class='alert alert-danger rounded-4 shadow-sm'>Terjadi kesalahan saat menyimpan data.</div>";
    }
}
?>

<div class="row justify-content-center px-3 mt-4 fade-in mb-5">
    <div class="col-xl-8 col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-primary fw-bold"><i class="fas fa-user-plus me-2"></i>Tambah Pengguna</h1>
                <p class="text-muted small mb-0">Mendaftarkan personel baru dengan standar keamanan terenkripsi.</p>
            </div>
            <a href="/uas_web/index.php/user/index" class="btn btn-light border rounded-pill shadow-sm px-4 fw-bold">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4 p-md-5">
                <?= $message ?>
                
                <form method="POST">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-id-card text-muted"></i></span>
                                <input type="text" name="nama" class="form-control bg-light border-0 py-2 shadow-none" placeholder="Contoh: Budi Santoso" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-at text-muted"></i></span>
                                <input type="text" name="username" class="form-control bg-light border-0 py-2 shadow-none" placeholder="Contoh: budi_s" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Password Login</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control bg-light border-0 py-2 shadow-none" placeholder="Masukkan password awal..." required>
                        </div>
                        <div class="form-text small"><i class="fas fa-shield-alt text-primary me-1"></i>Sistem otomatis mengamankan password menggunakan hashing.</div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control bg-light border-0 py-2 shadow-none" placeholder="email@domain.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">No. Handphone</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-phone text-muted"></i></span>
                                <input type="text" name="no_hp" class="form-control bg-light border-0 py-2 shadow-none" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label small fw-bold text-muted text-uppercase">Role / Hak Akses</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-user-shield text-muted"></i></span>
                            <select name="role" class="form-select bg-light border-0 py-2 shadow-none">
                                <option value="user" selected>User Biasa (Terbatas)</option>
                                <option value="admin">Administrator (Penuh)</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" name="submit" class="btn btn-primary rounded-pill py-3 fw-bold shadow-sm transition-up">
                            <i class="fas fa-save me-2"></i>Simpan Data Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.transition-up { transition: all 0.3s ease; }
.transition-up:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
.fade-in { animation: fadeIn 0.8s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
</style>