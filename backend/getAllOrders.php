<?php
session_start();
include '../dbConnect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Check if the user is logged in and has a user ID in the session
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User is not logged in.']);
        exit;
    }

    $loggedInUserId = $_SESSION['user_id'];

    // Check if orderType is provided in the URL parameters
    if (isset($_GET['orderType'])) {
        $orderType = $_GET['orderType'];

        // Updated SQL query to filter by the logged-in user's ID and the provided orderType
        $sql = "
            SELECT 
                u.id AS user_id,
                u.full_name,
                u.email,
                u.company_name,
                u.phone_number,
                o.id AS order_id,
                o.design_name,
                o.format,
                o.rush_order,
                o.location,
                o.price,
                o.color_name,
                o.fabric_type,
                o.expected_delivery,
                o.comments,
                o.status,
                o.created_at AS order_created_at
            FROM 
                users u
            LEFT JOIN 
                orders o 
            ON 
                u.id = o.user_id
            WHERE 
                o.orderType = ? AND u.id = ?  -- Filter by orderType and logged-in user's ID
            ORDER BY 
                u.id, o.created_at
        ";

        // Prepare the SQL statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind the orderType and logged-in user's ID parameters
            $stmt->bind_param('si', $orderType, $loggedInUserId);  // 's' for string, 'i' for integer
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result === false) {
                echo json_encode(['success' => false, 'message' => 'Error fetching orders.']);
                exit;
            }

            $usersOrders = [];
            while ($row = $result->fetch_assoc()) {
                $userId = $row['user_id'];
                if (!isset($usersOrders[$userId])) {
                    $usersOrders[$userId] = [
                        'user_id' => $userId,
                        'full_name' => $row['full_name'],
                        'email' => $row['email'],
                        'phone_number' => $row['phone_number'],
                        'company_name' => $row['company_name'],
                        'orders' => []
                    ];
                }

                if (!empty($row['order_id'])) {
                    $usersOrders[$userId]['orders'][] = [
                        'order_id' => $row['order_id'],
                        'design_name' => $row['design_name'],
                        'format' => $row['format'],
                        'location' => $row['location'],
                        'price' => $row['price'],
                        'rush_order' => $row['rush_order'],
                        'color_name' => $row['color_name'],
                        'fabric_type' => $row['fabric_type'],
                        'expected_delivery' => $row['expected_delivery'],
                        'comments' => $row['comments'],
                        'order_created_at' => $row['order_created_at'],
                        'status' => $row['status']
                    ];
                }
            }

            $usersOrders = array_values($usersOrders);
            echo json_encode(['success' => true, 'data' => $usersOrders]);

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error preparing statement.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'orderType parameter is required.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
