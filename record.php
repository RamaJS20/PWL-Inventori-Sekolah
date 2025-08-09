<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Ambil data user yang sedang login untuk ditampilkan di header
$username = $_SESSION['username'];
$stmt_user = $conn->prepare("SELECT nama, profile_pic FROM user WHERE username = ?");
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_data = $result_user->fetch_assoc();
$stmt_user->close();

$tableName = 'aksi_barang';

// Buat query untuk mengambil data record aktivitas
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Aktivitas</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/record-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">STM 88 DKI JAKARTA</div>
            </div>
            <nav class="sidebar-nav">
                <ul class="main-menu">
                    <li class="menu-item">
                        <a href="index.php"><i class="fas fa-home"></i>Dashboard</a>
                    </li>
                    <li class="menu-item active dropdown">
                        <a href="#"><i class="fas fa-database"></i>Master Data <i class="fas fa-chevron-down dropdown-arrow"></i></a>
                        <ul class="submenu">
                            <li class="active"><a href="record.php">Record Aktivitas</a></li>
                            <li><a href="database.php">Database Barang</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="menu-separator"></div>
                <h3>ASET SEKOLAH</h3>
                <ul class="main-menu">
                    <li class="menu-item dropdown">
                        <a href="#"><i class="fas fa-couch"></i>Sarana Prasarana <i class="fas fa-chevron-down dropdown-arrow"></i></a>
                        <ul class="submenu">
                            <li><a href="inbound.php">Barang Masuk</a></li>
                            <li><a href="outbound.php">Barang Keluar</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="menu-separator"></div>
                <h3>OTHER</h3>
                <ul class="main-menu">
                    <li class="menu-item">
                        <a href="user.php"><i class="fas fa-user-friends"></i>User</a>
                    </li>
                    <li class="menu-item">
                        <a href="sekolah.php"><i class="fa-solid fa-school"></i>Tentang Sekolah</a>
                    </li>
                    
                </ul>
            </nav>
            <div class="sidebar-footer">
                <p>STM 88 DKI JAKARTA</p>
                <p>&copy; 2025</p>
            </div>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <span class="breadcrumb">MASTER DATA > Record Aktivitas</span>
                </div>
                <div class="top-bar-right">
                    
                    <a href="profile.php" class="icon-link" title="Pengaturan Profil"><i class="fas fa-cog"></i></a>
                    <div class="user-profile">
                        <span><?php echo htmlspecialchars($user_data['nama']); ?></span>
                        <img src="assets/<?php echo htmlspecialchars($user_data['profile_pic']); ?>" alt="User Profile">
                        <a href="logout.php" title="Logout" class="logout-link"><i class="fas fa-sign-out-alt"></i></a>
                    </div>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="data-table-container">
                    <div class="header">
                        <h1>Record Aktivitas</h1>
                        <form class="search-form" method="GET" action="">
                            <input type="text" name="cari" placeholder="Cari ID / Nama Barang / Aksi / PIC"
                                value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>">
                            <button type="submit">Cari</button>
                        </form>
                    </div>

                    <?php 
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
            </div>
        </main>
    </div>
</body>
<script src="script.js"></script>
</html>