<?php
/**
 * 1. PROTEKSI AKSES & AMBIL DATA
 * Memastikan hanya Administrator yang diizinkan mencetak.
 */
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    die("<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
            <h2>Akses Ditolak</h2>
            <p>Hanya Administrator yang memiliki wewenang mencetak laporan.</p>
            <a href='/uas_web/index.php/home/index'>Kembali ke Dashboard</a>
         </div>");
}

$db = new Database();

try {
    /**
     * 2. AMBIL DATA DENGAN runQuery (PDO)
     * Mengambil data untuk laporan arsip.
     */
    $stmt = $db->runQuery("SELECT * FROM artikel ORDER BY tanggal DESC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Sembunyikan detail teknis database.
    die("<div style='text-align:center; padding:50px;'>Terjadi kesalahan pada instruksi database.</div>");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan_Artikel_<?= date('d-m-Y') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* CSS KHUSUS TAMPILAN CETAK (PRINT) */
        @media print {
            /* PENTING: Sembunyikan semua elemen sidebar dan navigasi website */
            .no-print, .sidebar, #sidebar-wrapper, .navbar, .btn, .logout-btn, footer { 
                display: none !important; 
            }
            
            /* Atur kertas agar putih bersih tanpa background abu-abu */
            body { 
                background: white !important; 
                padding: 0 !important; 
                margin: 0 !important;
                color: black !important;
            }
            
            /* Maksimalkan lebar kontainer di atas kertas */
            .container { 
                width: 100% !important; 
                max-width: 100% !important; 
                margin: 0 !important; 
            }
            
            @page { margin: 1.5cm; }
            
            .card { border: none !important; box-shadow: none !important; }
            .table { border: 1px solid #000 !important; }
            .table th { background-color: #f0f0f0 !important; color: black !important; }
        }

        body {
            font-family: "Times New Roman", Times, serif;
            background-color: #f4f7f6;
        }

        .kop-surat {
            border-bottom: 3px double #000;
            margin-bottom: 30px;
            padding-bottom: 10px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="text-center kop-surat">
        <h1 class="fw-bold mb-0">MODULAR SYSTEM APP</h1>
        <p class="mb-1">Sistem Manajemen Konten Berbasis Objek (OOP)</p>
        <p class="small text-muted mb-0">Jl. Raya Lab 11 No. 01, Bekasi Regency, West Java, Indonesia</p>
    </div>

    <div class="text-center mb-4">
        <h4 class="fw-bold text-uppercase">Laporan Rekapitulasi Data Artikel</h4>
        <p class="text-muted small">Periode Laporan: <?= date('d F Y') ?> | Dicetak oleh: <?= htmlspecialchars($_SESSION['nama']) ?></p>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light text-center small text-uppercase">
                    <tr>
                        <th width="50" class="py-3">No</th>
                        <th class="py-3">Judul Artikel</th>
                        <th width="180" class="py-3">Tanggal Terbit</th>
                        <th width="120" class="py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Tidak ada data artikel yang tersedia untuk dilaporkan.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($data as $row): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="ps-3 fw-bold">
                                <?= htmlspecialchars($row['judul']) ?>
                            </td>
                            <td class="text-center small">
                                <?= date('d M Y', strtotime($row['tanggal'] ?? 'now')) ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small">
                                    Published
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mt-5 pt-3">
        <div class="col-8"></div>
        <div class="col-4 text-center">
            <p class="mb-5">Bekasi, <?= date('d F Y') ?><br>Administrator System,</p>
            <br>
            <p class="fw-bold text-decoration-underline"><?= htmlspecialchars($_SESSION['nama']) ?></p>
        </div>
    </div>

    <div class="text-center mt-5 mb-5 no-print">
        <hr>
        <button onclick="window.print()" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
            <i class="fas fa-print me-2"></i> Cetak Dokumen / Simpan PDF
        </button>
        <div class="mt-3">
            <a href="/uas_web/index.php/artikel/index" class="text-decoration-none text-muted small">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Manajemen Artikel
            </a>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>