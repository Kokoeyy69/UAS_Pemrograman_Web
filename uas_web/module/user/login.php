<?php
/**
 * 1. PROTEKSI SESI
 * Jika sudah login, langsung lempar ke halaman home.
 */
if (isset($_SESSION['is_login'])) {
    header("Location: /uas_web/index.php/home/index");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $login_input = trim($_POST['username']); // Data dari input form
    $password = $_POST['password'];

    try {
        /**
         * 2. SOLUSI ERROR: Invalid parameter number
         * Gunakan dua nama parameter unik (:user dan :mail) agar sistem tidak bingung.
         */
        $sql = "SELECT * FROM user WHERE (username = :user OR email = :mail) LIMIT 1";
        
        // Kita isi :user dengan $login_input DAN :mail juga dengan $login_input
        $stmt = $db->runQuery($sql, [
            ':user' => $login_input, 
            ':mail' => $login_input
        ]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        /**
         * 3. VERIFIKASI PASSWORD HASH
         */
        if ($user && password_verify($password, $user['password'])) {
            
            // Cek Status Aktif (Jika ada kolom is_active)
            if (isset($user['is_active']) && $user['is_active'] == 0) {
                $message = "Akun belum aktif! <br> <a href='/uas_web/index.php/user/verifikasi' class='fw-bold text-white text-decoration-underline'>Klik disini untuk verifikasi OTP</a>";
            } else {
                // LOGIN SUKSES - Set Sesi
                $_SESSION['is_login'] = true;
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama']     = $user['nama'];
                $_SESSION['role']     = $user['role'];
                
                header("Location: /uas_web/index.php/home/index");
                exit;
            }

        } else {
            $message = "Username/Email atau Password salah!";
        }
    } catch (Exception $e) {
        $message = "Terjadi gangguan sistem.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Modular App</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 15px; margin: 0; overflow: hidden;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; }
        }
        .login-card {
            background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);
            border-radius: 25px; padding: 40px; width: 100%; max-width: 420px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2); animation: fadeInUp 0.8s ease-out;
        }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .form-floating .form-control { border-radius: 12px; border: 1px solid #ddd; }
        .btn-login {
            background: linear-gradient(to right, #e73c7e, #ee7752); border: none;
            border-radius: 50px; padding: 12px; font-weight: 600; color: white; transition: 0.3s;
        }
        .btn-login:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(231, 60, 126, 0.3); color: white; }
        .password-container { position: relative; }
        .toggle-password { position: absolute; right: 15px; top: 18px; cursor: pointer; color: #aaa; z-index: 10; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="bg-white d-inline-block p-3 rounded-circle shadow-sm mb-3">
                <i class="fa-solid fa-rocket fa-2x" style="color: #e73c7e;"></i>
            </div>
            <h3 class="fw-bold text-dark">Welcome Back</h3>
            <p class="text-muted small">Silakan login untuk mengakses dashboard</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-danger border-0 shadow-sm small py-2 mb-3 rounded-3">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-floating mb-3">
                <input type="text" name="username" class="form-control" id="userInput" placeholder="Username" required>
                <label for="userInput"><i class="fa-solid fa-user me-2"></i>Username / Email</label>
            </div>
            <div class="form-floating mb-3 password-container">
                <input type="password" name="password" class="form-control" id="passInput" placeholder="Password" required>
                <label for="passInput"><i class="fa-solid fa-lock me-2"></i>Password</label>
                <i class="fa-solid fa-eye toggle-password" onclick="togglePassword()" id="toggleIcon"></i>
            </div>
            <div class="text-end mb-4">
                <a href="/uas_web/index.php/user/lupa_password" class="text-decoration-none small fw-bold" style="color: #e73c7e;">Lupa Password?</a>
            </div>
            <button type="submit" class="btn btn-login w-100">MASUK SEKARANG</button>
        </form>
        <div class="text-center mt-4">
            <span class="text-muted small">Belum memiliki akun?</span>
            <a href="/uas_web/index.php/user/daftar" class="fw-bold text-decoration-none ms-1" style="color: #e73c7e;">Daftar Disini</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            var input = document.getElementById("passInput"); var icon = document.getElementById("toggleIcon");
            if (input.type === "password") { 
                input.type = "text"; icon.classList.replace("fa-eye", "fa-eye-slash"); 
            } else { 
                input.type = "password"; icon.classList.replace("fa-eye-slash", "fa-eye"); 
            }
        }
    </script>
</body>
</html>