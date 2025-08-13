<?php
session_start();
include '../../dbConnect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
        exit;
    }

    
    $data = json_decode(file_get_contents("php://input"), true);
    
    
    if (!isset($data['order_id']) || !isset($data['status'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
        exit;
    }

    $order_id = (int)$data['order_id'];
    $status = $data['status'];

    
    $allowed_statuses = ['Pending', 'In Progress', 'Completed', 'Cancelled'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
        exit;
    }

    
    $sql = "UPDATE orders SET status = ? WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('si', $status, $order_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Order status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update order status.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
