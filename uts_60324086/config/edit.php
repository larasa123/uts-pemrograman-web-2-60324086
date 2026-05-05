<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php
require_once 'config/database.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php?error=ID tidak ditemukan");
    exit;
}

// ambil data berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    header("Location: index.php?error=Data tidak ditemukan");
    exit;
}

// isi awal form
$kode = $data['kode_kategori'];
$nama = $data['nama_kategori'];
$deskripsi = $data['deskripsi'];
$status = $data['status'];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // ambil data + sanitasi
    $kode = htmlspecialchars(trim($_POST['kode_kategori']));
    $nama = htmlspecialchars(trim($_POST['nama_kategori']));
    $deskripsi = htmlspecialchars(trim($_POST['deskripsi']));
    $status = $_POST['status'];

    // validasi kode
    if (empty($kode)) {
        $errors[] = "Kode wajib diisi";
    } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
        $errors[] = "Kode harus 4-10 karakter";
    } elseif (substr($kode, 0, 4) != "KAT-") {
        $errors[] = "Kode harus diawali KAT-";
    }

    // validasi nama
    if (empty($nama)) {
        $errors[] = "Nama wajib diisi";
    } elseif (strlen($nama) < 3 || strlen($nama) > 50) {
        $errors[] = "Nama harus 3-50 karakter";
    }

    // validasi deskripsi
    if (strlen($deskripsi) > 200) {
        $errors[] = "Deskripsi maksimal 200 karakter";
    }

    // validasi status
    if (!in_array($status, ['Aktif', 'Nonaktif'])) {
        $errors[] = "Status tidak valid";
    }

    // cek duplikat (kecuali data sendiri)
    $cek = $conn->prepare("SELECT id_kategori FROM kategori WHERE kode_kategori = ? AND id_kategori != ?");
    $cek->bind_param("si", $kode, $id);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        $errors[] = "Kode sudah digunakan";
    }

    // update data
    if (empty($errors)) {
        $update = $conn->prepare("
            UPDATE kategori 
            SET kode_kategori=?, nama_kategori=?, deskripsi=?, status=? 
            WHERE id_kategori=?
        ");

        $update->bind_param("ssssi", $kode, $nama, $deskripsi, $status, $id);

        if ($update->execute()) {
            header("Location: index.php?success=Data berhasil diupdate");
            exit;
        } else {
            $errors[] = "Gagal update data";
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card">
                <div class="card-header">
                    <h4>Edit Kategori</h4>
                </div>

                <div class="card-body">

                    <!-- ERROR -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $e): ?>
                                <div><?= $e ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- FORM -->
                    <form method="POST">

                        <div class="mb-3">
                            <label>Kode Kategori</label>
                            <input type="text" name="kode_kategori" class="form-control" value="<?= $kode ?>">
                        </div>

                        <div class="mb-3">
                            <label>Nama Kategori</label>
                            <input type="text" name="nama_kategori" class="form-control" value="<?= $nama ?>">
                        </div>

                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control"><?= $deskripsi ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Status</label><br>
                            <input type="radio" name="status" value="Aktif" <?= $status == 'Aktif' ? 'checked' : '' ?>> Aktif
                            <input type="radio" name="status" value="Nonaktif" <?= $status == 'Nonaktif' ? 'checked' : '' ?>> Nonaktif
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="index.php" class="btn btn-secondary">Kembali</a>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>