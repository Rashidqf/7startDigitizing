<?php
session_start();
include '../../dbConnect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
        exit;
    }

    // Pagination: get `page`, `limit`, and `orderType` from query parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    $orderType = isset($_GET['orderType']) ? $_GET['orderType'] : 'quickOrder'; // Default to 'revive'

    // SQL query to fetch orders along with user details
    $sql = "
        SELECT 
            u.email,
            u.username,
            u.company_name,
            o.id AS order_id,
            o.design_name,
            o.format,
            o.location,
            o.price,
            o.status,
            o.rush_order,
            o.color_name,
            o.fabric_type,
            o.expected_delivery,
            o.comments,
            o.created_at AS order_created_at
        FROM 
            orders o
        LEFT JOIN 
            users u 
        ON 
            o.user_id = u.id
        WHERE 
            o.orderType = ?
        ORDER BY 
            o.created_at DESC
        LIMIT ? OFFSET ?
    ";

    // Prepare and execute the SQL statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters (orderType, limit, offset)
        $stmt->bind_param('sii', $orderType, $limit, $offset);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result === false) {
            echo json_encode(['success' => false, 'message' => 'Error fetching orders.']);
            exit;
        }

        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = [
                'order_id' => $row['order_id'],
                'design_name' => $row['design_name'],
                'status' => $row['status'],
                'format' => $row['format'],
                'location' => $row['location'],
                'price' => $row['price'],
                'rush_order' => $row['rush_order'],
                'color_name' => $row['color_name'],
                'fabric_type' => $row['fabric_type'],
                'expected_delivery' => $row['expected_delivery'],
                'comments' => $row['comments'],
                'order_created_at' => $row['order_created_at'],
                'email' => $row['email'],
                'username' => $row['username'],
                'company_name' => $row['company_name']
            ];
        }

        // Get total count for pagination
        $countSql = "SELECT COUNT(*) AS total_orders FROM orders WHERE orderType = ?";
        $countStmt = $conn->prepare($countSql);
        $countStmt->bind_param('s', $orderType);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalOrders = $countResult->fetch_assoc()['total_orders'];
        $totalPages = ceil($totalOrders / $limit);

        echo json_encode([
            'success' => true,
            'data' => $orders,
            'total_orders' => $totalOrders,
            'total_pages' => $totalPages,
            'current_page' => $page,
            'limit' => $limit
        ]);

        $stmt->close();
        $countStmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
