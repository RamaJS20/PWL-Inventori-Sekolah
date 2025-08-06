<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary Page</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 16px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Summary Page</h1>

    <form method="GET" action="">
        <input type="text" name="cari" placeholder="Cari ID Barang / Aksi / PIC"
               value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>">
        <button type="submit">Cari</button>
    </form>

    <?php 
    include 'koneksi.php';

    // Buat query
    if (isset($_GET['cari']) && $_GET['cari'] != '') {
        $cari = $conn->real_escape_string($_GET['cari']);
        $query = "SELECT aksi.id_aksi, aksi.timestamp, aksi.id_barang, barang.nama_barang, 
                         aksi.qty, aksi.satuan, aksi.harga, aksi.total_harga, 
                         aksi.aksi, aksi.keterangan, aksi.pic 
                  FROM aksi_barang AS aksi
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
                  FROM aksi_barang AS aksi
                  JOIN barang AS barang ON aksi.id_barang = barang.id_barang";
    }

    // Eksekusi query
    $data = $conn->query($query);

    // Cek apakah query berhasil
    if ($data === false) {
        echo "<p>Error dalam query: " . $conn->error . "</p>";
    } elseif ($data->num_rows > 0) {
        echo "<table>";
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
              </tr>";

        while ($row = $data->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id_aksi']}</td>
                    <td>{$row['timestamp']}</td>
                    <td>{$row['id_barang']}</td>
                    <td>{$row['nama_barang']}</td>
                    <td>{$row['qty']}</td>
                    <td>{$row['satuan']}</td>
                    <td>{$row['harga']}</td>
                    <td>{$row['total_harga']}</td>
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
