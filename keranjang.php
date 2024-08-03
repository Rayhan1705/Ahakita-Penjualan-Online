<?php
// Memulai sesi untuk mengakses data user_id dari sesi
session_start();

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    die("User not authenticated"); // Menangani akses tanpa otentikasi
}

// untuk koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "ahakita");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Mengambil user_id dari sesi
$user_id = $_SESSION['user_id'];

// Menyiapkan query untuk mengambil item keranjang pengguna yang sedang login dari database
$query = "SELECT * FROM keranjang WHERE user_id = ?"; // Sesuaikan query untuk memfilter berdasarkan user_id
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Memeriksa apakah query berhasil dieksekusi
if ($result) {
      // Memeriksa apakah ada item di keranjang
    if (mysqli_num_rows($result) > 0) {
        // Menampilkan tabel HTML dengan header kolom
        echo "<table class='table table-striped'>
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>";
        
        // Mengambil dan menampilkan setiap item dalam keranjang
        while ($row = mysqli_fetch_assoc($result)) {
            $productId = $row['id'];
            $productName = $row['product_name'];
            $productPrice = $row['product_price'];
            $quantity = $row['quantity'];
            $subtotal = $productPrice * $quantity;

            // Menampilkan setiap baris item dalam tabel
            echo "<tr class='cart-item'>
                    <td>{$productName}</td>
                    <td class='item-price'>Rp " . number_format($productPrice, 0, ',', '.') . "</td>
                    <td class='item-quantity'>Quantity: {$quantity}</td>
                    <td>Rp " . number_format($subtotal, 0, ',', '.') . "</td>
                    <td>
                        <button class='btn btn-primary edit-btn' data-id='{$productId}'>Edit</button>
                        <button class='btn btn-danger delete-btn' data-id='{$productId}'>Hapus</button>
                    </td>
                  </tr>";
        }

        echo "  </tbody>
              </table>";
    } else {
        echo '<p>Keranjang belanja Anda kosong.</p>';
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

// Close
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
