<?php
/**
 * 1. PROTEKSI AKSES & INISIALISASI
 * Memastikan sesi login aktif untuk mengakses data privat.
 */
if (!isset($_SESSION['is_login'])) {
    header("Location: /uas_web/index.php/user/login");
    exit;
}

$db = new Database();
$username_session = $_SESSION['username'];
$message = "";

// Folder target upload foto profil
$target_dir = __DIR__ . "/../../assets/img/user/";

/**
 * 2. PROSES HAPUS FOTO PROFIL
 */
if (isset($_POST['hapus_foto'])) {
    $stmt = $db->runQuery("SELECT foto FROM user WHERE username = :u", [':u' => $username_session]);
    $cek_foto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!empty($cek_foto['foto'])) {
        $file_path = __DIR__ . "/../../" . str_replace("//", "", $cek_foto['foto']);
        
        if (file_exists($file_path)) {
            unlink($file_path); // Hapus file fisik
        }
        
        $db->runQuery("UPDATE user SET foto = NULL WHERE username = :u", [':u' => $username_session]);
        
        $message = "<div class='alert alert-warning alert-dismissible fade show rounded-4 shadow-sm'>
                        <i class='fas fa-trash-alt me-2'></i>Foto profil berhasil dihapus.
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
    }
}

/**
 * 3. PROSES UPDATE PROFIL & UPLOAD FOTO BARU
 */
if (isset($_POST['update_profile'])) {
    $nama  = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $hp    = trim($_POST['no_hp']);
    
    // Ambil data lama untuk pengecekan file
    $stmt_old = $db->runQuery("SELECT foto FROM user WHERE username = :u", [':u' => $username_session]);
    $old_data = $stmt_old->fetch(PDO::FETCH_ASSOC);
    $foto_value = $old_data['foto'];

    // Logika upload foto profil baru
    if (!empty($_FILES['foto']['name'])) {
        $ext = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($ext, $allowed) && $_FILES["foto"]["size"] <= 2000000) {
            // Hapus foto lama jika ada
            if (!empty($old_data['foto'])) {
                $old_path = __DIR__ . "/../../" . str_replace("//", "", $old_data['foto']);
                if (file_exists($old_path)) unlink($old_path);
            }

            if (!file_exists($target_dir)) mkdir($target_dir, 0755, true);

            $new_name = "user_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_dir . $new_name)) {
                $foto_value = "//assets/img/user/" . $new_name;
            }
        } else {
            $message = "<div class='alert alert-danger rounded-4 shadow-sm'>Format tidak didukung atau file > 2MB.</div>";
        }
    }

    if (empty($message)) {
        $sql = "UPDATE user SET nama = :nama, email = :email, no_hp = :hp, foto = :foto WHERE username = :u";
        $params = [':nama' => $nama, ':email' => $email, ':hp' => $hp, ':foto' => $foto_value, ':u' => $username_session];
        
        if ($db->runQuery($sql, $params)) {
            $_SESSION['nama'] = $nama; // Update session real-time
            $message = "<div class='alert alert-success alert-dismissible fade show rounded-4 shadow-sm'>
                            <i class='fas fa-check-circle me-2'></i>Profil diperbarui!
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>";
        }
    }
}

/**
 * 4. PROSES GANTI PASSWORD (SECURE HASHING)
 * Menggunakan standar industri password_hash.
 */
if (isset($_POST['update_password'])) {
    $lama = $_POST['lama'];
    $baru = $_POST['baru'];
    $konf = $_POST['konf'];

    $stmt_p = $db->runQuery("SELECT password FROM user WHERE username = :u", [':u' => $username_session]);
    $user_p = $stmt_p->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($lama, $user_p['password'])) {
        $message = "<div class='alert alert-danger rounded-4 shadow-sm'>Password lama salah!</div>";
    } elseif ($baru !== $konf) {
        $message = "<div class='alert alert-danger rounded-4 shadow-sm'>Konfirmasi password tidak cocok!</div>";
    } else {
        $hash = password_hash($baru, PASSWORD_DEFAULT);
        $db->runQuery("UPDATE user SET password = :p WHERE username = :u", [':p' => $hash, ':u' => $username_session]);
        $message = "<div class='alert alert-success rounded-4 shadow-sm'>Password berhasil diganti!</div>";
    }
}

/**
 * 5. AMBIL DATA TERBARU
 */
