<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ahakita";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection to database failed: " . $conn->connect_error);
}

// Handle POST request for deleting user account
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if email parameter exists in session
    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];

        // Retrieve user_id based on email
        $select_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $select_stmt->bind_param("s", $email);
        $select_stmt->execute();
        $select_stmt->bind_result($user_id);
        $select_stmt->fetch();
        $select_stmt->close();

        if ($user_id) {
            // Delete all items in the cart associated with the user
            $delete_cart_stmt = $conn->prepare("DELETE FROM keranjang WHERE user_id = ?");
            $delete_cart_stmt->bind_param("i", $user_id);
            $delete_cart_stmt->execute();
            $delete_cart_stmt->close();

            // Prepare SQL statement for deleting user account
            $delete_user_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $delete_user_stmt->bind_param("i", $user_id);

            if ($delete_user_stmt->execute()) {
                // If deletion is successful, destroy session and redirect to homepage
                session_destroy(); // Destroy user session
                echo json_encode(array('success' => true));
                header('Location: HomePage Ahakita.html'); // Redirect to homepage
                exit;
            } else {
                // If deletion fails, log error and send error response
                $error_message = 'Delete failed: ' . htmlspecialchars($delete_user_stmt->error);
                error_log($error_message);
                echo json_encode(array('error' => 'Failed to delete account: ' . $delete_user_stmt->error));
            }

            // Close DELETE statement
            $delete_user_stmt->close();
        } else {
            echo json_encode(array('error' => 'User not found'));
        }
    } else {
        echo json_encode(array('error' => 'Missing session email'));
    }
} else {
    echo json_encode(array('error' => 'Unsupported request method'));
}

// Close database connection
$conn->close();

?>