<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Mengatur koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ahakita";

$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['email'])) {
    header("Location: Login.html");
    exit;
}

// Mendapatkan email pengguna dari sesi
$email = $_SESSION['email'];

// Persiapkan pernyataan SQL untuk mengambil data pengguna
$stmt = $conn->prepare("SELECT name, mobile_number, address, email FROM users WHERE email = ?");
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($name, $mobile_number, $address, $email);

// Ambil hasil dari kueri SQL
if ($stmt->fetch()) {
    // Menyimpan data ke dalam array asosiatif
    $user_data = array(
        'name' => $name,
        'mobile_number' => $mobile_number,
        'address' => $address,
        'email' => $email
    );
    
    // Mengatur header untuk mengembalikan data sebagai JSON
    header('Content-Type: application/json');
    
    // Mengembalikan data sebagai JSON
    echo json_encode($user_data);
} else {
    echo json_encode(array('error' => 'No user found with this email'));
}

// Tutup pernyataan dan koneksi
$stmt->close();
$conn->close();
?>
