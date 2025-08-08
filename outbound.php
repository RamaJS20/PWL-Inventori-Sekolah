<?php
include 'koneksi.php';

// Generate ID Aksi Otomatis
$tanggalHariIni = date('Ymd');
$prefix = "OUT/$tanggalHariIni/";

$queryLast = "SELECT id_aksi FROM aksi_barang WHERE id_aksi LIKE '$prefix%' ORDER BY id_aksi DESC LIMIT 1";
$resultLast = $conn->query($queryLast);

if ($resultLast && $rowLast = $resultLast->fetch_assoc()) {
    $lastNumber = (int)substr($rowLast['id_aksi'], -4);
    $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
} else {
    $nextNumber = '0001';
}

$idAksiBaru = $prefix . $nextNumber;

// Proses submit form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_barang = $_POST['id_barang'];
    $qty = $_POST['qty'];
    $satuan = $_POST['satuan'];
    $harga = $_POST['harga'];
    $total_harga = $qty * $harga;
    $keterangan = $_POST['keterangan'];
    $pic = $_POST['pic'];
    $aksi = "Keluar";

    $stmt = $conn->prepare("INSERT INTO aksi_barang (id_aksi, id_barang, qty, satuan, harga, total_harga, aksi, keterangan, pic) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissdsss", $idAksiBaru, $id_barang, $qty, $satuan, $harga, $total_harga, $aksi, $keterangan, $pic);

    if ($stmt->execute()) {
        echo "<script>alert('Barang keluar berhasil dicatat!'); window.location='outbound.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barang Keluar</title>
    <link rel="stylesheet" href="style/outbound-style.css">
</head>
<body>
    <div class="container">
        <h2>Form Barang Keluar</h2>

        <form method="POST" action="" class="form-outbound">
            <div class="form-group">
                <label for="id_aksi">ID Aksi</label>
                <input type="text" name="id_aksi" id="id_aksi" value="<?= $idAksiBaru ?>" readonly>
            </div>
            
            <div class="form-group">
                <label for="id_barang">ID Barang</label>
                <select name="id_barang" id="id_barang" required>
                    <option value="">-- Pilih Barang --</option>
                    <?php
                    $barang = $conn->query("SELECT * FROM barang");
                    while ($row = $barang->fetch_assoc()) {
                        echo "<option value='{$row['id_barang']}'>{$row['id_barang']} - {$row['nama_barang']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="qty">Qty</label>
                <input type="number" name="qty" id="qty" required>
            </div>
            
            <div class="form-group">
                <label for="satuan">Satuan</label>
                <input type="text" name="satuan" id="satuan" required>
            </div>
            
            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" name="harga" id="harga" required>
            </div>
            
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <input type="text" name="keterangan" id="keterangan">
            </div>
            
            <div class="form-group">
                <label for="pic">PIC</label>
                <input type="text" name="pic" id="pic" required>
            </div>
            
            <button type="submit" class="btn-submit">Simpan</button>
        </form>

        <hr>

        <h3>Daftar Barang Keluar</h3>
        <?php
        $query = "SELECT aksi.*, barang.nama_barang 
                  FROM aksi_barang AS aksi 
                  JOIN barang AS barang ON aksi.id_barang = barang.id_barang 
                  WHERE aksi.aksi = 'Keluar' 
                  ORDER BY aksi.timestamp DESC";

        $data = $conn->query($query);
        ?>
        <?php if ($data->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID Aksi</th>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Total</th>
                    <th>Keterangan</th>
                    <th>PIC</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $data->fetch_assoc()): ?>
                <tr>
                    <td data-label="ID Aksi"><?= $row['id_aksi'] ?></td>
                    <td data-label="ID Barang"><?= $row['id_barang'] ?></td>
                    <td data-label="Nama Barang"><?= $row['nama_barang'] ?></td>
                    <td data-label="Qty"><?= $row['qty'] ?></td>
                    <td data-label="Harga"><?= number_format($row['harga']) ?></td>
                    <td data-label="Total"><?= number_format($row['total_harga']) ?></td>
                    <td data-label="Keterangan"><?= $row['keterangan'] ?></td>
                    <td data-label="PIC"><?= $row['pic'] ?></td>
                    <td data-label="Waktu"><?= $row['timestamp'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="not-found">Tidak ada data barang keluar ditemukan.</p>
        <?php endif; ?>
    </div>
</body>
</html>