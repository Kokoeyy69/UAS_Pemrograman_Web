<?php
/**
 * 1. INISIALISASI & LOGIKA PAGINATION
 * Halaman publik untuk menampilkan daftar berita dengan pembagian halaman.
 */
$db = new Database();
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

// --- KONFIGURASI PAGINATION (WAJIB ADA) ---
$batas = 6; // Jumlah artikel per halaman
$halaman = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

try {
    // A. HITUNG TOTAL DATA (FIXED: Pakai :p1 dan :p2)
    if (!empty($search)) {
        // Kita pakai 2 parameter berbeda (:p1 dan :p2) untuk mencegah error "Invalid parameter number"
        $sql_count = "SELECT count(id) as jumlah FROM artikel WHERE judul LIKE :p1 OR isi LIKE :p2";
        $stmt_count = $db->runQuery($sql_count, [
            ':p1' => "%$search%", 
            ':p2' => "%$search%"
        ]);
    } else {
        $sql_count = "SELECT count(id) as jumlah FROM artikel";
        $stmt_count = $db->runQuery($sql_count);
    }
    $jumlah_data = $stmt_count->fetch(PDO::FETCH_ASSOC)['jumlah'];
    $total_halaman = ceil($jumlah_data / $batas);

    // B. AMBIL DATA DENGAN LIMIT (FIXED: Pakai :p1 dan :p2)
    if (!empty($search)) {
        // Query pencarian juga diperbaiki parameternya
        $sql = "SELECT * FROM artikel WHERE judul LIKE :p1 OR isi LIKE :p2 ORDER BY tanggal DESC LIMIT $halaman_awal, $batas";
        $stmt = $db->runQuery($sql, [
            ':p1' => "%$search%", 
            ':p2' => "%$search%"
        ]);
    } else {
        $sql = "SELECT * FROM artikel ORDER BY tanggal DESC LIMIT $halaman_awal, $batas";
        $stmt = $db->runQuery($sql);
    }
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error_msg = "Gagal memuat data berita.";
}
?>

<div class="fade-in px-3 mt-4 mb-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-primary animate-up"><i class="fas fa-newspaper me-2"></i>Berita & Artikel</h1>
        <p class="text-muted">Informasi terbaru dan artikel menarik yang dioptimalkan untuk Anda.</p>
        
        <div class="row justify-content-center mt-4">
            <div class="col-md-7 col-lg-6">
                <form action="" method="GET">
                    <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white border p-1">
                        <span class="input-group-text bg-transparent border-0 ps-3">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="q" class="form-control border-0 shadow-none px-2" 
                               placeholder="Cari berita atau artikel..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-primary rounded-pill px-4 fw-bold" type="submit">Cari</button>
                    </div>
                    <?php if(!empty($search)): ?>
                        <div class="mt-2 small text-muted">
                            Menampilkan hasil untuk: <strong>"<?= htmlspecialchars($search) ?>"</strong> 
                            | <a href="/uas_web/index.php/artikel/list" class="text-danger fw-bold">Bersihkan</a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php if(empty($articles)): ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted opacity-50">
                    <i class="fas fa-search-minus fa-4x mb-3"></i>
                    <h4><?= !empty($search) ? 'Berita tidak ditemukan.' : 'Belum ada artikel dipublikasikan.' ?></h4>
                    <p>Coba gunakan kata kunci lain atau periksa kembali nanti.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach($articles as $row): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4 hover-top transition overflow-hidden">
                        <div class="card-img-top position-relative" style="height: 200px; background: #f8f9fc;">
                            <?php if(!empty($row['gambar'])): ?>
                                <img src="<?= htmlspecialchars($row['gambar']) ?>" class="w-100 h-100 object-fit-cover img-hover" alt="Thumbnail">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted bg-light">
                                    <i class="fas fa-image fa-3x opacity-25"></i>
                                </div>
                            <?php endif; ?>
                            <span class="position-absolute top-0 end-0 m-3 badge bg-primary bg-opacity-75 rounded-pill shadow-sm small">Baru</span>
                        </div>
                        
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="small text-muted mb-2">
                                <i class="far fa-calendar-alt me-1 text-primary"></i> 
                                <?= isset($row['tanggal']) ? date('d M Y', strtotime($row['tanggal'])) : date('d M Y') ?>
                            </div>
                            
                            <h5 class="card-title fw-bold mb-3 text-dark text-truncate-2">
                                <?= htmlspecialchars($row['judul']) ?>
                            </h5>
                            
                            <p class="card-text text-muted small flex-grow-1">
                                <?= htmlspecialchars(substr(strip_tags($row['isi']), 0, 100)) ?>...
                            </p>
                            
                            <a href="/uas_web/index.php/artikel/baca?slug=<?= $row['slug'] ?>" class="btn btn-primary rounded-pill w-100 mt-4 fw-bold shadow-sm py-2">
                                Baca Selengkapnya <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if($total_halaman > 1): ?>
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($halaman <= 1) ? 'disabled' : '' ?>">
                <a class="page-link rounded-pill px-3 me-2 border-0 shadow-sm" href="?q=<?= $search ?>&page=<?= $halaman - 1 ?>">
                    <i class="fas fa-chevron-left me-1"></i> Prev
                </a>
            </li>

            <?php for($x = 1; $x <= $total_halaman; $x++): ?>
                <li class="page-item <?= ($halaman == $x) ? 'active' : '' ?>">
                    <a class="page-link rounded-circle mx-1 d-flex align-items-center justify-content-center border-0 shadow-sm fw-bold" 
                       style="width: 40px; height: 40px;" 
                       href="?q=<?= $search ?>&page=<?= $x ?>"><?= $x ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : '' ?>">
                <a class="page-link rounded-pill px-3 ms-2 border-0 shadow-sm" href="?q=<?= $search ?>&page=<?= $halaman + 1 ?>">
                    Next <i class="fas fa-chevron-right ms-1"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<style>
/* CSS Tambahan */
.hover-top { transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); }
.hover-top:hover { 
    transform: translateY(-8px); 
    box-shadow: 0 1.5rem 3rem rgba(78, 115, 223, 0.12)!important; 
}
.img-hover { transition: transform 0.6s ease; }
.hover-top:hover .img-hover { transform: scale(1.08); }
.object-fit-cover { object-fit: cover; }
.text-truncate-2 {
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; 
    overflow: hidden; min-height: 3rem; line-height: 1.5;
}
.page-link { color: #4e73df; }
.page-item.active .page-link { background-color: #4e73df; border-color: #4e73df; color: white; }
.animate-up { animation: fadeInUp 0.8s ease-out; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.fade-in { animation: fadeIn 0.6s ease-in; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>