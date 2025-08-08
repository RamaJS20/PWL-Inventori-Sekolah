<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Barang</title>
    <link rel="stylesheet" href="style/database-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Database Barang</h1>
            <form class="search-form" method="GET" action="">
                <input type="text" name="cari" placeholder="Cari ID / Nama Barang"
                       value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <?php 
        include 'koneksi.php';

        // Ganti 'NAMA_TABEL_BARANG' dengan nama tabel sebenarnya
        $tableName = 'barang';

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
            echo "<p class='not-found'>Error: " . $conn->error . "</p>";
        } elseif ($data->num_rows > 0) {
            echo "<table>";
            echo "<thead>";
            echo "<tr>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Deskripsi</th>
                    <th>Satuan</th>
                    <th>Aksi</th>
                  </tr>";
            echo "</thead>";
            echo "<tbody>";

            while ($row = $data->fetch_assoc()) {
                echo "<tr>
                        <td data-label='ID Barang'>{$row['id_barang']}</td>
                        <td data-label='Nama Barang'>{$row['nama_barang']}</td>
                        <td data-label='Deskripsi'>{$row['deskripsi']}</td>
                        <td data-label='Satuan'>{$row['satuan_unit']}</td>
                        <td data-label='Aksi' class='action-cell'>
                            <a href='edit.php?id={$row['id_barang']}' class='action-icon edit-icon' title='Edit'><i class='fas fa-edit'></i></a>
                            <a href='hapus.php?id={$row['id_barang']}' class='action-icon delete-icon' title='Hapus' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'><i class='fas fa-trash-alt'></i></a>
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