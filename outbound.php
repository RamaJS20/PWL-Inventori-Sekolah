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

<h2>Form Barang Keluar</h2>

<form method="POST" action="">
    <label>ID Aksi</label><br>
    <input type="text" name="id_aksi" value="<?= $idAksiBaru ?>" readonly><br><br>

    <label>ID Barang</label><br>
    <select name="id_barang" required>
        <option value="">-- Pilih Barang --</option>
        <?php
        $barang = $conn->query("SELECT * FROM barang");
        while ($row = $barang->fetch_assoc()) {
            echo "<option value='{$row['id_barang']}'>{$row['id_barang']} - {$row['nama_barang']}</option>";
        }
        ?>
    </select><br><br>

    <label>Qty</label><br>
    <input type="number" name="qty" required><br><br>

    <label>Satuan</label><br>
    <input type="text" name="satuan" required><br><br>

    <label>Harga</label><br>
    <input type="number" name="harga" required><br><br>

    <label>Keterangan</label><br>
    <input type="text" name="keterangan"><br><br>

    <label>PIC</label><br>
    <input type="text" name="pic" required><br><br>

    <button type="submit">Simpan</button>
</form>

<hr>

<h3>Daftar Barang Keluar</h3>
<table border="1" cellpadding="5" cellspacing="0">
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

    <?php
    $query = "SELECT aksi.*, barang.nama_barang 
              FROM aksi_barang AS aksi 
              JOIN barang AS barang ON aksi.id_barang = barang.id_barang 
              WHERE aksi.aksi = 'Keluar' 
              ORDER BY aksi.timestamp DESC";

    $data = $conn->query($query);

    while ($row = $data->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id_aksi']}</td>
                <td>{$row['id_barang']}</td>
                <td>{$row['nama_barang']}</td>
                <td>{$row['qty']}</td>
                <td>{$row['harga']}</td>
                <td>{$row['total_harga']}</td>
                <td>{$row['keterangan']}</td>
                <td>{$row['pic']}</td>
                <td>{$row['timestamp']}</td>
              </tr>";
    }
    ?>
</table>
