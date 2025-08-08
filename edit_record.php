<?php
include 'koneksi.php';

$tableName = 'aksi_barang';
$id_aksi = '';
$timestamp = '';
$id_barang = '';
$nama_barang = '';
$qty = '';
$satuan = '';
$harga = '';
$total_harga = '';
$aksi = '';
$keterangan = '';
$pic = '';
$error = '';
$success = '';

// Proses saat form disubmit (menggunakan metode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_aksi = $_POST['id_aksi'];
    $id_barang = $_POST['id_barang'];
    $qty = $_POST['qty'];
    $satuan = $_POST['satuan'];
    $harga = $_POST['harga'];
    $keterangan = $_POST['keterangan'];
    $pic = $_POST['pic'];
    
    // Total harga dihitung ulang
    $total_harga = $qty * $harga;

    // Validasi sederhana
    if (empty($id_barang) || empty($qty) || empty($satuan) || empty($harga) || empty($pic)) {
        $error = "ID Barang, Qty, Satuan, Harga, dan PIC tidak boleh kosong.";
    } else {
        // Query untuk update data
        $query = "UPDATE $tableName SET 
                    id_barang = '$id_barang',
                    qty = '$qty',
                    satuan = '$satuan',
                    harga = '$harga',
                    total_harga = '$total_harga',
                    keterangan = '$keterangan',
                    pic = '$pic'
                  WHERE id_aksi = '$id_aksi'";

        if ($conn->query($query) === TRUE) {
            $success = "Record aktivitas berhasil diperbarui!";
            // Refresh data setelah update berhasil
            header("Refresh:0");
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

// Mengambil data record yang akan diedit (menggunakan metode GET)
if (isset($_GET['id'])) {
    $id_aksi_get = $conn->real_escape_string($_GET['id']);
    
    $query = "SELECT aksi.id_aksi, aksi.timestamp, aksi.id_barang, barang.nama_barang, 
                     aksi.qty, aksi.satuan, aksi.harga, aksi.total_harga, 
                     aksi.aksi, aksi.keterangan, aksi.pic 
              FROM aksi_barang AS aksi
              JOIN barang AS barang ON aksi.id_barang = barang.id_barang
              WHERE aksi.id_aksi = '$id_aksi_get'";
    
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_aksi = $row['id_aksi'];
        $timestamp = $row['timestamp'];
        $id_barang = $row['id_barang'];
        $nama_barang = $row['nama_barang'];
        $qty = $row['qty'];
        $satuan = $row['satuan'];
        $harga = $row['harga'];
        $total_harga = $row['total_harga'];
        $aksi = $row['aksi'];
        $keterangan = $row['keterangan'];
        $pic = $row['pic'];
    } else {
        $error = "Record tidak ditemukan.";
    }
} else {
    // Jika tidak ada ID di URL, kembali ke halaman utama
    header("Location: record.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Record Aktivitas</title>
    <link rel="stylesheet" href="style/record-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1 class="record-summary">Edit Record Aktivitas</h1>

        <?php if ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message success"><?= $success ?></div>
        <?php endif; ?>

        <form action="edit_record.php" method="POST" class="form-edit">
            <input type="hidden" name="id_aksi" value="<?= htmlspecialchars($id_aksi) ?>">
            
            <div class="form-group">
                <label for="timestamp">Timestamp:</label>
                <input type="text" name="timestamp" id="timestamp" value="<?= htmlspecialchars($timestamp) ?>" disabled>
            </div>

            <div class="form-group">
                <label for="id_barang">ID Barang:</label>
                <input type="text" name="id_barang" id="id_barang" value="<?= htmlspecialchars($id_barang) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="nama_barang">Nama Barang:</label>
                <input type="text" name="nama_barang" id="nama_barang" value="<?= htmlspecialchars($nama_barang) ?>" disabled>
            </div>
            
            <div class="form-group">
                <label for="aksi">Aksi:</label>
                <input type="text" name="aksi" id="aksi" value="<?= htmlspecialchars($aksi) ?>" disabled>
            </div>
            
            <div class="form-group">
                <label for="qty">Qty:</label>
                <input type="number" name="qty" id="qty" value="<?= htmlspecialchars($qty) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="satuan">Satuan:</label>
                <input type="text" name="satuan" id="satuan" value="<?= htmlspecialchars($satuan) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="harga">Harga:</label>
                <input type="number" name="harga" id="harga" value="<?= htmlspecialchars($harga) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="keterangan">Keterangan:</label>
                <textarea name="keterangan" id="keterangan" rows="4"><?= htmlspecialchars($keterangan) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="pic">PIC:</label>
                <input type="text" name="pic" id="pic" value="<?= htmlspecialchars($pic) ?>" required>
            </div>
            
            <button type="submit" class="btn-update">Update Record</button>
            <a href="record.php" class="btn-update" style="background-color: #6c757d; margin-left: 10px;">Kembali</a>
        </form>
    </div>
</body>
</html>