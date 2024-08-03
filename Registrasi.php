<?php
// Pastikan file ini dipanggil dengan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sambungkan ke database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ahakita";

    // Buat koneksi
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Periksa koneksi
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Ambil data dari form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $mobile_number = $_POST['mobile_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi apakah password sama dengan konfirmasi password
    if ($password != $confirm_password) {
        // Redirect kembali ke halaman registrasi dengan pesan error
        header("Location: registrasi.php?error=Konfirmasi password tidak cocok. Silakan coba lagi.");
        exit;
    }

    // Query untuk memeriksa apakah email sudah terdaftar
    $check_email_query = "SELECT id FROM users WHERE email = ?";
    $stmt_check_email = $conn->prepare($check_email_query);
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();  
    $stmt_check_email->store_result();
    if ($stmt_check_email->num_rows > 0) {
        // Redirect kembali ke halaman registrasi dengan pesan error
        header("Location: registrasi.php?error=email_terdaftar");
        exit;
    }

    // Jika email belum terdaftar, lanjutkan dengan memasukkan data ke database
    $sql = "INSERT INTO users (name, email, address, mobile_number, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $email, $address, $mobile_number, $password);

    // Eksekusi statement INSERT
    if ($stmt->execute()) {
        // Registrasi berhasil, redirect ke halaman login
        header("Location: Login.html");
        exit;
    } else {
        // Jika terjadi error saat eksekusi query, tampilkan pesan error
        echo "Error: " . $sql . "<br>" . $con  n->error;
    }

    // Tutup statement dan koneksi
    $stmt->close();
    $conn->close();

} else {
    // Jika tidak dipanggil dengan metode POST, tidak ada yang dilakukan
    echo "Permintaan tidak valid.";
}
?>