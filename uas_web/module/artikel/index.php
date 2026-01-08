<?php
/**
 * 1. PROTEKSI AKSES & INISIALISASI
 * Memastikan sesi login aktif sebelum mengakses manajemen artikel.
 */
if (!isset($_SESSION['is_login'])) {
    header("Location: /uas_web/index.php/user/login");
    exit;
}

$db = new Database();
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin');

/**
 * 2. AMBIL DATA DENGAN runQuery (PDO Style)
 * Mengambil data artikel secara aman dari database.
 */
try {
    $stmt = $db->runQuery("SELECT * FROM artikel ORDER BY id DESC");
    $data_artikel = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Menyembunyikan detail teknis database untuk keamanan.
    $error_msg = "Gagal memuat data artikel.";
}
?>

<div class="fade-in px-3 mt-4 mb-5">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold">Manajemen Artikel</h1>
            <p class="text-muted small mb-0">Kelola konten berita dan publikasi sistem secara terpusat.</p>
        </div>
        <div class="d-flex gap-2 mt-3 mt-sm-0">
            <?php if ($isAdmin): ?>
                <a href="/uas_web/index.php/artikel/cetak" target="_blank" class="btn btn-outline-secondary shadow-sm rounded-pill px-4 fw-bold">
                    <i class="fas fa-print me-2"></i>Cetak Laporan
                </a>
                <a href="/uas_web/index.php/artikel/tambah" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Artikel
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(isset($error_msg)): ?>
        <div class="alert alert-danger rounded-4 shadow-sm"><?= $error_msg ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="p-4 bg-white border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <span class="small fw-bold text-muted text-uppercase" style="letter-spacing: 1px;">Daftar Konten</span>
                    </div>
                    <div class="col-md-4 ms-auto">
                        <div class="input-group bg-light rounded-pill px-3 py-1">
                            <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" id="searchInput" class="form-control bg-transparent border-0 shadow-none" placeholder="Cari judul artikel...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable">
                    <thead class="bg-light text-uppercase small fw-bold text-muted">
                        <tr>
                            <th class="ps-4 py-3">Judul Artikel</th>
                            <th class="py-3">Kutipan Isi</th>
                            <?php if ($isAdmin): ?>
                                <th class="py-3 text-end pe-4">Aksi Admin</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php if(!empty($data_artikel)): ?>
                            <?php foreach($data_artikel as $row): ?>
                            <tr class="transition">
                                <td class="ps-4 py-3 fw-bold text-dark">
                                    <?= htmlspecialchars($row['judul']) ?>
                                </td>
                                <td class="text-muted small">
                                    <?= htmlspecialchars(substr(strip_tags($row['isi']), 0, 100)) ?>...
                                </td>
                                <?php if ($isAdmin): ?>
                                <td class="pe-4 text-end">
                                    <div class="btn-group">
                                        <a href="/uas_web/index.php/artikel/ubah?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning rounded-circle me-2 border-0" title="Edit Artikel">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <a href="/uas_web/index.php/artikel/hapus?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger rounded-circle border-0" onclick="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')" title="Hapus Artikel">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= $isAdmin ? '3' : '2' ?>" class="text-center py-5 text-muted">
                                    <div class="opacity-25 mb-3">
                                        <i class="fas fa-folder-open fa-4x"></i>
                                    </div>
                                    <h6 class="fw-bold">Belum ada data artikel.</h6>
                                    <p class="small mb-0">Silakan tambahkan artikel baru melalui tombol di atas.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Logic Pencarian Real-time (Client Side)
     * Memungkinkan filter cepat pada daftar tabel tanpa reload.
     */
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#dataTable tbody tr');
        
        rows.forEach(row => {
            if(row.cells.length > 1) {
                let text = row.cells[0].textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            }
        });
    });
</script>

<style>
/* Efek Transisi dan Animasi */
.transition { transition: all 0.2s ease; }
.table-hover tbody tr:hover { background-color: rgba(78, 115, 223, 0.03); }
.fade-in { animation: fadeIn 0.6s ease-out; }
@keyframes fadeIn { 
    from { opacity: 0; transform: translateY(10px); } 
    to { opacity: 1; transform: translateY(0); } 
}
</style>