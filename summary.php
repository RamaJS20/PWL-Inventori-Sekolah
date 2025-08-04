<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary Page</title>
</head>
<body>
    <h1>Summary Page</h1>

    <form method="GET" action="">
        <input type="text" name="cari" placeholder="Cari ID Barang / Aksi / PIC" value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>">
        <button type="submit">Cari</button>
    </form>

   <?php 
        include 'koneksi.php';

        // CEK PENCARIAN
        if (isset($_GET['cari']) && $_GET['cari'] != '') {
            $cari = $conn->real_escape_string($_GET['cari']); // aman dari SQL injection
            $query = "SELECT * FROM aksi_barang 
                      WHERE id_barang LIKE '%$cari%' 
                         OR aksi LIKE '%$cari%' 
                         OR pic LIKE '%$cari%'
                      ORDER BY timestamp DESC";
        } else {
            $query = "SELECT * FROM aksi_barang ORDER BY timestamp DESC";
        }

        $data = $conn->query($query);

        // TAMPILKAN TABEL
        if ($data->num_rows > 0) {
            echo "<table>";
            echo "<tr>
                    <th>ID Aksi</th>
                    <th>Timestamp</th>
                    <th>ID Barang</th>
                    <th>Qty</th>
                    <th>Satuan</th>
                    <th>Aksi</th>
                    <th>Keterangan</th>
                    <th>PIC</th>
                  </tr>";

            while ($row = $data->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id_aksi']}</td>
                        <td>{$row['timestamp']}</td>
                        <td>{$row['id_barang']}</td>
                        <td>{$row['qty']}</td>
                        <td>{$row['satuan']}</td>
                        <td>{$row['aksi']}</td>
                        <td>{$row['keterangan']}</td>
                        <td>{$row['pic']}</td>
                      </tr>";
            }

            echo "</table>";
        } else {
            echo "<p>Tidak ada data ditemukan.</p>";
        }

        $conn->close();
    ?>

</body>
</html>