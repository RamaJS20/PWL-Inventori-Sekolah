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

// Tutup koneksi setelah selesai
$conn->close();

$team_members = [
    ['name' => 'Rama.js', 'role' => 'Project Manager', 'icon' => 'fas fa-briefcase'],
    ['name' => 'Caidenrev', 'role' => 'Lead Developer', 'icon' => 'fas fa-code'],
    ['name' => 'Adimas', 'role' => 'UI/UX Designer', 'icon' => 'fas fa-paint-brush'],
    ['name' => 'Amelia', 'role' => 'Backend Developer', 'icon' => 'fas fa-server'],
    ['name' => 'Fadil', 'role' => 'Database Admin', 'icon' => 'fas fa-database'],
    ['name' => 'Ghifari', 'role' => 'Frontend Developer', 'icon' => 'fas fa-laptop-code'],
    ['name' => 'Imanuel', 'role' => 'Fullstack Developer', 'icon' => 'fas fa-check-double'],
    ['name' => 'Azima', 'role' => 'SQL Key', 'icon' => 'fas fa-pen-nib'],
    ['name' => 'Nadim', 'role' => 'System Analyst', 'icon' => 'fas fa-chart-line'],
];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Sekolah - STM 88 DKI Jakarta</title>
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
                    <li class="menu-item active">
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
                    <span class="breadcrumb">TENTANG SEKOLAH</span>
                </div>
                <div class="top-bar-right">
                    <a href="#" class="icon-link"><i class="fas fa-bell"></i></a>
                    <a href="profile.php" class="icon-link" title="Pengaturan Profil"><i class="fas fa-cog"></i></a>
                    <div class="user-profile">
                        <span><?php echo htmlspecialchars($user_data['nama']); ?></span>
                        <img src="assets/<?php echo htmlspecialchars($user_data['profile_pic']); ?>" alt="User Profile">
                        <a href="logout.php" title="Logout" class="logout-link"><i class="fas fa-sign-out-alt"></i></a>
                    </div>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="about-container">
                    <h1 class="about-title">Sekilas Tentang STM 88 DKI Jakarta</h1>
                    <p>
                        STM 88 DKI Jakarta adalah salah satu institusi pendidikan kejuruan yang telah lama berdedikasi dalam mencetak generasi-generasi muda yang siap terjun ke dunia industri. Sejak didirikan, sekolah ini memiliki komitmen kuat untuk menyediakan pendidikan berbasis praktik yang relevan dengan perkembangan teknologi dan kebutuhan pasar kerja.
                    </p>
                    <p>
                        Dengan kurikulum yang terus diperbarui dan fasilitas yang memadai, kami menawarkan program keahlian di berbagai bidang, mulai dari teknologi informasi, otomotif, hingga kelistrikan. Setiap program dirancang untuk membekali siswa dengan keterampilan teknis yang solid, etos kerja profesional, dan kemampuan beradaptasi.
                    </p>
                    <p>
                        Selain fokus pada keunggulan akademik dan kejuruan, STM 88 juga menekankan pentingnya pembentukan karakter. Melalui berbagai kegiatan ekstrakurikuler dan program pengembangan diri, kami mendorong siswa untuk menjadi individu yang bertanggung jawab, kreatif, dan memiliki semangat gotong royong.
                    </p>
                    <p>
                        Kami percaya bahwa pendidikan adalah investasi terbaik untuk masa depan. Oleh karena itu, STM 88 DKI Jakarta terus berupaya menjadi mitra terpercaya bagi para siswa dan orang tua dalam mewujudkan impian dan ambisi mereka.
                    </p>
                </div>
                
                <hr style="margin: 40px 0;">

                <div class="team-development-section">
                    <h2 class="team-title">Tim Pengembang Sistem Inventory</h2>
                    <div class="team-members-grid">
                        <?php foreach ($team_members as $member): ?>
                        <div class="team-member-card">
                            <div class="team-member-icon">
                                <i class="<?= $member['icon'] ?>"></i>
                            </div>
                            <h3><?= $member['name'] ?></h3>
                            <p><?= $member['role'] ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>