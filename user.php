<?php
session_start();
// Cek apakah user sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include 'koneksi.php';

// Ambil semua data user dari database
$users_query = "SELECT id_user, username, nama, email, no_hp, status FROM user";
$users_data = $conn->query($users_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <main class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <span class="breadcrumb">Manajemen User</span>
                </div>
                <div class="top-bar-right">
                    <a href="#" class="icon-link"><i class="fas fa-bell"></i></a>
                    <a href="#" class="icon-link"><i class="fas fa-cog"></i></a>
                    <div class="user-profile">
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <img src="logo_user.png" alt="User Profile">
                    </div>
                </div>
            </header>
            
            <div class="content-wrapper">
                <div class="data-table-container">
                    <div class="table-header">
                        <h4>Daftar Pengguna</h4>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($users_data->num_rows > 0): ?>
                            <?php while ($user = $users_data->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['id_user']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['nama']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['no_hp']; ?></td>
                                <td><?php echo $user['status']; ?></td>
                                <td>
                                    </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7">Tidak ada user ditemukan.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>