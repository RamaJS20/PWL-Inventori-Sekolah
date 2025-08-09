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

$tableName = 'barang';
$id_barang = '';
$nama_barang = '';
$deskripsi = '';
$satuan_unit = '';
$error = '';
$success = '';

// Process form submission (using POST method)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_barang_old = $_POST['id_barang_old'];
    $id_barang = $_POST['id_barang'];
    $nama_barang = $_POST['nama_barang'];
    $deskripsi = $_POST['deskripsi'];
    $satuan_unit = $_POST['satuan_unit'];

    // Simple validation
    if (empty($id_barang) || empty($nama_barang) || empty($satuan_unit)) {
        $error = "ID Barang, Nama Barang, dan Satuan tidak boleh kosong.";
    } else {
        // Query to update data
        $query = "UPDATE $tableName SET 
                    id_barang = '$id_barang',
                    nama_barang = '$nama_barang',
                    deskripsi = '$deskripsi',
                    satuan_unit = '$satuan_unit'
                  WHERE id_barang = '$id_barang_old'";

        if ($conn->query($query) === TRUE) {
            $success = "Data barang berhasil diperbarui!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

// Fetch item data to be edited (using GET method)
if (isset($_GET['id'])) {
    $id_barang_get = $conn->real_escape_string($_GET['id']);
    $query = "SELECT * FROM $tableName WHERE id_barang = '$id_barang_get'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_barang = $row['id_barang'];
        $nama_barang = $row['nama_barang'];
        $deskripsi = $row['deskripsi'];
        $satuan_unit = $row['satuan_unit'];
    } else {
        $error = "Data tidak ditemukan.";
    }
} else {
    // If no ID in the URL, redirect to the main page
    header("Location: database.php");
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .form-edit-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            background-color: var(--white-color);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .form-edit {
            display: grid;
            gap: 20px;
            grid-template-columns: 1fr;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group input[type="text"],
        .form-group textarea {
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 14px;
        }
        .btn-action-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-update {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: var(--white-color);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }
        .btn-update:hover {
            background-color: #0b5ed7;
        }
        .btn-back {
            background-color: var(--secondary-color);
            color: var(--white-color);
            padding: 10px 20px;
            border-radius: 5px;
            text-align: center;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
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
                            <li><a href="record.php">Record Aktivitas</a></li>
                            <li class="active"><a href="database.php">Database Barang</a></li>
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
                    <span class="breadcrumb">MASTER DATA > Database Barang > Edit</span>
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
                    <h1>Edit Data Barang</h1>

                    <?php if ($error): ?>
                        <div class="message error"><?= $error ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="message success"><?= $success ?></div>
                    <?php endif; ?>

                    <form action="edit.php" method="POST" class="form-edit">
                        <input type="hidden" name="id_barang_old" value="<?= htmlspecialchars($id_barang) ?>">
                        
                        <div class="form-group">
                            <label for="id_barang">ID Barang:</label>
                            <input type="text" name="id_barang" id="id_barang" value="<?= htmlspecialchars($id_barang) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nama_barang">Nama Barang:</label>
                            <input type="text" name="nama_barang" id="nama_barang" value="<?= htmlspecialchars($nama_barang) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi:</label>
                            <textarea name="deskripsi" id="deskripsi" rows="4"><?= htmlspecialchars($deskripsi) ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="satuan_unit">Satuan:</label>
                            <input type="text" name="satuan_unit" id="satuan_unit" value="<?= htmlspecialchars($satuan_unit) ?>" required>
                        </div>
                        
                        <div class="btn-action-group">
                            <button type="submit" class="btn-update">Update Data</button>
                            <a href="database.php" class="btn-back">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>