<?php
session_start();
include '../../dbConnect.php'; 

header('Content-Type: application/json'); 

//
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}


$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;


$usersPerPage = 10;


$offset = ($page - 1) * $usersPerPage;


$sql = "SELECT id, username, full_name, email, phone_number, company_name, country, state, postal_code, city, is_admin 
        FROM users 
        LIMIT $usersPerPage OFFSET $offset";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    $users = [];
    while ($user = $result->fetch_assoc()) {
        $users[] = $user;
    }

    
    $countSql = "SELECT COUNT(*) AS total_users FROM users";
    $countResult = $conn->query($countSql);
    $totalUsers = $countResult->fetch_assoc()['total_users'];
    $totalPages = ceil($totalUsers / $usersPerPage); 

    echo json_encode([
        'success' => true,
        'users' => $users,
        'total_users' => $totalUsers,
        'total_pages' => $totalPages,
        'current_page' => $page,
    ]);
} else {
    
    echo json_encode(['success' => false, 'message' => 'No users found']);
}

$conn->close();
?>
