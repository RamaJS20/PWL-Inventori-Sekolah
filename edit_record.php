<?php
session_start();
include 'koneksi.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Fetch current user data for the header
$username = $_SESSION['username'];
$stmt_user = $conn->prepare("SELECT nama, profile_pic FROM user WHERE username = ?");
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_data = $result_user->fetch_assoc();
$stmt_user->close();

// Re-open connection as it was closed after fetching user data
$conn = new mysqli("localhost", "root", "", "inventori_sekolah_88");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$tableName = 'aksi_barang';
$id_aksi = '';
$timestamp = '';
$id_barang = '';
$nama_barang = '';
$qty = '';
$satuan = '';
$harga = '';
$total_harga = '';
$aksi = '';
$keterangan = '';
$pic = '';
$error = '';
$success = '';

// Proses saat form disubmit (menggunakan metode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_aksi = $_POST['id_aksi'];
    $id_barang = $_POST['id_barang'];
    $qty = $_POST['qty'];
    $satuan = $_POST['satuan'];
    $harga = $_POST['harga'];
    $keterangan = $_POST['keterangan'];
    $pic = $_POST['pic'];
    
    // Total harga dihitung ulang
    $total_harga = $qty * $harga;

    // Validasi sederhana
    if (empty($id_barang) || empty($qty) || empty($satuan) || empty($harga) || empty($pic)) {
        $error = "ID Barang, Qty, Satuan, Harga, dan PIC tidak boleh kosong.";
    } else {
        // Query untuk update data
        $query = "UPDATE $tableName SET 
                    id_barang = '$id_barang',
                    qty = '$qty',
                    satuan = '$satuan',
                    harga = '$harga',
                    total_harga = '$total_harga',
                    keterangan = '$keterangan',
                    pic = '$pic'
                  WHERE id_aksi = '$id_aksi'";

        if ($conn->query($query) === TRUE) {
            $success = "Record aktivitas berhasil diperbarui!";
            // Redirect untuk menghindari resubmission form
            header("Location: edit_record.php?id=$id_aksi");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

// Mengambil data record yang akan diedit (menggunakan metode GET)
if (isset($_GET['id'])) {
    $id_aksi_get = $conn->real_escape_string($_GET['id']);
    
    $query = "SELECT aksi.id_aksi, aksi.timestamp, aksi.id_barang, barang.nama_barang, 
                     aksi.qty, aksi.satuan, aksi.harga, aksi.total_harga, 
                     aksi.aksi, aksi.keterangan, aksi.pic 
              FROM aksi_barang AS aksi
              JOIN barang AS barang ON aksi.id_barang = barang.id_barang
              WHERE aksi.id_aksi = '$id_aksi_get'";
    
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_aksi = $row['id_aksi'];
        $timestamp = $row['timestamp'];
        $id_barang = $row['id_barang'];
        $nama_barang = $row['nama_barang'];
        $qty = $row['qty'];
        $satuan = $row['satuan'];
        $harga = $row['harga'];
        $total_harga = $row['total_harga'];
        $aksi = $row['aksi'];
        $keterangan = $row['keterangan'];
        $pic = $row['pic'];
    } else {
        $error = "Record tidak ditemukan.";
    }
} else {
    // Jika tidak ada ID di URL, kembali ke halaman utama
    header("Location: record.php");
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Record Aktivitas</title>
    <link rel="stylesheet" href="style/style.css">
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
                    <span class="breadcrumb">MASTER DATA > Record Aktivitas > Edit</span>
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
                <div class="form-edit-container">
                    <h1>Edit Record Aktivitas</h1>

                    <?php if ($error): ?>
                        <div class="message error"><?= $error ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="message success"><?= $success ?></div>
                    <?php endif; ?>

                    <form action="edit_record.php" method="POST" class="form-edit">
                        <input type="hidden" name="id_aksi" value="<?= htmlspecialchars($id_aksi) ?>">
                        
                        <div class="form-group">
                            <label for="timestamp">Timestamp:</label>
                            <input type="text" name="timestamp" id="timestamp" value="<?= htmlspecialchars($timestamp) ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label for="id_barang">ID Barang:</label>
                            <input type="text" name="id_barang" id="id_barang" value="<?= htmlspecialchars($id_barang) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nama_barang">Nama Barang:</label>
                            <input type="text" name="nama_barang" id="nama_barang" value="<?= htmlspecialchars($nama_barang) ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label for="aksi">Aksi:</label>
                            <input type="text" name="aksi" id="aksi" value="<?= htmlspecialchars($aksi) ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label for="qty">Qty:</label>
                            <input type="number" name="qty" id="qty" value="<?= htmlspecialchars($qty) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="satuan">Satuan:</label>
                            <input type="text" name="satuan" id="satuan" value="<?= htmlspecialchars($satuan) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="harga">Harga:</label>
                            <input type="number" name="harga" id="harga" value="<?= htmlspecialchars($harga) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="keterangan">Keterangan:</label>
                            <textarea name="keterangan" id="keterangan" rows="4"><?= htmlspecialchars($keterangan) ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="pic">PIC:</label>
                            <input type="text" name="pic" id="pic" value="<?= htmlspecialchars($pic) ?>" required>
                        </div>
                        
                        <div class="btn-action-group">
                            <button type="submit" class="btn-update">Update Record</button>
                            <a href="record.php" class="btn-back">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>