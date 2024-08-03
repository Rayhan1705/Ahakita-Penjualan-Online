<?php
session_start(); // Start the session

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    die("User belum Login");
}

// mengambil user yang login
$user_id = $_SESSION['user_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ahakita";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mengambil data dari permintaan POST
$productName = $_POST['productName'];
$productPrice = $_POST['productPrice'];
$quantity = $_POST['quantity'];

// Menyiapkan pernyataan SQL untuk memasukkan data ke tabel keranjang
$sql = "INSERT INTO keranjang (user_id, product_name, product_price, quantity)
        VALUES ($user_id, '$productName', $productPrice, $quantity)";

if ($conn->query($sql) === TRUE) {
    echo "Produk berhasil ditambahkan ke karanjang";// Menampilkan pesan sukses
} else {
    echo "Error: " . $sql . "<br>" . $conn->error; // pesan gagal
}

$conn->close();
?>
