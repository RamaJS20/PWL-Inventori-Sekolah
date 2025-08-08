<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Buka kembali koneksi setelah ditutup jika diperlukan
$conn = new mysqli("localhost", "root", "", "inventori_sekolah_88");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$error = '';
$success = '';

// Mengambil data user saat ini
$stmt = $conn->prepare("SELECT nama, email, no_hp, profile_pic FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $new_password = $_POST['new_password'];

    $update_query = "UPDATE user SET nama = ?, email = ?, no_hp = ?";
    $params = [$nama, $email, $no_hp];
    $types = "sss";

    // Jika ada password baru, hash dan tambahkan ke query
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query .= ", password = ?";
        $params[] = $hashed_password;
        $types .= "s";
    }

    // Proses upload foto profil
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target_dir = "assets/";
        $file_extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('profile_') . '.' . $file_extension;
        $target_file = $target_dir . basename($new_filename);
        $image_file_type = strtolower($file_extension);

        // Validasi file
        $check = getimagesize($_FILES['profile_pic']['tmp_name']);
        if ($check === false) {
            $error = "File bukan gambar.";
        } else if ($_FILES['profile_pic']['size'] > 500000) { // 500KB
            $error = "Ukuran file terlalu besar.";
        } else if ($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg" && $image_file_type != "gif") {
            $error = "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        } else {
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
                // Hapus foto profil lama jika bukan default
                if ($user['profile_pic'] !== 'default-user.png' && file_exists($target_dir . $user['profile_pic'])) {
                    unlink($target_dir . $user['profile_pic']);
                }
                
                $update_query .= ", profile_pic = ?";
                $params[] = $new_filename;
                $types .= "s";
            } else {
                $error = "Maaf, ada error saat mengunggah file Anda.";
            }
        }
    }

    if (empty($error)) {
        $update_query .= " WHERE username = ?";
        $params[] = $username;
        $types .= "s";
        
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bind_param($types, ...$params);

        if ($stmt_update->execute()) {
            $success = "Profil berhasil diperbarui!";
            
            // Ambil ulang data user untuk ditampilkan
            $stmt = $conn->prepare("SELECT nama, email, no_hp, profile_pic FROM user WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

        } else {
            $error = "Gagal memperbarui profil: " . $conn->error;
        }
        $stmt_update->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style/profile.css">
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
                    <div class="menu-separator"></div>
                    <h3>ASET SEKOLAH</h3>
                    <li class="menu-item dropdown">
                        <a href="#"><i class="fas fa-couch"></i>Sarana Prasarana <i class="fas fa-chevron-down dropdown-arrow"></i></a>
                        <ul class="submenu">
                            <li><a href="inbound.php">Barang Masuk</a></li>
                            <li><a href="outbound.php">Barang Keluar</a></li>
                        </ul>
                    </li>
                    <div class="menu-separator"></div>
                    <h3>OTHER</h3>
                    <li class="menu-item">
                        <a href="user.php"><i class="fas fa-user-friends"></i>User</a>
                    </li>
                    <li class="menu-item">
                        <a href="sekolah.php"><i class="fa-solid fa-school"></i>Tentang Sekolah</a>
                    </li>
                    <li class="menu-item">
                        <a href="#"><i class="fas fa-file-alt"></i>Report</a>
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
                    <span class="breadcrumb">Pengaturan Profil</span>
                </div>
                <div class="top-bar-right">
                    <a href="#" class="icon-link"><i class="fas fa-bell"></i></a>
                    <a href="#" class="icon-link" title="Pengaturan Profil"><i class="fas fa-cog"></i></a>
                    <div class="user-profile">
                        <span><?= htmlspecialchars($user['nama']) ?></span>
                        <img src="assets/<?= htmlspecialchars($user['profile_pic']) ?>" alt="User Profile">
                        <a href="logout.php" title="Logout" class="logout-link"><i class="fas fa-sign-out-alt"></i></a>
                    </div>
                </div>
            </header>
            
            <div class="content-wrapper">
                <div class="form-edit-container">
                    <h2>Pengaturan Profil</h2>
                    <?php if ($error): ?><div class="message error"><?= $error ?></div><?php endif; ?>
                    <?php if ($success): ?><div class="message success"><?= $success ?></div><?php endif; ?>

                    <form action="profile.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group-profile">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                        </div>
                        <div class="form-group-profile">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="form-group-profile">
                            <label for="no_hp">Nomor HP</label>
                            <input type="text" id="no_hp" name="no_hp" value="<?= htmlspecialchars($user['no_hp']) ?>" required>
                        </div>
                        <div class="form-group-profile">
                            <label for="new_password">Password Baru</label>
                            <input type="password" id="new_password" name="new_password">
                            <small>Kosongkan jika tidak ingin mengubah password.</small>
                        </div>
                        <div class="form-group-profile">
                            <label>Foto Profil</label>
                            <div class="upload-btn-wrapper">
                                <button class="btn-upload">Pilih File</button>
                                <input type="file" id="profile_pic" name="profile_pic" />
                                <span id="file-chosen">Tidak ada file dipilih</span>
                            </div>
                            <div class="current-profile-pic">
                                <img src="assets/<?= htmlspecialchars($user['profile_pic']) ?>" alt="Foto Profil Saat Ini">
                            </div>
                        </div>
                        <div class="btn-action-group">
                            <button type="submit" class="btn-primary">Simpan Perubahan</button>
                            <a href="index.php" class="btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>