$stmt_final = $db->runQuery("SELECT * FROM user WHERE username = :u", [':u' => $username_session]);
$data = $stmt_final->fetch(PDO::FETCH_ASSOC);

$foto_profil = !empty($data['foto']) ? $data['foto'] : "https://ui-avatars.com/api/?name=" . urlencode($data['nama']) . "&background=4e73df&color=fff&size=128";
?>

<div class="fade-in px-3 mt-4 mb-5">
    <div class="row">
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div style="height: 120px; background: linear-gradient(120deg, #4e73df, #224abe);"></div>
                <div class="card-body text-center position-relative" style="margin-top: -70px;">
                    <div class="position-relative d-inline-block">
                        <img src="<?= $foto_profil ?>" class="rounded-circle border border-4 border-white shadow-sm mb-3" width="130" height="130" style="object-fit: cover;">
                        <?php if (!empty($data['foto'])): ?>
                            <form method="POST" onsubmit="return confirm('Hapus foto profil?');">
                                <button type="submit" name="hapus_foto" class="btn btn-danger btn-sm position-absolute bottom-0 end-0 rounded-circle shadow-sm" style="width: 35px; height: 35px; border: 3px solid white;"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($data['nama']) ?></h4>
                    <p class="text-muted mb-2">@<?= htmlspecialchars($data['username']) ?></p>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill text-uppercase small fw-bold"><i class="fas fa-shield-alt me-1"></i> <?= $data['role'] ?></span>
                    <hr class="my-4 opacity-10">
                    <div class="text-start px-3 small">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light text-primary me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;"><i class="fas fa-envelope"></i></div>
                            <div><span class="text-muted d-block small">Email</span><span class="fw-bold"><?= htmlspecialchars($data['email'] ?? '-') ?></span></div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-light text-success me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;"><i class="fas fa-phone-alt"></i></div>
                            <div><span class="text-muted d-block small">No. HP</span><span class="fw-bold"><?= htmlspecialchars($data['no_hp'] ?? '-') ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <?= $message ?>
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 p-4 pb-0">
                    <ul class="nav nav-pills" id="profileTabs" role="tablist">
                        <li class="nav-item"><button class="nav-link active fw-bold px-4 rounded-pill" data-bs-toggle="tab" data-bs-target="#edit" type="button"><i class="fas fa-user-edit me-2"></i>Profil</button></li>
                        <li class="nav-item ms-2"><button class="nav-link fw-bold px-4 rounded-pill" data-bs-toggle="tab" data-bs-target="#password" type="button"><i class="fas fa-key me-2"></i>Keamanan</button></li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="edit">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Update Foto</label>
                                    <input type="file" name="foto" class="form-control bg-light border-0 px-3">
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label fw-bold small">USERNAME</label><input type="text" class="form-control bg-light border-0" value="<?= $data['username'] ?>" readonly></div>
                                    <div class="col-md-6"><label class="form-label fw-bold small">NAMA LENGKAP</label><input type="text" name="nama" class="form-control bg-light border-0" value="<?= htmlspecialchars($data['nama']) ?>" required></div>
                                    <div class="col-md-6"><label class="form-label fw-bold small">EMAIL</label><input type="email" name="email" class="form-control bg-light border-0" value="<?= htmlspecialchars($data['email'] ?? '') ?>"></div>
                                    <div class="col-md-6"><label class="form-label fw-bold small">NO. HP</label><input type="text" name="no_hp" class="form-control bg-light border-0" value="<?= htmlspecialchars($data['no_hp'] ?? '') ?>"></div>
                                </div>
                                <div class="text-end mt-4"><button type="submit" name="update_profile" class="btn btn-primary px-5 rounded-pill fw-bold py-2 shadow-sm">Simpan</button></div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="password">
                            <form method="POST">
                                <div class="mb-4"><label class="form-label fw-bold small">PASSWORD LAMA</label><input type="password" name="lama" class="form-control bg-light border-0" required></div>
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label fw-bold small">PASSWORD BARU</label><input type="password" name="baru" class="form-control bg-light border-0" required></div>
                                    <div class="col-md-6"><label class="form-label fw-bold small">KONFIRMASI</label><input type="password" name="konf" class="form-control bg-light border-0" required></div>
                                </div>
                                <div class="text-end mt-4"><button type="submit" name="update_password" class="btn btn-danger px-5 rounded-pill fw-bold py-2 shadow-sm">Update Password</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>