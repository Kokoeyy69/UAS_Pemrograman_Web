<?php
/**
 * 1. PROSES LOGIKA LOGIN 
 * Diperbaiki agar tidak menyebabkan error "Invalid parameter number".
 */
if (isset($_POST['login'])) {
    $db = new Database();
    $input = trim($_POST['username']); 
    $pass  = $_POST['password'];

    try {
        // Menggunakan 2 parameter unik (:user dan :mail) agar PDO tidak bingung.
        $sql = "SELECT * FROM user WHERE (username = :user OR email = :mail) LIMIT 1";
        
        $params = [
            ':user' => $input, 
            ':mail' => $input 
        ];
        
        $stmt = $db->runQuery($sql, $params);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password'])) {
            // Cek apakah akun sudah aktif
            if (isset($user['is_active']) && $user['is_active'] == 0) {
                $message = "<div class='alert alert-warning'>Akun Anda belum aktif/verifikasi OTP!</div>";
            } else {
                // Set Session Sukses
                $_SESSION['is_login'] = true;
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['nama']     = $user['nama'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                header("Location: /uas_web/index.php/home/index");
                exit;
            }
        } else {
            $message = "<div class='alert alert-danger'>Login Gagal: User tidak ditemukan atau Password salah!</div>";
        }
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Terjadi gangguan pada sistem database.</div>";
    }
}

/**
 * 2. PENGECEKAN TAMPILAN
 * Kita tentukan apakah user harus melihat FORM LOGIN atau TABEL USER.
 */
$is_admin = (isset($_SESSION['is_login']) && $_SESSION['role'] === 'admin');

if ($is_admin) {
    // Jika Admin, ambil data dari database untuk tabel
    $db = new Database();
    $stmt = $db->runQuery("SELECT * FROM user ORDER BY role ASC, nama ASC");
    $data_user = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php if (!$is_admin): ?>
    <div class="d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="card border-0 shadow-lg rounded-4 p-4" style="width: 100%; max-width: 400px;">
            <div class="text-center mb-4">
                <i class="fas fa-user-shield fa-3x text-primary mb-3"></i>
                <h3 class="fw-bold">Admin Login</h3>
                <p class="text-muted small">Silakan masuk untuk mengelola pengguna</p>
            </div>

            <?php if (isset($message)) echo $message; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Username / Email</label>
                    <input type="text" name="username" class="form-control rounded-pill px-3" required placeholder="Masukkan username">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control rounded-pill px-3" required placeholder="******">
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm">
                    MASUK SEKARANG
                </button>
            </form>
        </div>
    </div>

<?php else: ?>
    <div class="fade-in px-3 mt-3">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-0 text-primary fw-bold"><i class="fas fa-users-cog me-2"></i>Kelola Pengguna</h1>
                <p class="text-muted small mb-0">Manajemen hak akses dan kontrol akun sistem.</p>
            </div>
            <a href="/uas_web/index.php/user/tambah" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold transition-scale">
                <i class="fas fa-user-plus me-2"></i> Tambah User Baru
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-body p-0">
                <div class="p-4 bg-white border-bottom">
                    <div class="input-group bg-light rounded-pill px-3 py-1" style="max-width: 350px;">
                        <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchUser" class="form-control bg-transparent border-0 shadow-none" placeholder="Cari nama atau username...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableUser">
                        <thead class="bg-light text-uppercase small fw-bold text-muted">
                            <tr>
                                <th class="ps-4 py-3">Informasi Pengguna</th>
                                <th class="py-3">Kontak & Status</th>
                                <th class="py-3">Role</th>
                                <th class="py-3 text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data_user as $row): ?>
                            <tr class="transition">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <?php 
                                            $foto = !empty($row['foto']) ? $row['foto'] : "https://ui-avatars.com/api/?name=" . urlencode($row['nama']) . "&background=4e73df&color=fff&size=128&bold=true";
                                        ?>
                                        <img src="<?= $foto ?>" class="rounded-circle me-3 border shadow-sm" width="48" height="48" style="object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama']) ?></div>
                                            <div class="small text-muted">@<?= htmlspecialchars($row['username']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-dark mb-1"><i class="far fa-envelope me-2 text-muted"></i><?= htmlspecialchars($row['email'] ?? '-') ?></div>
                                    <div class="d-flex align-items-center">
                                        <?php if(isset($row['is_active']) && $row['is_active'] == 1): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2">Pending</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?= $row['role'] == 'admin' ? 'bg-primary text-primary' : 'bg-secondary text-secondary' ?> bg-opacity-10 px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.7rem;">
                                        <?= $row['role'] ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if($row['username'] != $_SESSION['username']): ?>
                                        <a href="/uas_web/index.php/user/edit?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary border-0"><i class="fas fa-edit"></i></a>
                                        <a href="/uas_web/index.php/user/hapus?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus user?')"><i class="fas fa-trash"></i></a>
                                    <?php else: ?>
                                        <a href="/uas_web/index.php/user/profile" class="badge bg-light text-primary border px-3 py-2 rounded-pill text-decoration-none shadow-sm">Akun Saya</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
document.getElementById('searchUser').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#tableUser tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<style>
.transition { transition: all 0.2s ease; }
.transition-scale:hover { transform: translateY(-2px); }
.fade-in { animation: fadeIn 0.8s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>