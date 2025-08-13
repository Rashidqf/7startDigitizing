<?php
session_start(); 
include '../dbConnect.php';

header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $requiredFields = ['email', 'password'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => "The field '$field' is required."]);
            exit;
        }
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare query to check if the user exists
    $sql = "SELECT id, email, password FROM users WHERE email = ? AND is_admin = 0";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Error preparing the query.']);
        exit;
    }

    $stmt->bind_param("s", $email); 
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        $stmt->close();
        exit;
    }

    // Bind result
    $stmt->bind_result($userId, $storedEmail, $storedPassword);
    $stmt->fetch();

    // Verify the password
    if (password_verify($password, $storedPassword)) {
        // Password is correct - set session variables
        $_SESSION['user_id'] = $userId;
        $_SESSION['email'] = $storedEmail;

        echo json_encode(['success' => true, 'message' => 'Login successful!', 'user_id' => $userId]);
    } else {
        // Invalid password
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
