<?php
/**
 * 1. INISIALISASI & AMBIL SLUG
 * Halaman ini sekarang menggunakan Slug untuk mendukung SEO.
 */
$slug = isset($_GET['slug']) ? $_GET['slug'] : null;
$db = new Database();

// Proteksi jika slug tidak ada di URL
if (!$slug) {
    header("Location: /uas_web/index.php/artikel/list");
    exit;
}

/**
 * 2. AMBIL DATA (PDO)
 * Mencari artikel berdasarkan kolom slug yang unik.
 */
try {
    $sql = "SELECT * FROM artikel WHERE slug = :slug";
    $stmt = $db->runQuery($sql, [':slug' => $slug]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Penanganan jika artikel tidak ditemukan
    if (!$data) {
        echo "<div class='container py-5 text-center fade-in'>
                <div class='alert alert-danger border-0 shadow-sm p-5 rounded-4'>
                    <i class='fas fa-search fa-3x mb-3 text-danger opacity-50'></i>
                    <h3 class='fw-bold'>Artikel Tidak Ditemukan</h3>
                    <p class='text-muted'>Konten yang Anda cari mungkin telah dihapus atau URL salah.</p>
                    <a href='/uas_web/index.php/artikel/list' class='btn btn-primary rounded-pill px-4 mt-3'>Kembali ke Berita</a>
                </div>
              </div>";
        exit;
    }
} catch (Exception $e) {
    // Sembunyikan detail teknis untuk keamanan publik.
    die("<div class='container py-5 text-center'>Terjadi kesalahan pada instruksi database.</div>");
}
?>

<div class="fade-in px-3 mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <a href="/uas_web/index.php/artikel/list" class="btn btn-light rounded-pill shadow-sm mb-4 border transition-scale fw-bold text-muted px-4">
                <i class="fas fa-arrow-left me-2 text-primary"></i> Kembali ke Berita
            </a>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                
                <?php if(!empty($data['gambar'])): ?>
                    <div class="position-relative">
                        <img src="<?= htmlspecialchars($data['gambar']) ?>" class="w-100 object-fit-cover shadow-sm" style="height: 450px;" alt="Banner Artikel">
                        <div class="position-absolute bottom-0 start-0 w-100 p-4 bg-gradient-dark">
                            <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm">
                                <i class="far fa-calendar-alt me-1"></i> 
                                <?= isset($data['tanggal']) ? date('d F Y', strtotime($data['tanggal'])) : date('d F Y') ?>
                            </span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="w-100 bg-light d-flex flex-column align-items-center justify-content-center border-bottom" style="height: 250px;">
                        <i class="fas fa-image fa-4x text-muted opacity-25 mb-3"></i>
                        <span class="text-muted small">No Featured Image</span>
                    </div>
                <?php endif; ?>
                
                <div class="card-body p-4 p-md-5">
                    <div class="mb-4">
                        <h1 class="fw-bold text-dark display-5 mb-3"><?= htmlspecialchars($data['judul']) ?></h1>
                        <div class="d-flex align-items-center text-muted small border-start border-primary border-4 ps-3">
                            <div class="me-3"><i class="fas fa-user-circle me-1 text-primary"></i> Administrator</div>
                            <div class="me-3"><i class="fas fa-eye me-1"></i> Dibaca secara publik</div>
                        </div>
                    </div>
                    
                    <hr class="my-4 opacity-10">
                    
                    <div class="article-content text-dark lh-lg fs-5">
                        <?= $data['isi'] ?> 
                    </div>

                    <hr class="my-5 opacity-10">

                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i> Sumber: Modular Content System
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm rounded-circle shadow-sm"><i class="fab fa-facebook-f"></i></button>
                            <button class="btn btn-outline-secondary btn-sm rounded-circle shadow-sm"><i class="fab fa-twitter"></i></button>
                            <button class="btn btn-outline-secondary btn-sm rounded-circle shadow-sm"><i class="fab fa-whatsapp"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mb-5 text-muted small">
                <p>&copy; <?= date('Y') ?> Modular System. Seluruh hak cipta dilindungi.</p>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS UNTUK TAMPILAN BACA NYAMAN */
.article-content {
    text-align: justify;
    word-wrap: break-word;
    color: #2c3e50 !important;
}

/* Responsivitas Gambar di dalam Artikel */
.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    margin: 20px 0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.bg-gradient-dark {
    background: linear-gradient(transparent, rgba(0,0,0,0.85));
}

.transition-scale { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.transition-scale:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
.object-fit-cover { object-fit: cover; }

/* Animasi Masuk */
.fade-in { animation: fadeIn 0.8s ease-out; }
@keyframes fadeIn { 
    from { opacity: 0; transform: translateY(15px); } 
    to { opacity: 1; transform: translateY(0); } 
}
</style>