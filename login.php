<?php
session_start(); // Memulai sesi untuk menyimpan informasi login pengguna
// Mengatur koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ahakita";

$conn = new mysqli($servername, $username, $password, $dbname); // Membuat koneksi ke database

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error); // Menghentikan eksekusi jika koneksi gagal
}

// Memeriksa apakah form dikirim via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Persiapkan pernyataan SQL untuk mengambil password sesuai dengan email yang diberikan
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); // Bind parameter email ke statement
    $stmt->execute();
    $stmt->store_result(); // Simpan hasil query

    // Memeriksa apakah email sudah terdaftar
    if ($stmt->num_rows == 0) {
        // Redirect kembali ke halaman login dengan pesan error jika email tidak terdaftar
        header("Location: login.php?error=Email belum terdaftar");
        exit;
    }  

    // Bind result
    $stmt->bind_result($user_id, $stored_password); 
    $stmt->fetch(); // Mengambil hasil query

    // Verifikasi password
    if ($password == $stored_password) { // Memeriksa apakah password yang dimasukkan sesuai dengan password yang tersimpan
        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $email;

        header("Location: homepage ahakita dan menu profil.html"); // Redirect ke halaman beranda dan profil jika login berhasil
        exit;
    } else {
        // Redirect kembali ke halaman login dengan pesan error jika password salah
        header("Location: login.php?error=Password atau email salah");
        exit;
    }
}
// Menutup koneksi
$conn->close(); // Menutup koneksi ke database
?>