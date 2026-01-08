<?php
/**
 * 1. FUNGSI HELPER SEO SLUG
 * Mengubah judul menjadi format URL friendly.
 */
function createSlug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
}

/**
 * 2. PROTEKSI ADMIN (ACL)
 */
if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'admin') {
    header("Location: /uas_web/index.php/home/index");
    exit;
}

$message = "";

/**
 * 3. PROSES SUBMIT
 */
if (isset($_POST['submit'])) {
    $db = new Database();
    $judul = trim($_POST['judul']);
    $slug  = createSlug($judul); // Otomatis buat slug
    $isi   = $_POST['isi'];      // Menangkap data HTML CKEditor
    $gambar_value = null;

    // LOGIKA UPLOAD FILE
    if (!empty($_FILES['foto']['name'])) {
        $relative_path = "assets/img/artikel/";
        $target_dir = __DIR__ . "/../../" . $relative_path;
        
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true); 
        }

        $ext = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
        $new_name = "art_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($ext, $allowed)) {
            if ($_FILES["foto"]["size"] <= 2000000) {
                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_dir . $new_name)) {
                    $gambar_value = "//" . $relative_path . $new_name;
                } else {
                    $message = "<div class='alert alert-danger rounded-4 shadow-sm'>Ukuran gambar terlalu besar! Maksimal 2MB.</div>";
                }
            } else {
                $message = "<div class='alert alert-danger rounded-4 shadow-sm'>Ukuran file melebihi batas.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger rounded-4 shadow-sm'>Format gambar tidak didukung.</div>";
        }
    }

    /**
     * 4. SIMPAN KE DATABASE (PDO)
     * Menyertakan kolom 'slug' agar tidak terjadi kesalahan instruksi database.
     */
    if (empty($message)) {
        $sql = "INSERT INTO artikel (judul, slug, isi, gambar, tanggal) VALUES (:judul, :slug, :isi, :gambar, NOW())";
        $params = [
            ':judul'  => $judul,
            ':slug'   => $slug,
            ':isi'    => $isi,
            ':gambar' => $gambar_value
        ];

        try {
            if ($db->runQuery($sql, $params)) {
                echo "<script>alert('Artikel berhasil dipublikasikan!'); window.location.href='/uas_web/index.php/artikel/index';</script>";
                exit;
            }
        } catch (Exception $e) {
            // Tampilkan error spesifik saat pengembangan jika diperlukan
            $message = "<div class='alert alert-danger rounded-4 shadow-sm'>Kesalahan Database: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<div class="fade-in px-3 mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 mb-0 text-primary fw-bold"><i class="fas fa-pen-fancy me-2"></i>Tulis Artikel</h1>
                    <p class="text-muted small mb-0">Kelola konten dengan URL ramah SEO.</p>
                </div>
                <a href="/uas_web/index.php/artikel/index" class="btn btn-light border rounded-pill shadow-sm px-4 fw-bold">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <?= $message ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Judul Berita</label>
                            <input type="text" name="judul" class="form-control form-control-lg bg-light border-0 shadow-none px-3" placeholder="Masukkan judul..." required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Gambar Sampul</label>
                            <div class="input-group">
                                <label class="input-group-text bg-light border-0 px-3" for="foto"><i class="fas fa-cloud-upload-alt text-primary"></i></label>
                                <input type="file" name="foto" id="foto" class="form-control bg-light border-0 shadow-none py-2" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Konten Lengkap</label>
                            <textarea name="isi" id="editor" class="form-control bg-light border-0 shadow-none px-3 py-3"></textarea>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" name="submit" class="btn btn-primary rounded-pill fw-bold shadow-sm py-3 transition-scale">
                                <i class="fas fa-paper-plane me-2"></i> Publikasikan Artikel Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo']
        })
        .catch(error => { console.error(error); });
</script>

<style>
.ck-editor__editable { min-height: 400px; background-color: #f8f9fc !important; border: none !important; }
.transition-scale { transition: all 0.3s ease; }
.transition-scale:hover { transform: translateY(-3px); box-shadow: 0 0.5rem 1rem rgba(78, 115, 223, 0.25)!important; }
.bg-light { background-color: #f8f9fc !important; }
.fade-in { animation: fadeIn 0.8s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
</style>