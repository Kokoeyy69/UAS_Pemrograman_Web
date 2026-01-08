<?php
/**
 * 1. PROTEKSI SESI
 * Jika sudah login, tidak perlu memverifikasi lagi.
 */
if (isset($_SESSION['is_login'])) { 
    header("Location: /uas_web/index.php/home/index"); 
    exit; 
}

$message = "";

/**
 * 2. PROSES VERIFIKASI (POST)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $otp_input = trim($_POST['otp']);
    
    try {
        /**
         * 3. CEK OTP MENGGUNAKAN PDO
         * Mencari user yang memiliki kode OTP cocok dan belum aktif.
         */
        $sql = "SELECT id, nama FROM user WHERE otp = :otp AND is_active = 0";
        $stmt = $db->runQuery($sql, [':otp' => $otp_input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $id_user = $user['id'];
            
            /**
             * 4. AKTIFKANKAN AKUN
             * Mengubah is_active menjadi 1 dan menghapus OTP agar tidak bisa dipakai ulang.
             */
            $update_sql = "UPDATE user SET is_active = 1, otp = NULL WHERE id = :id";
            if ($db->runQuery($update_sql, [':id' => $id_user])) {
                
                echo "<script>
                        alert('Verifikasi Berhasil! Akun " . htmlspecialchars($user['nama']) . " sudah aktif. Silakan Login.');
                        window.location.href='/uas_web/index.php/user/index';
                      </script>";
                exit;
            }
        } else {
            $message = "Kode OTP salah atau sudah tidak berlaku!";
        }
    } catch (Exception $e) {
        $message = "Terjadi kesalahan sistem: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Akun | Modular System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .card-otp {
            background: white; padding: 40px; border-radius: 25px; width: 100%; max-width: 420px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); text-align: center;
            animation: fadeIn 0.6s ease-out;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .otp-input {
            letter-spacing: 10px; text-align: center; font-size: 28px; font-weight: bold;
            background: #f8f9fa; border: 2px solid #e9ecef; border-radius: 15px; padding: 15px;
        }
        .otp-input:focus { background: #fff; border-color: #84fab0; box-shadow: none; }
        .btn-verify {
            background: #20c997; border: none; border-radius: 50px; padding: 12px;
            font-weight: 600; color: white; transition: 0.3s;
        }
        .btn-verify:hover { background: #198754; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="card-otp">
        <div class="mb-4 text-success">
            <i class="fas fa-shield-alt fa-3x"></i>
        </div>
        <h3 class="fw-bold mb-2">Verifikasi OTP</h3>
        <p class="text-muted small mb-4">Masukkan 6 digit kode verifikasi yang muncul pada notifikasi pendaftaran Anda.</p>
        
        <?php if ($message): ?>
            <div class="alert alert-danger py-2 small border-0 rounded-3 mb-4"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <input type="text" name="otp" class="form-control otp-input" maxlength="6" placeholder="000000" pattern="\d{6}" title="Masukkan 6 digit angka" required autofocus>
            </div>
            <button type="submit" class="btn btn-verify w-100 shadow-sm">
                <i class="fas fa-check-circle me-2"></i> Verifikasi Akun
            </button>
        </form>

        <div class="mt-4">
            <small class="text-muted">Masalah saat verifikasi?</small>
            <a href="/uas_web/index.php/user/daftar" class="text-decoration-none fw-bold text-success ms-1">Daftar Ulang</a>
        </div>
    </div>
</body>
</html>