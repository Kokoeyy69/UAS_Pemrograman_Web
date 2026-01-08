<?php
// Proteksi Admin
if (!isset($_SESSION['is_login']) || $_SESSION['role'] != 'admin') {
    header("Location: /uas_web/index.php/home/index");
    exit;
}

$id = $_GET['id'];
$db = new Database();
$data = $db->query("SELECT * FROM artikel WHERE id = $id")->fetch_assoc();

$message = "";
if (isset($_POST['submit'])) {
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $foto_sql = "";

    // Logika Ganti Foto
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = __DIR__ . "/../../assets/img/";
        $ext = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
        $new_name = "artikel_" . time() . "." . $ext;
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {
            // Hapus foto lama jika ada
            if ($data['foto'] && file_exists($target_dir . $data['foto'])) {
                unlink($target_dir . $data['foto']);
            }
            
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_dir . $new_name)) {
                $foto_sql = ", foto='$new_name'";
            }
        } else {
            $message = "<div class='alert alert-danger'>Format gambar salah.</div>";
        }
    }

    if (empty($message)) {
        $sql = "UPDATE artikel SET judul='$judul', isi='$isi' $foto_sql WHERE id = $id";
        if ($db->query($sql)) {
            header("Location: /uas_web/index.php/artikel/index");
            exit;
        } else {
            $message = "<div class='alert alert-danger'>Gagal mengupdate artikel.</div>";
        }
    }
}
?>

<div class="fade-in">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Edit Artikel</h1>
        <a href="/uas_web/index.php/artikel/index" class="btn btn-light border rounded-pill shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <?= $message ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Judul Artikel</label>
                            <input type="text" name="judul" class="form-control form-control-lg" value="<?= $data['judul'] ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Ganti Gambar (Opsional)</label>
                            <div class="d-flex align-items-center gap-3">
                                <?php if($data['foto']): ?>
                                    <img src="//assets/img/<?= $data['foto'] ?>" class="rounded shadow-sm" width="60" height="60">
                                <?php endif; ?>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">Isi Berita</label>
                            <textarea name="isi" class="form-control" rows="10" required><?= $data['isi'] ?></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" name="submit" class="btn btn-primary px-5 rounded-pill fw-bold">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>