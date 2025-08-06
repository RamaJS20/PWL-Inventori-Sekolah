<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary Barang</title>
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
    <h1>Summary Barang</h1>

    <form method="GET" action="">
        <input type="text" name="cari" placeholder="Cari ID / Nama Barang"
               value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>">
        <button type="submit">Cari</button>
    </form>

    <?php 
    include 'koneksi.php';

    // Ganti 'NAMA_TABEL_BARANG' dengan nama tabel sebenarnya
    $tableName = 'barang'; // misalnya ini nama tabel kamu

    if (isset($_GET['cari']) && $_GET['cari'] != '') {
        $cari = $conn->real_escape_string($_GET['cari']);
        $query = "SELECT * FROM $tableName 
                  WHERE id_barang LIKE '%$cari%' 
                     OR nama_barang LIKE '%$cari%' 
                  ORDER BY id_barang ASC";
    } else {
        $query = "SELECT * FROM $tableName ORDER BY id_barang ASC";
    }

    $data = $conn->query($query);

    if ($data === false) {
        echo "<p>Error: " . $conn->error . "</p>";
    } elseif ($data->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Deskripsi</th>
                <th>Satuan</th>
              </tr>";

        while ($row = $data->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id_barang']}</td>
                    <td>{$row['nama_barang']}</td>
                    <td>{$row['deskripsi']}</td>
                    <td>{$row['satuan_unit']}</td>
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
