<?php
session_start();
include 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil data user dari database
    $stmt = $conn->prepare("SELECT password FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $hashed_password = $row['password'];
        // Verifikasi password dengan hash yang tersimpan
        if (password_verify($password, $hashed_password)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    } else {
        $error = 'Username atau password salah!';
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - STM 88 DKI Jakarta</title>
    <link rel="stylesheet" href="style/loginstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="left-panel">
            <div class="content">
                <h1>STM 88</h1>
                <h1>DKI JAKARTA</h1>
                <p class="subtitle">Data Inventory Manajemen Aset</p>
                <p class="description">
                    Selamat Datang di Sistem Data Inventory Manajemen Aset STM 88 DKI Jakarta. Platform ini merupakan sistem pendataan aset sekolah yang dirancang untuk mempermudah pengelolaan, pelacakan, dan pemeliharaan inventaris di lingkungan STM 88 DKI Jakarta.
                </p>
                <div class="info-section">
                    <h3>INFORMASI :</h3>
                    <div class="contact-info">
                        <span><i class="fas fa-globe"></i>www.stm88jakarta.com</span>
                        <span><i class="fas fa-phone"></i>+123 456 7890</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="right-panel">
            <div class="logo-container">
                <img src="assets/LOGO STM88.png" alt="Logo STM BB" class="logo">
            </div>
            <div class="login-form-container">
                <h2>User Log In</h2>
                
                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-options">
                        <label>
                            <input type="checkbox" name="remember_me"> Remember Me
                        </label>
                        <a href="#" class="forgot-password">Forgot Password</a>
                    </div>
                    <button type="submit" class="login-button">LOGIN</button>
                    <div class="signup-link">
                        Belum punya akun? <a href="register.php">Daftar sekarang</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>