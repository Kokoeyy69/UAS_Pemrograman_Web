<?php
/**
 * 1. PROTEKSI AKSES (ACL)
 * Memastikan hanya Administrator yang berwenang mengakses halaman ini.
 */
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: /uas_web/index.php/home/index");
    exit;
}

$db = new Database();
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Proteksi jika ID tidak valid atau tidak ada di URL
if (!$id) {
    header("Location: /uas_web/index.php/user/index");
    exit;
}

/**
 * 2. PROSES UPDATE DATA (POST)
 */
if (isset($_POST['submit'])) {
    $nama      = trim($_POST['nama']);
    $role      = $_POST['role'];
    $is_active = $_POST['is_active'];
    $password  = $_POST['password'];

    try {
        /**
         * Update Data Utama (Nama, Role, Status Aktif)
         * Menggunakan PDO Prepared Statements untuk keamanan.
         */
        $sql_update = "UPDATE user SET nama = :nama, role = :role, is_active = :active WHERE id = :id";
        $params = [
            ':nama'   => $nama,
            ':role'   => $role,
            ':active' => $is_active,
            ':id'     => $id
        ];
        $db->runQuery($sql_update, $params);

        /**
         * Sinkronisasi Sesi
         * Jika admin mengedit profilnya sendiri, perbarui nama di sesi agar tampilan header berubah.
         */
        if ($id === (int)$_SESSION['user_id']) {
            $_SESSION['nama'] = $nama;
            $_SESSION['role'] = $role;
        }

        /**
         * Update Password
         * Hanya diproses jika kolom password diisi.
         */
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $db->runQuery("UPDATE user SET password = :pass WHERE id = :id", [
                ':pass' => $hash,
                ':id'   => $id
            ]);
        }

        echo "<script>alert('Data pengguna berhasil diperbarui!'); window.location.href='/uas_web/index.php/user/index';</script>";
        exit;
    } catch (Exception $e) {
        $error_msg = "Gagal memperbarui data database.";
    }
}

/**
 * 3. AMBIL DATA USER TERBARU
 * Mengambil data untuk ditampilkan kembali ke dalam form.
 */
$stmt = $db->runQuery("SELECT * FROM user WHERE id = :id", [':id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) { 
    echo "<div class='alert alert-danger m-3 shadow-sm'>User tidak ditemukan dalam sistem database.</div>"; 
    exit; 
}
?>

<div class="row justify-content-center fade-in px-3 mt-4">
    <div class="col-md-7 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-header bg-primary py-3 border-0">
                <h5 class="m-0 fw-bold text-white"><i class="fas fa-user-edit me-2"></i>Edit Informasi Pengguna</h5>
            </div>
            <div class="card-body p-4 bg-white">
                <?php if(isset($error_msg)): ?>
                    <div class="alert alert-danger small shadow-sm border-0"><?= $error_msg ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Username (Identitas Permanen)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-at text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-0 fw-bold" value="<?= htmlspecialchars($data['username']) ?>" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control bg-light border-0" value="<?= htmlspecialchars($data['nama']) ?>" required placeholder="Masukkan nama lengkap...">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Role Akses</label>
                            <select name="role" class="form-select bg-light border-0">
                                <option value="user" <?= $data['role'] == 'user' ? 'selected' : '' ?>>User Biasa</option>
                                <option value="admin" <?= $data['role'] == 'admin' ? 'selected' : '' ?>>Administrator</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Status Akun</label>
                            <select name="is_active" class="form-select bg-light border-0">
                                <option value="1" <?= $data['is_active'] == 1 ? 'selected' : '' ?>>Aktif</option>
                                <option value="0" <?= $data['is_active'] == 0 ? 'selected' : '' ?>>Pending / Non-Aktif</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4 opacity-10">

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-primary text-uppercase">Ganti Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-key text-muted"></i></span>
                            <input type="password" name="password" class="form-control bg-light border-0" placeholder="Kosongkan jika tidak ingin ganti password...">
                        </div>
                        <div class="form-text small text-muted mt-2">
                            <i class="fas fa-info-circle me-1"></i> Gunakan password yang kuat jika ingin melakukan reset.
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <a href="//index.php/user/index" class="text-muted small text-decoration-none fw-bold">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" name="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm transition-scale">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.bg-light { background-color: #f8f9fa !important; }
.fade-in { animation: fadeIn 0.6s ease-out; }
.transition-scale { transition: all 0.3s ease; }
.transition-scale:hover { transform: scale(1.03); }
@keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
</style>