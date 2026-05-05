<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php
require_once 'config/database.php';

$stmt = $conn->prepare("SELECT * FROM kategori ORDER BY id_kategori DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-5">

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= $_GET['success'] ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= $_GET['error'] ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Kategori Buku</h2>
        <a href="create.php" class="btn btn-primary">Tambah Kategori</a>
    </div>

    <div class="card">
        <div class="card-body">

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php $no = 1; ?>
                <?php while($row = $result->fetch_assoc()): ?>

                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['kode_kategori'] ?></td>
                        <td><?= $row['nama_kategori'] ?></td>
                        <td><?= $row['deskripsi'] ?></td>

                        <td>
                            <?php if($row['status'] == 'Aktif'): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a href="edit.php?id=<?= $row['id_kategori'] ?>" class="btn btn-warning btn-sm">Edit</a>

                            <a href="delete.php?id=<?= $row['id_kategori'] ?>"
                               onclick="return confirm('Yakin ingin menghapus kategori ini?')"
                               class="btn btn-danger btn-sm">
                               Hapus
                            </a>
                        </td>
                    </tr>

                <?php endwhile; ?>

                </tbody>
            </table>

        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Yakin ingin menghapus kategori ini?')) {
        window.location.href = 'delete.php?id=' + id;
    }
}
</script>

</body>
</html>
