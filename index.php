<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Inventory Sekolah</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php
// PHP untuk mensimulasikan data
$data_barang_elektronik = [
    'total' => 253,
    'tersedia' => 201,
    'rusak' => 30
];

$data_barang_rumah_tangga = [
    'total' => 70,
    'tersedia' => 74, // Contoh data yang lebih besar dari total, perlu penyesuaian di data nyata
    'rusak' => 23
];

$data_barang_pinjaman_aktif = [
    'total' => 102,
    'sudah_dikembalikan' => 80,
    'belum_dikembalikan' => 22
];

$data_barang_rusak = [
    'total' => 23,
    'menunggu_perbaikan' => 16,
    'proses_perbaikan' => 9
];



$tabel_inventaris = [
    ['KR001', 'Kursi Siswa', 950, 'Unit', 'Tersedia', 'Gudang', 'Renaldi'],
    ['KR001', 'Kursi Siswa', 950, 'Unit', 'Tersedia', 'Gudang', 'Renaldi'],
    ['KR001', 'Kursi Siswa', 950, 'Unit', 'Tersedia', 'Gudang', 'Renaldi'],
    ['KR001', 'Kursi Siswa', 950, 'Unit', 'Tersedia', 'Gudang', 'Renaldi'],
    ['KR001', 'Kursi Siswa', 500, 'Unit', 'Tersedia', 'Gudang', 'Renaldi'],
];

?>

    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">STM 88 DKI JAKARTA</div>
            </div>
            <nav class="sidebar-nav">
                <ul class="main-menu">
                    <li class="menu-item active">
                        <a href="#"><i class="fas fa-home"></i>Dashboard</a>
                    </li>
                    <li class="menu-item">
                        <li class="menu-item dropdown">
                        <a href="#"><i class="fas fa-database"></i>Master Data <i class="fas fa-chevron-down dropdown-arrow"></i></a>
                        <ul class="submenu">
                            <li><a href="summary.php">Summary Barang</a></li>
                            <li><a href="#">Database Barang</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="menu-separator"></div>
                <h3>ASET SEKOLAH</h3>
                <ul class="main-menu">
                    <li class="menu-item dropdown">
                        <a href="#"><i class="fas fa-couch"></i>Sarana Prasarana <i class="fas fa-chevron-down dropdown-arrow"></i></a>
                        <ul class="submenu">
                            <li><a href="#">Barang Masuk</a></li>
                            <li><a href="#">Barang Keluar</a></li>
                            <li><a href="#">Barang Rusak</a></li>
                        </li>
                </ul>
                <div class="menu-separator"></div>
                <h3>OTHER</h3>
                <ul class="main-menu">
                    <li class="menu-item">
                        <a href="#"><i class="fas fa-user-friends"></i>User</a>
                    </li>
                    <li class="menu-item">
                        <a href="#"><i class="fa-solid fa-school"></i>Tentang Sekolah</a>
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
                    <span class="breadcrumb">DASHBOARD</span>
                </div>
                <div class="top-bar-right">
                    <a href="#" class="icon-link"><i class="fas fa-bell"></i></a>
                    <a href="#" class="icon-link"><i class="fas fa-cog"></i></a>
                    <div class="user-profile">
                        <span>Renaldi</span>
                        <img src="logo_user.png" alt="User Profile">
                        
                    </div>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="summary-cards-container">
                    <div class="card">
                        <div class="card-header">
                            <h4>Barang Elektronik</h4>
                            <a href="#"><i class="fas fa-external-link-alt"></i></a>
                        </div>
                        <div class="card-body">
                            <span class="card-number"><?php echo $data_barang_elektronik['total']; ?></span>
                            <span class="card-unit">Unit</span>
                            <ul>
                                <li><i class="fas fa-circle active"></i>Barang Tersedia: <span><?php echo $data_barang_elektronik['tersedia']; ?></span></li>
                                <li><i class="fas fa-circle danger"></i>Barang Rusak: <span><?php echo $data_barang_elektronik['rusak']; ?></span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Barang Rumah Tangga</h4>
                            <a href="#"><i class="fas fa-external-link-alt"></i></a>
                        </div>
                        <div class="card-body">
                            <span class="card-number"><?php echo $data_barang_rumah_tangga['total']; ?></span>
                            <span class="card-unit">Item</span>
                            <ul>
                                <li><i class="fas fa-circle active"></i>Barang Tersedia: <span><?php echo $data_barang_rumah_tangga['tersedia']; ?></span></li>
                                <li><i class="fas fa-circle danger"></i>Barang Rusak: <span><?php echo $data_barang_rumah_tangga['rusak']; ?></span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Barang Pinjaman Aktif</h4>
                            <a href="#"><i class="fas fa-external-link-alt"></i></a>
                        </div>
                        <div class="card-body">
                            <span class="card-number"><?php echo $data_barang_pinjaman_aktif['total']; ?></span>
                            <span class="card-unit">Unit</span>
                            <ul>
                                <li><i class="fas fa-circle active"></i>Sudah dikembalikan: <span><?php echo $data_barang_pinjaman_aktif['sudah_dikembalikan']; ?></span></li>
                                <li><i class="fas fa-circle danger"></i>Belum dikembalikan: <span><?php echo $data_barang_pinjaman_aktif['belum_dikembalikan']; ?></span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Barang Rusak</h4>
                            <a href="#"><i class="fas fa-external-link-alt"></i></a>
                        </div>
                        <div class="card-body">
                            <span class="card-number"><?php echo $data_barang_rusak['total']; ?></span>
                            <span class="card-unit">Unit</span>
                            <ul>
                                <li><i class="fas fa-circle warning"></i>Menunggu perbaikan: <span><?php echo $data_barang_rusak['menunggu_perbaikan']; ?></span></li>
                                <li><i class="fas fa-circle primary"></i>Proses Perbaikan: <span><?php echo $data_barang_rusak['proses_perbaikan']; ?></span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                    

                <div class="data-table-container">
                    <div class="table-header">
                        <h4>Data Aktivitas Inventaris Sekolah</h4>
                        <a href="#" class="filter-btn"><i class="fas fa-filter"></i></a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>ID BARANG</th>
                                <th>NAMA BARANG</th>
                                <th>QTY</th>
                                <th>SATUAN</th>
                                <th>STATUS</th>
                                <th>LOKASI</th>
                                <th>PIC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($tabel_inventaris as $row) : ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row[0]; ?></td>
                                <td><?php echo $row[1]; ?></td>
                                <td><?php echo $row[2]; ?></td>
                                <td><?php echo $row[3]; ?></td>
                                <td class="status-cell"><span class="status-badge <?php echo strtolower($row[4]); ?>"><?php echo $row[4]; ?></span></td>
                                <td><?php echo $row[5]; ?></td>
                                <td><?php echo $row[6]; ?></td>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
</html>