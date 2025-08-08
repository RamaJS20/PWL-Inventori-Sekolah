<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Aktivitas</title>
    <link rel="stylesheet" href="style/record-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Record Aktivitas</h1>
            <form class="search-form" method="GET" action="">
                <input type="text" name="cari" placeholder="Cari ID / Nama Barang / Aksi / PIC"
                       value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <?php 
        include 'koneksi.php';

        $tableName = 'aksi_barang';

        // Buat query
        if (isset($_GET['cari']) && $_GET['cari'] != '') {
            $cari = $conn->real_escape_string($_GET['cari']);
            $query = "SELECT aksi.id_aksi, aksi.timestamp, aksi.id_barang, barang.nama_barang, 
                             aksi.qty, aksi.satuan, aksi.harga, aksi.total_harga, 
                             aksi.aksi, aksi.keterangan, aksi.pic 
                      FROM $tableName AS aksi
                      JOIN barang AS barang ON aksi.id_barang = barang.id_barang
                      WHERE aksi.id_barang LIKE '%$cari%' 
                         OR aksi.aksi LIKE '%$cari%' 
                         OR aksi.pic LIKE '%$cari%' 
                         OR barang.nama_barang LIKE '%$cari%'
                      ORDER BY aksi.timestamp DESC";
        } else {
            $query = "SELECT aksi.id_aksi, aksi.timestamp, aksi.id_barang, barang.nama_barang, 
                             aksi.qty, aksi.satuan, aksi.harga, aksi.total_harga, 
                             aksi.aksi, aksi.keterangan, aksi.pic 
                      FROM $tableName AS aksi
                      JOIN barang AS barang ON aksi.id_barang = barang.id_barang
                      ORDER BY aksi.timestamp DESC";
        }

        // Eksekusi query
        $data = $conn->query($query);

        // Cek apakah query berhasil
        if ($data === false) {
            echo "<p class='not-found'>Error dalam query: " . $conn->error . "</p>";
        } elseif ($data->num_rows > 0) {
            echo "<table>";
            echo "<thead>";
            echo "<tr>
                    <th>ID Aksi</th>
                    <th>Timestamp</th>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Satuan</th>
                    <th>Harga</th>
                    <th>Total Harga</th>
                    <th>Aksi</th>
                    <th>Keterangan</th>
                    <th>PIC</th>
                    <th>Aksi</th>
                  </tr>";
            echo "</thead>";
            echo "<tbody>";

            while ($row = $data->fetch_assoc()) {
                echo "<tr>
                        <td data-label='ID Aksi'>{$row['id_aksi']}</td>
                        <td data-label='Timestamp'>{$row['timestamp']}</td>
                        <td data-label='ID Barang'>{$row['id_barang']}</td>
                        <td data-label='Nama Barang'>{$row['nama_barang']}</td>
                        <td data-label='Qty'>{$row['qty']}</td>
                        <td data-label='Satuan'>{$row['satuan']}</td>
                        <td data-label='Harga'>{$row['harga']}</td>
                        <td data-label='Total Harga'>{$row['total_harga']}</td>
                        <td data-label='Aksi'>{$row['aksi']}</td>
                        <td data-label='Keterangan'>{$row['keterangan']}</td>
                        <td data-label='PIC'>{$row['pic']}</td>
                        <td data-label='Aksi' class='action-cell'>
                            <a href='edit_record.php?id={$row['id_aksi']}' class='action-icon edit-icon' title='Edit'><i class='fas fa-edit'></i></a>
                            <a href='hapus_record.php?id={$row['id_aksi']}' class='action-icon delete-icon' title='Hapus' onclick='return confirm(\"Apakah Anda yakin ingin menghapus record ini?\")'><i class='fas fa-trash-alt'></i></a>
                        </td>
                      </tr>";
            }

            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p class='not-found'>Tidak ada data ditemukan.</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>