<?php
// Establish database connection
$conn = mysqli_connect("localhost", "root", "", "ahakita");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    // Delete the item from the database
    $query = "DELETE FROM keranjang WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        echo "produk barhasil dihapus.";
    } else {
        echo "Error deleting item: " . mysqli_error($conn);
    }
}

// Close database connection
mysqli_close($conn);
?>
