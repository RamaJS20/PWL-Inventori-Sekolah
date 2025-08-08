<?php
include 'koneksi.php';

$tableName = 'barang';
$id_barang = '';
$nama_barang = '';
$deskripsi = '';
$satuan_unit = '';
$error = '';
$success = '';

// Proses saat form disubmit (menggunakan metode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_barang_old = $_POST['id_barang_old']; // ID lama untuk WHERE clause
    $id_barang = $_POST['id_barang'];
    $nama_barang = $_POST['nama_barang'];
    $deskripsi = $_POST['deskripsi'];
    $satuan_unit = $_POST['satuan_unit'];

    // Validasi sederhana
    if (empty($id_barang) || empty($nama_barang) || empty($satuan_unit)) {
        $error = "ID Barang, Nama Barang, dan Satuan tidak boleh kosong.";
    } else {
        // Query untuk update data
        $query = "UPDATE $tableName SET 
                    id_barang = '$id_barang',
                    nama_barang = '$nama_barang',
                    deskripsi = '$deskripsi',
                    satuan_unit = '$satuan_unit'
                  WHERE id_barang = '$id_barang_old'";

        if ($conn->query($query) === TRUE) {
            $success = "Data barang berhasil diperbarui!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

// Mengambil data barang yang akan diedit (menggunakan metode GET)
if (isset($_GET['id'])) {
    $id_barang_get = $conn->real_escape_string($_GET['id']);
    $query = "SELECT * FROM $tableName WHERE id_barang = '$id_barang_get'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_barang = $row['id_barang'];
        $nama_barang = $row['nama_barang'];
        $deskripsi = $row['deskripsi'];
        $satuan_unit = $row['satuan_unit'];
    } else {
        $error = "Data tidak ditemukan.";
    }
} else {
    // Jika tidak ada ID di URL, kembali ke halaman utama
    header("Location: database.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang</title>
    <link rel="stylesheet" href="style/database-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1 class="database-summary">Edit Data Barang</h1>

        <?php if ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message success"><?= $success ?></div>
        <?php endif; ?>

        <form action="edit.php" method="POST" class="form-edit">
            <input type="hidden" name="id_barang_old" value="<?= htmlspecialchars($id_barang) ?>">
            
            <div class="form-group">
                <label for="id_barang">ID Barang:</label>
                <input type="text" name="id_barang" id="id_barang" value="<?= htmlspecialchars($id_barang) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="nama_barang">Nama Barang:</label>
                <input type="text" name="nama_barang" id="nama_barang" value="<?= htmlspecialchars($nama_barang) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea name="deskripsi" id="deskripsi" rows="4"><?= htmlspecialchars($deskripsi) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="satuan_unit">Satuan:</label>
                <input type="text" name="satuan_unit" id="satuan_unit" value="<?= htmlspecialchars($satuan_unit) ?>" required>
            </div>
            
            <button type="submit" class="btn-update">Update Data</button>
            <a href="database.php" class="btn-update" style="background-color: #6c757d; margin-left: 10px;">Kembali</a>
        </form>
    </div>
</body>
</html>