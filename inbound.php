<?php
include 'koneksi.php';

// Fungsi buat generate ID Aksi otomatis
function generateIdAksi($conn) {
    $tgl = date("Ymd");
    $prefix = "IN/$tgl/";
    
    $query = "SELECT id_aksi FROM aksi_barang WHERE id_aksi LIKE '$prefix%' ORDER BY id_aksi DESC LIMIT 1";
    $result = $conn->query($query);
    
    if ($row = $result->fetch_assoc()) {
        $lastNumber = (int)substr($row['id_aksi'], -4);
        $nextNumber = str_pad($lastNumber + 1, 4, "0", STR_PAD_LEFT);
    } else {
        $nextNumber = "0001";
    }
    
    return $prefix . $nextNumber;
}

$id_aksi_baru = generateIdAksi($conn);

// Simpan data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_aksi = $_POST['id_aksi'];
    $id_barang = $_POST['id_barang'];
    $qty = $_POST['qty'];
    $satuan = $_POST['satuan'];
    $harga = $_POST['harga'];
    $total = $qty * $harga;
    $keterangan = $_POST['keterangan'];
    $pic = $_POST['pic'];

    $stmt = $conn->prepare("INSERT INTO aksi_barang (id_aksi, id_barang, qty, satuan, harga, total_harga, aksi, keterangan, pic) 
                            VALUES (?, ?, ?, ?, ?, ?, 'Masuk', ?, ?)");
    $stmt->bind_param("ssississ", $id_aksi, $id_barang, $qty, $satuan, $harga, $total, $keterangan, $pic);
    $stmt->execute();
    $stmt->close();

    // Redirect untuk refresh tanpa resubmit
    header("Location: inbound.php");
    exit;
}

// Ambil data barang untuk dropdown
$barang = $conn->query("SELECT id_barang, nama_barang FROM barang");

// Ambil list barang masuk
$masuk = $conn->query("SELECT aksi.*, b.nama_barang FROM aksi_barang aksi 
                       JOIN barang b ON aksi.id_barang = b.id_barang 
                       WHERE aksi.aksi = 'Masuk'
                       ORDER BY aksi.timestamp DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barang Masuk</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form, table { max-width: 800px; margin-bottom: 30px; }
        input, select, textarea { width: 100%; padding: 6px; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>

<h2>Form Barang Masuk</h2>
<form method="POST" action="">
    <label>ID Aksi</label>
    <input type="text" name="id_aksi" value="<?= $id_aksi_baru ?>" readonly>

    <label>Barang</label>
    <select name="id_barang" required>
        <option value="">-- Pilih Barang --</option>
        <?php while($row = $barang->fetch_assoc()): ?>
            <option value="<?= $row['id_barang'] ?>"><?= $row['id_barang'] ?> - <?= $row['nama_barang'] ?></option>
        <?php endwhile; ?>
    </select>

    <label>Jumlah (Qty)</label>
    <input type="number" name="qty" required>

    <label>Satuan</label>
    <input type="text" name="satuan" required>

    <label>Harga</label>
    <input type="number" name="harga" required>

    <label>Keterangan</label>
    <textarea name="keterangan"></textarea>

    <label>PIC</label>
    <input type="text" name="pic" required>

    <button type="submit">Simpan</button>
</form>

<h2>Riwayat Barang Masuk</h2>
<table>
    <tr>
        <th>ID Aksi</th>
        <th>Tanggal</th>
        <th>ID Barang</th>
        <th>Nama Barang</th>
        <th>Qty</th>
        <th>Satuan</th>
        <th>Harga</th>
        <th>Total</th>
        <th>PIC</th>
        <th>Keterangan</th>
    </tr>
    <?php while($row = $masuk->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id_aksi'] ?></td>
        <td><?= $row['timestamp'] ?></td>
        <td><?= $row['id_barang'] ?></td>
        <td><?= $row['nama_barang'] ?></td>
        <td><?= $row['qty'] ?></td>
        <td><?= $row['satuan'] ?></td>
        <td><?= number_format($row['harga']) ?></td>
        <td><?= number_format($row['total_harga']) ?></td>
        <td><?= $row['pic'] ?></td>
        <td><?= $row['keterangan'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
