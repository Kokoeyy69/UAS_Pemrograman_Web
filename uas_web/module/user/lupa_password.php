<?php
/**
 * 1. PROTEKSI SESI & INISIALISASI
 */
if (isset($_SESSION['is_login'])) { 
    header("Location: /uas_web/index.php/home/index"); 
    exit; 
}

$message = "";

/**
 * 2. PROSES RESET PASSWORD (POST)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $pass_baru = $_POST['pass_baru'];
    $pass_konf = $_POST['pass_konf'];

    try {
        /**
         * 3. VERIFIKASI IDENTITAS (PDO)
         * Mencocokkan username dan email di tabel user.
         */
        $sql_cek = "SELECT id FROM user WHERE username = :u AND email = :e";
        $stmt_cek = $db->runQuery($sql_cek, [':u' => $username, ':e' => $email]);
        $user = $stmt_cek->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Cek kesesuaian password baru
            if ($pass_baru === $pass_konf) {
                /**
                 * 4. UPDATE PASSWORD DENGAN HASHING
                 */
                $pass_hash = password_hash($pass_baru, PASSWORD_DEFAULT);
                $sql_update = "UPDATE user SET password = :p WHERE id = :id";
                
                if ($db->runQuery($sql_update, [':p' => $pass_hash, ':id' => $user['id']])) {
                    echo "<script>
                            alert('Password berhasil direset! Silakan login kembali.'); 
                            window.location.href='/uas_web/index.php/user/index';
                          </script>";
                    exit;
                }
            } else {
                $message = "Konfirmasi password baru tidak cocok.";
            }
        } else {
            $message = "Username dan Email tidak ditemukan!";
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
    <title>Reset Password | Modular System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(120deg, #f6d365 0%, #fda085 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            margin: 0; padding: 15px; position: relative; overflow-x: hidden;
        }
        body::before {
            content: ""; position: fixed; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 60%);
            animation: rotateBG 20s linear infinite; z-index: -1; pointer-events: none;
        }
        @keyframes rotateBG { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .reset-card {
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(15px);
            border-radius: 20px; padding: 40px; width: 100%; max-width: 450px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            animation: slideIn 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        @keyframes slideIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .form-control { border-radius: 10px; padding: 12px 15px; border: 1px solid #e0e0e0; background: #f8f9fa; }
        .form-control:focus { background: #fff; border-color: #fda085; box-shadow: 0 0 0 4px rgba(253, 160, 133, 0.1); }
        .btn-reset {
            background: linear-gradient(to right, #ff9966, #ff5e62);
            border: none; border-radius: 50px; padding: 12px;
            font-weight: 600; width: 100%; color: white; margin-top: 10px; transition: 0.3s;
        }
        .btn-reset:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(255, 94, 98, 0.3); }
        .header-icon {
            width: 70px; height: 70px; background: #fff5f0; color: #ff5e62;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 30px; margin: 0 auto 20px;
        }
    </style>
</head>
<body>
    <div class="reset-card shadow-lg">
        <div class="text-center mb-4">
            <div class="header-icon shadow-sm"><i class="fa-solid fa-key"></i></div>
            <h3 class="fw-bold text-dark">Reset Password</h3>
            <p class="text-muted small">Verifikasi identitas Anda untuk melanjutkan</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-danger text-center small py-2 rounded-3 mb-3"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <h6 class="text-uppercase text-muted fw-bold small mb-3">1. Verifikasi Data</h6>
            <div class="mb-3"><input type="text" name="username" class="form-control" placeholder="Username" required></div>
            <div class="mb-4"><input type="email" name="email" class="form-control" placeholder="Email Terdaftar" required></div>
            
            <h6 class="text-uppercase text-muted fw-bold small mb-3">2. Password Baru</h6>
            <div class="mb-3"><input type="password" name="pass_baru" class="form-control" placeholder="Password Baru" required></div>
            <div class="mb-4"><input type="password" name="pass_konf" class="form-control" placeholder="Ulangi Password Baru" required></div>
            
            <button type="submit" class="btn btn-reset">SIMPAN PASSWORD BARU</button>
        </form>
        
        <div class="text-center mt-4">
            <a href="//index.php/user/index" class="text-decoration-none fw-bold small text-muted">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Login
            </a>
        </div>
    </div>
</body>
</html>