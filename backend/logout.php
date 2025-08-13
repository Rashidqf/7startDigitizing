<?php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    // Unset all session variables
    session_unset(); 

    // Destroy the session
    session_destroy(); 

    echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
}
?>