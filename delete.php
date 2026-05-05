<?php
require_once 'config/database.php';

// ambil id dari URL
$id = $_GET['id'] ?? null;

// validasi id
if (!$id) {
    header("Location: index.php?error=ID tidak ditemukan");
    exit;
}

// cek data ada atau enggak
$cek = $conn->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
$cek->bind_param("i", $id);
$cek->execute();
$result = $cek->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php?error=Data tidak ditemukan");
    exit;
}

// proses delet
$delete = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
$delete->bind_param("i", $id);

if ($delete->execute()) {

    // cek apakah sudah terhapus
    if ($delete->affected_rows > 0) {
        header("Location: index.php?success=Data berhasil dihapus");
        exit;
    } else {
        header("Location: index.php?error=Gagal menghapus data");
        exit;
    }

} else {
    header("Location: index.php?error=Query gagal dijalankan");
    exit;
}
?>
