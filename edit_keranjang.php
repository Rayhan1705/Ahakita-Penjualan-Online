<?php
// Establish database connection
$conn = mysqli_connect("localhost", "root", "", "ahakita");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $quantity = $_POST['quantity'];

    // Update the quantity in the database
    $query = "UPDATE keranjang SET quantity = '$quantity' WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        echo "Item updated successfully.";
    } else {
        echo "Error updating item: " . mysqli_error($conn);
    }
}

// Close database connection
mysqli_close($conn);
?>
