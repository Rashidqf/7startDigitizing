<?php
session_start();

header('Content-Type: application/json');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    echo json_encode(['loggedIn' => true, 'user_id' => $_SESSION['user_id'], 'email' => $_SESSION['email']]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>
