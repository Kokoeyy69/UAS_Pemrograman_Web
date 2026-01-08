<?php
/**
 * 1. FUNGSI HELPER SEO SLUG
 * Sinkronisasi URL dengan judul yang diperbarui.
 */
function createSlug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
}

/**
 * 2. PROTEKSI AKSES & INISIALISASI
 * Memastikan keamanan sesi Administrator.
 */
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: /uas_web/index.php/home/index");
    exit;
}

$db = new Database();
$message = "";

/**
 * 3. AMBIL DATA LAMA (GET)
 * Mengambil record berdasarkan ID untuk populasi form.
 */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
try {
    $stmt_old = $db->runQuery("SELECT * FROM artikel WHERE id = :id", [':id' => $id]);
    $data_lama = $stmt_old->fetch(PDO::FETCH_ASSOC);

    if (!$data_lama) {
        header("Location: /uas_web/index.php/artikel/index");
        exit;
    }
} catch (Exception $e) {
    die("Terjadi kesalahan sistem saat memuat data.");
}

/**
 * 4. PROSES SIMPAN PERUBAHAN (POST)
 */
if (isset($_POST['submit'])) {
    $judul = trim($_POST['judul']);
    $slug  = createSlug($judul); // Update slug agar tetap SEO Friendly
    $isi   = $_POST['isi'];      // Menampung data HTML CKEditor
    $path_gambar = $data_lama['gambar']; 

    try {
        // Logika Upload Gambar Baru (Jika Ada)
        if (!empty($_FILES['foto']['name'])) {
            $ext = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ext, $allowed)) {
                // A. Cleanup: Hapus file gambar lama dari server
                if (!empty($data_lama['gambar'])) {
                    $relative_old = str_replace("//", "", $data_lama['gambar']);
                    $old_file_path = __DIR__ . "/../../" . $relative_old;
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }

                // B. Upload file gambar baru
                $new_name = "art_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
                $target_dir = __DIR__ . "/../../assets/img/artikel/";
                if (!file_exists($target_dir)) { mkdir($target_dir, 0755, true); }

                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_dir . $new_name)) {
                    $path_gambar = "//assets/img/artikel/" . $new_name;
                }
            } else {
                $message = "<div class='alert alert-danger rounded-4 shadow-sm'>Format file tidak didukung!</div>";
            }
        }

        /**
         * 5. UPDATE DATABASE (PDO)
         * Menyertakan kolom 'slug' dalam query update.
         */
        if (empty($message)) {
            $sql = "UPDATE artikel SET judul = :j, slug = :s, isi = :i, gambar = :g WHERE id = :id";
            $params = [
                ':j'  => $judul,
                ':s'  => $slug,
                ':i'  => $isi,
                ':g'  => $path_gambar,
                ':id' => $id
            ];

            if ($db->runQuery($sql, $params)) {
                echo "<script>alert('Perubahan berhasil disimpan!'); window.location='/uas_web/index.php/artikel/index';</script>";
                exit;
            }
        }
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger rounded-4 shadow-sm'>Gagal memperbarui data: " . $e->getMessage() . "</div>";
    }
}
?>

<div class="fade-in px-3 mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-primary fw-bold"><i class="fas fa-edit me-2"></i>Edit Artikel</h1>
                <a href="/uas_web/index.php/artikel/index" class="btn btn-light border rounded-pill shadow-sm px-4 fw-bold">
                    <i class="fas fa-arrow-left me-2"></i>Batal
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <?= $message ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Judul Artikel</label>
                            <input type="text" name="judul" class="form-control bg-light border-0 py-2 shadow-none px-3" 
                                   value="<?= htmlspecialchars($data_lama['judul']) ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Gambar Sampul</label>
                            <div class="mb-3">
                                <?php if($data_lama['gambar']): ?>
                                    <div class="position-relative d-inline-block">
                                        <img src="<?= $data_lama['gambar'] ?>" class="rounded-3 shadow-sm border" width="200" alt="Thumbnail">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary shadow">Aktif</span>
                                    </div>
                                <?php else: ?>
                                    <div class="p-3 bg-light border rounded text-muted small">Belum ada gambar sampul.</div>
                                <?php endif; ?>
                            </div>
                            <label class="form-label fw-bold small text-muted text-uppercase">Ganti Gambar (Opsional)</label>
                            <input type="file" name="foto" class="form-control bg-light border-0 py-2 shadow-none px-3">
                            <div class="form-text small">Biarkan kosong jika tidak ingin mengganti gambar.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Isi Konten</label>
                            <textarea name="isi" id="editor"><?= $data_lama['isi'] ?></textarea>
                        </div>

                        <div class="d-grid pt-3">
                            <button type="submit" name="submit" class="btn btn-primary rounded-pill fw-bold shadow-sm py-3 transition-scale">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan Artikel
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
.fade-in { animation: fadeIn 0.8s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>