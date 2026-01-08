<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modular Dashboard System</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-blue: #4e73df;
            --sidebar-width: 260px;
            --bg-light: #f8f9fc;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: #5a5c69;
            margin: 0; 
            overflow-x: hidden;
        }

        .wrapper { 
            display: flex; 
            min-height: 100vh; 
            width: 100%; 
            position: relative;
        }
        
        /* SIDEBAR DESKTOP */
        .sidebar {
            width: var(--sidebar-width);
            transition: all 0.3s ease;
            z-index: 1050;
            background-color: white;
            border-right: 1px solid #e3e6f0;
        }

        /* KONTEN DESKTOP */
        .content-wrapper {
            flex: 1;
            padding: 2rem;
            min-height: 100vh;
            background-color: var(--bg-light);
            transition: all 0.3s ease;
            width: calc(100% - var(--sidebar-width)); 
        }

        /* --- [CSS MOBILE] --- */
        @media (max-width: 991px) {
            /* 1. Sidebar Sembunyi (Diatur lebih detail di sidebar.php) */
            .sidebar {
                position: fixed !important;
                left: -280px !important;
                top: 0; bottom: 0;
                height: 100vh;
                width: 260px !important;
                box-shadow: none;
            }
            
            /* 2. Konten Full Layar & Turun ke Bawah */
            .content-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 1rem !important;
                /* PENTING: Samakan dengan sidebar.php biar sinkron */
                padding-top: 100px !important; 
            }
        }

        .topbar { height: 70px; background: #fff; display: flex; align-items: center; padding: 0 1.5rem; margin-bottom: 2rem; border-radius: 15px; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05); }
        .card-modern { border: none; border-radius: 15px; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); background: white; transition: transform 0.2s; }
        .fade-in { animation: fadeIn 0.6s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <div class="wrapper">
        <?php include "template/sidebar.php"; ?>
        
        <div class="content-wrapper fade-in">

            <div class="topbar d-none d-md-flex justify-content-between align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item small"><a href="#" class="text-decoration-none text-muted">Aplikasi</a></li>
                        <li class="breadcrumb-item small active text-primary fw-bold" aria-current="page"><?= ucfirst($mod) ?></li>
                    </ol>
                </nav>
                <div class="user-info small d-flex align-items-center">
                    <span class="me-2 text-muted italic">Login sebagai:</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 rounded-pill"><?= $_SESSION['nama'] ?? 'User' ?></span>
                </div>
            </div>