<?php
include 'koneksi.php';

$error = '';
$success = '';

// Proses form registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $status = 'user'; // Atur status default sebagai 'user'

    // Hash password sebelum disimpan ke database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah username atau email sudah ada
    $check_stmt = $conn->prepare("SELECT username, email FROM user WHERE username = ? OR email = ?");
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $error = 'Username atau email sudah terdaftar.';
    } else {
        // Masukkan data user baru ke database
        $insert_stmt = $conn->prepare("INSERT INTO user (username, nama, password, email, no_hp, status) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("ssssss", $username, $nama, $hashed_password, $email, $no_hp, $status);
        
        if ($insert_stmt->execute()) {
            $success = 'Pendaftaran berhasil! Anda akan diarahkan ke halaman login.';
            // Redirect ke halaman login setelah 3 detik
            header("refresh:3; url=login.php");
        } else {
            $error = 'Pendaftaran gagal: ' . $conn->error;
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - STM 88 DKI Jakarta</title>
    <link rel="stylesheet" href="style/loginstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .login-form-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            background-color: #fff;
        }
        .login-form-container h2 {
            margin-bottom: 1rem;
        }
        .signup-link {
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="left-panel">
            <div class="content">
                <h1>STM 88</h1>
                <h1>DKI JAKARTA</h1>
                <p class="subtitle">Data Inventory Manajemen Aset</p>
                <p class="description">
                    Selamat Datang di Sistem Data Inventory Manajemen Aset STM 88 DKI Jakarta.
                </p>
            </div>
        </div>

        <div class="right-panel">
            <div class="login-form-container">
                <h2>Daftar Akun Baru</h2>
                
                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>

                <form action="register.php" method="POST">
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-id-card"></i>
                        <input type="text" name="nama" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-phone"></i>
                        <input type="text" name="no_hp" placeholder="Nomor HP" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="login-button">DAFTAR</button>
                    <div class="signup-link">
                        Sudah punya akun? <a href="login.php">Masuk</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>