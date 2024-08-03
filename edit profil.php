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

// Handle POST request for updating user profile
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get JSON input data
    $input = json_decode(file_get_contents('php://input'), true);

    // Check if email parameter exists
    if (isset($input['email'])) {
        $email = $input['email'];

        // Extract updated data (use isset to handle optional fields)
        $new_name = isset($input['name']) ? $input['name'] : null;
        $new_mobile_number = isset($input['mobile_number']) ? $input['mobile_number'] : null;
        $new_address = isset($input['address']) ? $input['address'] : null;
        $new_email = isset($input['email']) ? $input['email'] : null;

        // Prepare SQL statement for updating user profile
        $update_stmt = $conn->prepare("UPDATE users SET name=?, mobile_number=?, address=?, email=? WHERE email=?");
        if ($update_stmt === false) {
            $error_message = 'Prepare failed: ' . htmlspecialchars($conn->error);
            error_log($error_message);
            echo json_encode(array('error' => $error_message));
            exit;
        }

        // Bind parameters and execute update statement
        $update_stmt->bind_param("sssss", $new_name, $new_mobile_number, $new_address, $new_email, $email);
        if ($update_stmt->execute()) {
            echo json_encode(array('success' => true));
        } else {
            $error_message = 'Update failed: ' . htmlspecialchars($update_stmt->error);
            error_log($error_message);
            echo json_encode(array('error' => 'Failed to update profile: ' . $update_stmt->error));
        }

        // Close update statement
        $update_stmt->close();

    } else {
        echo json_encode(array('error' => 'Missing email parameter'));
    }

} elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Handle GET request for fetching user profile data
    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];

        // Prepare SQL statement for fetching user profile data
        $stmt = $conn->prepare("SELECT name, mobile_number, address, email FROM users WHERE email = ?");
        if ($stmt === false) {
            $error_message = 'Prepare failed: ' . htmlspecialchars($conn->error);
            error_log($error_message);
            echo json_encode(array('error' => $error_message));
            exit;
        }

        // Bind parameter and execute statement
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($name, $mobile_number, $address, $email);

        // Fetch and return user profile data as JSON
        if ($stmt->fetch()) {
            $user_data = array(
                'name' => $name,
                'mobile_number' => $mobile_number,
                'address' => $address,
                'email' => $email
            );
            echo json_encode($user_data);
        } else {
            echo json_encode(array('error' => 'User not found'));
        }

        // Close SELECT statement
        $stmt->close();

    } else {
        echo json_encode(array('error' => 'Missing session email'));
    }

} else {
    echo json_encode(array('error' => 'Unsupported request method'));
}

// Close database connection
$conn->close();
?>