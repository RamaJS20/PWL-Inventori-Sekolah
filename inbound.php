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
    $stmt->bind_param("ssisiss", $id_aksi, $id_barang, $qty, $satuan, $harga, $total, $keterangan, $pic);
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
    <link rel="stylesheet" href="style/inbound-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Form Barang Masuk</h2>
        <form method="POST" action="" class="form-inbound">
            <div class="form-group">
                <label for="id_aksi">ID Aksi</label>
                <input type="text" name="id_aksi" id="id_aksi" value="<?= $id_aksi_baru ?>" readonly>
            </div>
            
            <div class="form-group">
                <label for="id_barang">Barang</label>
                <select name="id_barang" id="id_barang" required>
                    <option value="">-- Pilih Barang --</option>
                    <?php while($row = $barang->fetch_assoc()): ?>
                        <option value="<?= $row['id_barang'] ?>"><?= $row['id_barang'] ?> - <?= $row['nama_barang'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="qty">Jumlah (Qty)</label>
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
                <textarea name="keterangan" id="keterangan"></textarea>
            </div>
            
            <div class="form-group">
                <label for="pic">PIC</label>
                <input type="text" name="pic" id="pic" required>
            </div>
            
            <button type="submit" class="btn-submit">Simpan</button>
        </form>

        <h2>Riwayat Barang Masuk</h2>
        <?php if ($masuk->num_rows > 0): ?>
        <table>
            <thead>
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
            </thead>
            <tbody>
                <?php while($row = $masuk->fetch_assoc()): ?>
                <tr>
                    <td data-label="ID Aksi"><?= $row['id_aksi'] ?></td>
                    <td data-label="Tanggal"><?= $row['timestamp'] ?></td>
                    <td data-label="ID Barang"><?= $row['id_barang'] ?></td>
                    <td data-label="Nama Barang"><?= $row['nama_barang'] ?></td>
                    <td data-label="Qty"><?= $row['qty'] ?></td>
                    <td data-label="Satuan"><?= $row['satuan'] ?></td>
                    <td data-label="Harga"><?= number_format($row['harga']) ?></td>
                    <td data-label="Total"><?= number_format($row['total_harga']) ?></td>
                    <td data-label="PIC"><?= $row['pic'] ?></td>
                    <td data-label="Keterangan"><?= $row['keterangan'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="not-found">Tidak ada riwayat barang masuk ditemukan.</p>
        <?php endif; ?>
    </div>
</body>
</html>