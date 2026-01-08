<?php
/**
 * 1. PROTEKSI AKSES (ACL)
 * Memastikan hanya Administrator yang berwenang menghapus akun.
 */
if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: /uas_web/index.php/home/index");
    exit;
}

$db = new Database();
// Casting ke integer untuk mencegah manipulasi string (SQL Injection Protection).
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id) {
    header("Location: /uas_web/index.php/user/index");
    exit;
}

/**
 * 2. PROTEKSI DIRI SENDIRI
 * Mencegah admin menghapus akunnya sendiri yang sedang digunakan.
 */
if ($id === (int)$_SESSION['user_id']) {
    echo "<script>alert('Kesalahan: Anda tidak diizinkan menghapus akun sendiri!'); window.location.href='/uas_web/index.php/user/index';</script>";
    exit;
}

try {
    /**
     * 3. AMBIL DATA USER (CEK FOTO)
     * Mengambil path foto sebelum data di database dimusnahkan.
     */
    $stmt = $db->runQuery("SELECT foto FROM user WHERE id = :id", [':id' => $id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user_data) {
        /**
         * 4. HAPUS FILE FISIK FOTO PROFIL (Cleanup)
         * Menggunakan DOCUMENT_ROOT agar path file selalu akurat di hosting maupun localhost.
         */
        if (!empty($user_data['foto'])) {
            // Bersihkan path agar mendapatkan lokasi relatif yang bersih
            $relative_path = ltrim(str_replace("//", "", $user_data['foto']), "/");
            $full_file_path = $_SERVER['DOCUMENT_ROOT'] . "//" . $relative_path;

            // Pastikan file benar-benar ada dan merupakan file biasa (bukan folder) sebelum dihapus
            if (file_exists($full_file_path) && is_file($full_file_path)) {
                unlink($full_file_path); 
            }
        }

        /**
         * 5. HAPUS DATA DARI DATABASE
         * Menghapus record pengguna secara permanen menggunakan Prepared Statements.
         */
        $sql_delete = "DELETE FROM user WHERE id = :id";
        if ($db->runQuery($sql_delete, [':id' => $id])) {
            echo "<script>alert('Akun pengguna berhasil dihapus secara permanen!'); window.location.href='/uas_web/index.php/user/index';</script>";
            exit;
        }
    } else {
        header("Location: /uas_web/index.php/user/index");
    }

} catch (Exception $e) {
    // Sembunyikan pesan teknis database dari user untuk alasan keamanan.
    die("<div style='text-align:center; padding-top:50px; font-family:sans-serif;'>
            <h3 style='color:red;'>Gagal menghapus data.</h3>
            <p>Terjadi kesalahan sistem. Silakan hubungi pengembang.</p>
            <a href='/uas_web/index.php/user/index'>Kembali ke Daftar User</a>
         </div>");
}