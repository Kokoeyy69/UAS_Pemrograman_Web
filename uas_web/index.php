<?php
ob_start();
session_start();

include "config.php";
require_once "class/Database.php";
require_once "class/Form.php";

$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/home/index';
$segments = explode('/', trim($path, '/'));
$mod = isset($segments[0]) ? $segments[0] : 'home';
$page = isset($segments[1]) ? $segments[1] : 'index';

$public_pages = ['login', 'daftar', 'lupa_password', 'verifikasi'];
if ($mod == 'user' && in_array($page, $public_pages)) {
    $is_public = true;
} else {
    $is_public = false;
    if (!isset($_SESSION['is_login'])) {
        header('Location: /uas_web/index.php/user/login');
        exit();
    }
}

$file = "module/{$mod}/{$page}.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modular System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* RESET TOTAL (Hapus Margin/Padding Bawaan Browser) */
        /* Ini akan menghilangkan garis merah/pink di pinggir layar */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: sans-serif;
            width: 100%;
        }

        /* KHUSUS LOGIN: Animasi Gradient */
        <?php if ($is_public): ?>
        body {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden; /* Hilangkan scroll saat login */
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .login-wrapper {
            width: 100%;
            max-width: 450px;
            padding: 15px;
        }
        <?php else: ?>
        /* KHUSUS DASHBOARD */
        body { background-color: #f8f9fa; }
        <?php endif; ?>
    </style>
</head>
<body>

<?php if ($is_public): ?>
    
    <div class="login-wrapper">
        <?php if (file_exists($file)) include $file; ?>
    </div>

<?php else: ?>

    <div class="container-fluid p-0 h-100">
        <div class="row g-0 h-100 flex-nowrap">
            
            <div class="col-auto px-0 h-100">
                <?php include "template/sidebar.php"; ?>
            </div>
            
            <div class="col p-4 h-100 bg-light" style="overflow-y: auto;">
                <?php 
                    if (file_exists($file)) {
                        include $file;
                    } else {
                        echo "<div class='alert alert-danger'>Halaman tidak ditemukan.</div>";
                    }
                ?>
                <footer class="mt-5 text-muted small text-center py-3">
                    &copy; <?= date('Y') ?> Modular System
                </footer>
            </div>
            
        </div>
    </div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>