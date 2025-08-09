<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login, jika tidak, arahkan ke halaman login
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

// Buka kembali koneksi setelah ditutup
$conn = new mysqli("localhost", "root", "", "inventori_sekolah_88");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Barang Keluar</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/outbound-style.css">
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
                    <li class="menu-item active dropdown">
                        <a href="#"><i class="fas fa-couch"></i>Sarana Prasarana <i class="fas fa-chevron-down dropdown-arrow"></i></a>
                        <ul class="submenu">
                            <li><a href="inbound.php">Barang Masuk</a></li>
                            <li class="active"><a href="outbound.php">Barang Keluar</a></li>
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
                    <span class="breadcrumb">SARANA PRASARANA > Barang Keluar</span>
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
                <div class="form-container">
                    <h2>Form Barang Keluar</h2>

                    <form method="POST" action="" class="form-outbound">
                        <div class="form-group">
                            <label for="id_aksi">ID Aksi</label>
                            <input type="text" name="id_aksi" id="id_aksi" value="<?= $idAksiBaru ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="id_barang">ID Barang</label>
                            <select name="id_barang" id="id_barang" required>
                                <option value="">-- Pilih Barang --</option>
                                <?php
                                $barang = $conn->query("SELECT * FROM barang");
                                while ($row = $barang->fetch_assoc()) {
                                    echo "<option value='{$row['id_barang']}'>{$row['id_barang']} - {$row['nama_barang']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="qty">Qty</label>
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
                            <input type="text" name="keterangan" id="keterangan">
                        </div>
                        
                        <div class="form-group">
                            <label for="pic">PIC</label>
                            <input type="text" name="pic" id="pic" required>
                        </div>
                        
                        <button type="submit" class="btn-submit">Simpan</button>
                    </form>
                </div>

                <div class="data-table-container">
                    <h3>Daftar Barang Keluar</h3>
                    <?php
                    $query = "SELECT aksi.*, barang.nama_barang 
                              FROM aksi_barang AS aksi 
                              JOIN barang AS barang ON aksi.id_barang = barang.id_barang 
                              WHERE aksi.aksi = 'Keluar' 
                              ORDER BY aksi.timestamp DESC";

                    $data = $conn->query($query);
                    ?>
                    <?php if ($data->num_rows > 0): ?>
                    <table>
                        <thead>
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
                        </thead>
                        <tbody>
                            <?php while ($row = $data->fetch_assoc()): ?>
                            <tr>
                                <td data-label="ID Aksi"><?= $row['id_aksi'] ?></td>
                                <td data-label="ID Barang"><?= $row['id_barang'] ?></td>
                                <td data-label="Nama Barang"><?= $row['nama_barang'] ?></td>
                                <td data-label="Qty"><?= $row['qty'] ?></td>
                                <td data-label="Harga"><?= number_format($row['harga']) ?></td>
                                <td data-label="Total"><?= number_format($row['total_harga']) ?></td>
                                <td data-label="Keterangan"><?= $row['keterangan'] ?></td>
                                <td data-label="PIC"><?= $row['pic'] ?></td>
                                <td data-label="Waktu"><?= $row['timestamp'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <p class="not-found">Tidak ada data barang keluar ditemukan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>