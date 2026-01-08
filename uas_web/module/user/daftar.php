<?php
/**
 * 1. INISIALISASI & PROTEKSI
 */
if (isset($_SESSION['is_login'])) { 
    header("Location: /uas_web/index.php/home/index"); 
    exit; 
}

$db = new Database();
$message = "";

/**
 * 2. LOGIKA CAPTCHA MATEMATIKA (Anti-Bot)
 */
if (!isset($_SESSION['captcha_result'])) {
    $num1 = rand(1, 9);
    $num2 = rand(1, 9);
    $_SESSION['captcha_result'] = $num1 + $num2;
    $_SESSION['captcha_text'] = "$num1 + $num2 = ?";
}

/**
 * 3. PROSES REGISTRASI (POST)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $captcha_input = $_POST['captcha'];
    $role     = 'user';

    // A. Validasi CAPTCHA
    if ($captcha_input != $_SESSION['captcha_result']) {
        $message = "Jawaban Captcha salah! Silakan hitung ulang.";
    } else {
        try {
            // B. Cek Duplikasi User (PDO Style)
            $sql_cek = "SELECT id FROM user WHERE username = :u OR email = :e";
            $stmt_cek = $db->runQuery($sql_cek, [':u' => $username, ':e' => $email]);
            
            if ($stmt_cek->rowCount() > 0) {
                $message = "Username atau Email sudah terdaftar!";
            } else {
                // C. Proses Enkripsi & OTP
                $pass_hash = password_hash($password, PASSWORD_DEFAULT);
                $otp = rand(100000, 999999); 
                
                // D. Insert ke Database (is_active = 0 menunggu verifikasi)
                $sql_ins = "INSERT INTO user (username, password, nama, email, role, is_active, otp) 
                            VALUES (:u, :p, :n, :e, :r, 0, :otp)";
                
                $params = [
                    ':u' => $username, ':p' => $pass_hash, ':n' => $nama, 
                    ':e' => $email, ':r' => $role, ':otp' => $otp
                ];
                
                if ($db->runQuery($sql_ins, $params)) {
                    // Reset Captcha setelah berhasil
                    unset($_SESSION['captcha_result']);
                    unset($_SESSION['captcha_text']);
                    
                    // SIMULASI KIRIM EMAIL VIA ALERT
                    echo "<script>
                            alert('Registrasi Berhasil! \\n\\n[SIMULASI EMAIL] \\nKode OTP Anda adalah: $otp \\n\\nSilakan gunakan kode ini untuk verifikasi.'); 
                            window.location.href='/uas_web/index.php/user/verifikasi';
                          </script>";
                    exit;
                }
            }
        } catch (Exception $e) {
            $message = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }
    // Refresh captcha jika gagal
    $num1 = rand(1, 9); $num2 = rand(1, 9);
    $_SESSION['captcha_result'] = $num1 + $num2;
    $_SESSION['captcha_text'] = "$num1 + $num2 = ?";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | Modular System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #66a6ff 0%, #89f7fe 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .register-card { background: #fff; border-radius: 20px; padding: 40px; width: 100%; max-width: 450px; shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .form-control { border-radius: 10px; background: #f8f9fa; border: 1px solid #e9ecef; padding: 12px; }
        .captcha-box { background: #eef2f7; border: 2px dashed #66a6ff; border-radius: 10px; font-weight: bold; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; height: 100%; }
        .btn-register { background: #4e73df; border: none; border-radius: 50px; padding: 12px; font-weight: bold; color: #fff; transition: 0.3s; }
        .btn-register:hover { background: #224abe; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="register-card shadow-lg">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-primary">Daftar Akun</h3>
            <p class="text-muted small">Bergabung dengan Modular System</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-warning small py-2 text-center"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3"><input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required></div>
            <div class="mb-3"><input type="text" name="username" class="form-control" placeholder="Username" required></div>
            <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
            <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
            
            <div class="mb-4">
                <label class="small fw-bold text-muted mb-2">Verifikasi Keamanan (Anti-Bot)</label>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="captcha-box"><?= $_SESSION['captcha_text'] ?></div>
                    </div>
                    <div class="col-6">
                        <input type="number" name="captcha" class="form-control h-100 text-center fw-bold" placeholder="Hasil?" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-register w-100">DAFTAR & VERIFIKASI</button>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted">Sudah punya akun?</small>
            <a href="//index.php/user/index" class="fw-bold text-decoration-none ms-1">Login</a>
        </div>
    </div>
</body>
</html>