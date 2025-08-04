<?php

// Konfigurasi Database
$servername = "localhost"; // 
$username = "root";       // 
$password = "";           // 
$dbname = "inventori_sekolah_88"; // 

// Buat koneksi baru
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    // Jika koneksi gagal, hentikan eksekusi script dan tampilkan pesan error
    die("Koneksi gagal: " . $conn->connect_error);
}

// Opsi: Set karakter set menjadi UTF8 agar mendukung berbagai karakter
$conn->set_charset("utf8");

// Jika koneksi berhasil, Anda bisa melanjutkan dengan kode selanjutnya.
// Di file lain, Anda bisa menyertakan file ini dengan `include 'connect.php';`
?>