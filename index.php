<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login, jika tidak, arahkan ke halaman login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Ambil data user yang sedang login
$username = $_SESSION['username'];
$stmt_user = $conn->prepare("SELECT nama, profile_pic FROM user WHERE username = ?");
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_data = $result_user->fetch_assoc();
$stmt_user->close();

// === MENGAMBIL DATA AKTIVITAS DARI DATABASE SECARA REAL-TIME ===
// Buka kembali koneksi ke database karena sebelumnya sudah ditutup
$conn = new mysqli("localhost", "root", "", "inventori_sekolah_88");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// 1. Menghitung Total Barang Masuk dan Keluar
$total_masuk_query = $conn->query("SELECT SUM(qty) as total_masuk FROM aksi_barang WHERE aksi='Masuk'");
$total_masuk = $total_masuk_query->fetch_assoc()['total_masuk'] ?? 0;

$total_keluar_query = $conn->query("SELECT SUM(qty) as total_keluar FROM aksi_barang WHERE aksi='Keluar'");
$total_keluar = $total_keluar_query->fetch_assoc()['total_keluar'] ?? 0;

// 2. Menghitung Sisa Barang
$sisa_barang = $total_masuk - $total_keluar;

// 3. Menghitung Total Nama Barang (unique items)
$total_barang_query = $conn->query("SELECT COUNT(DISTINCT id_barang) as total_barang FROM barang");
$total_barang = $total_barang_query->fetch_assoc()['total_barang'] ?? 0;

// 4. Mengambil 5 aktivitas terbaru untuk tabel
$tabel_inventaris_query = $conn->query("
    SELECT 
        a.id_barang, 
        b.nama_barang, 
        a.qty, 
        a.satuan, 
        a.aksi AS status_aksi, 
        a.keterangan AS lokasi, 
        a.pic
    FROM aksi_barang a
    JOIN barang b ON a.id_barang = b.id_barang
    ORDER BY a.timestamp DESC
    LIMIT 5
");

// Tutup koneksi
$conn->close();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Inventory Sekolah</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">STM 88 DKI JAKARTA</div>
            </div>
            <nav class="sidebar-nav">
                <ul class="main-menu">
                    <li class="menu-item active">
                        <a href="index.php"><i class="fas fa-home"></i>Dashboard</a>
                    </li>
                    <li class="menu-item">
                        <li class="menu-item dropdown">
                        <a href="#"><i class="fas fa-database"></i>Master Data <i class="fas fa-chevron-down dropdown-arrow"></i></a>
                        <ul class="submenu">
                            <li><a href="record.php">Record Aktivitas</a></li>
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
                    <span class="breadcrumb">DASHBOARD</span>
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
                <div class="summary-cards-container">
                    <div class="card">
                        <div class="card-header">
                            <h4>Total Barang</h4>
                            <a href="database.php"><i class="fas fa-external-link-alt"></i></a>
                        </div>
                        <div class="card-body">
                            <span class="card-number"><?= $total_barang ?></span>
                            <span class="card-unit">Jenis</span>
                            <ul>
                                <li><i class="fas fa-circle active"></i>Barang Masuk: <span><?= $total_masuk ?></span></li>
                                <li><i class="fas fa-circle danger"></i>Barang Keluar: <span><?= $total_keluar ?></span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Total Barang Masuk</h4>
                            <a href="inbound.php"><i class="fas fa-external-link-alt"></i></a>
                        </div>
                        <div class="card-body">
                            <span class="card-number"><?= $total_masuk ?></span>
                            <span class="card-unit">Unit</span>
                            <ul>
                                <li><i class="fas fa-circle active"></i>Total Qty: <span><?= $total_masuk ?></span></li>
                                <li><i class="fas fa-circle warning"></i>Total Jenis: <span><?= $total_barang ?></span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Total Barang Keluar</h4>
                            <a href="outbound.php"><i class="fas fa-external-link-alt"></i></a>
                        </div>
                        <div class="card-body">
                            <span class="card-number"><?= $total_keluar ?></span>
                            <span class="card-unit">Unit</span>
                            <ul>
                                <li><i class="fas fa-circle active"></i>Total Qty: <span><?= $total_keluar ?></span></li>
                                <li><i class="fas fa-circle warning"></i>Total Jenis: <span><?= $total_barang ?></span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Sisa Barang</h4>
                            <a href="#"><i class="fas fa-external-link-alt"></i></a>
                        </div>
                        <div class="card-body">
                            <span class="card-number"><?= $sisa_barang ?></span>
                            <span class="card-unit">Unit</span>
                            <ul>
                                <li><i class="fas fa-circle primary"></i>Stok Tersedia: <span><?= $sisa_barang ?></span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="data-table-container">
                    <div class="table-header">
                        <h4>Aktivitas Terbaru</h4>
                        <a href="record.php" class="filter-btn" title="Lihat semua"><i class="fas fa-list"></i></a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID BARANG</th>
                                <th>NAMA BARANG</th>
                                <th>QTY</th>
                                <th>SATUAN</th>
                                <th>AKSI</th>
                                <th>KETERANGAN</th>
                                <th>PIC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($tabel_inventaris_query->num_rows > 0): ?>
                                <?php while ($row = $tabel_inventaris_query->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['id_barang']) ?></td>
                                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                        <td><?= htmlspecialchars($row['qty']) ?></td>
                                        <td><?= htmlspecialchars($row['satuan']) ?></td>
                                        <td class="status-cell">
                                            <span class="status-badge <?= strtolower($row['status_aksi']) ?>">
                                                <?= htmlspecialchars($row['status_aksi']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($row['lokasi']) ?></td>
                                        <td><?= htmlspecialchars($row['pic']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">Tidak ada data aktivitas terbaru.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>