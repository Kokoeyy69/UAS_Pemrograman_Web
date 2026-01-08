<?php
/**
 * 1. PROTEKSI AKSES (ACL)
 * Memastikan hanya Administrator yang memiliki wewenang untuk menghapus konten.
 */
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: /uas_web/index.php/home/index");
    exit;
}

/**
 * 2. INISIALISASI & VALIDASI ID
 * Melakukan casting (int) pada ID untuk mencegah eksploitasi URL.
 */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $db = new Database();
    
    try {
        /**
         * 3. AMBIL INFO GAMBAR SEBELUM DATA DIHAPUS (Pembersihan Sampah)
         * Mengambil path gambar untuk dihapus dari folder assets.
         */
        $sql_select = "SELECT gambar FROM artikel WHERE id = :id";
        $stmt_select = $db->runQuery($sql_select, [':id' => $id]);
        $data = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            // Jika ada path gambar di database, lakukan pembersihan file fisik
            if (!empty($data['gambar'])) {
                /**
                 * Konversi URL Database ke Path Sistem
                 * Path DB: //assets/img/artikel/file.jpg
                 * Path Server: C:/xampp/htdocs//assets/img/artikel/file.jpg
                 */
                $relative_path = str_replace("//", "", $data['gambar']);
                $absolute_path = __DIR__ . "/../../" . $relative_path;
                
                // Pastikan file benar-benar ada di folder sebelum unlink
                if (file_exists($absolute_path)) {
                    unlink($absolute_path);
                }
            }

            /**
             * 4. HAPUS DATA DARI DATABASE (PDO)
             * Menghapus record setelah file fisiknya berhasil dibersihkan.
             */
            $sql_delete = "DELETE FROM artikel WHERE id = :id";
            if ($db->runQuery($sql_delete, [':id' => $id])) {
                echo "<script>
                        alert('Artikel dan file gambar berhasil dihapus!');
                        window.location.href='/uas_web/index.php/artikel/index';
                      </script>";
                exit;
            }
        } else {
            // Kasus jika ID ada tapi data tidak ditemukan di DB
            echo "<script>alert('Data tidak ditemukan!'); window.location='/uas_web/index.php/artikel/index';</script>";
        }

    } catch (Exception $e) {
        /**
         * Information Hiding: Jangan tampilkan $e->getMessage() ke publik.
         * Log error secara internal jika diperlukan.
         */
        die("<script>alert('Terjadi kesalahan sistem saat menghapus data.'); window.location='/uas_web/index.php/artikel/index';</script>");
    }
} else {
    // Jika tidak ada ID yang dikirim, lempar kembali ke index
    header("Location: /uas_web/index.php/artikel/index");
    exit;
}