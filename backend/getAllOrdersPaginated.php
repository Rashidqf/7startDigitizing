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
        
        // Pagination parameters
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        
        // Calculate offset
        $offset = ($page - 1) * $limit;
        
        // Base SQL query
        $baseSql = "
            FROM 
                users u
            LEFT JOIN 
                orders o 
            ON 
                u.id = o.user_id
            WHERE 
                o.orderType = ? AND u.id = ?
        ";
        
        // Add search condition if search term is provided
        $searchCondition = '';
        $searchParams = [];
        if (!empty($search)) {
            $searchCondition = " AND (u.full_name LIKE ? OR u.email LIKE ? OR o.design_name LIKE ? OR o.status LIKE ?)";
            $searchTerm = "%$search%";
            $searchParams = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }
        
        // Count total records for pagination
        $countSql = "SELECT COUNT(DISTINCT o.id) as total " . $baseSql . $searchCondition;
        
        // Main query with pagination
        $mainSql = "
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
            " . $baseSql . $searchCondition . "
            ORDER BY 
                o.created_at DESC
            LIMIT ? OFFSET ?
        ";

        // Prepare and execute count query
        $totalRecords = 0;
        if ($countStmt = $conn->prepare($countSql)) {
            $countParams = [$orderType, $loggedInUserId];
            if (!empty($searchParams)) {
                $countParams = array_merge($countParams, $searchParams);
            }
            
            $countTypes = 'si' . str_repeat('s', count($searchParams));
            $countStmt->bind_param($countTypes, ...$countParams);
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $totalRecords = $countResult->fetch_assoc()['total'];
            $countStmt->close();
        }

        // Prepare and execute main query
        if ($stmt = $conn->prepare($mainSql)) {
            $mainParams = [$orderType, $loggedInUserId];
            if (!empty($searchParams)) {
                $mainParams = array_merge($mainParams, $searchParams);
            }
            $mainParams[] = $limit;
            $mainParams[] = $offset;
            
            $mainTypes = 'si' . str_repeat('s', count($searchParams)) . 'ii';
            $stmt->bind_param($mainTypes, ...$mainParams);
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
            
            // Calculate pagination info
            $totalPages = ceil($totalRecords / $limit);
            $hasNextPage = $page < $totalPages;
            $hasPrevPage = $page > 1;
            
            echo json_encode([
                'success' => true, 
                'data' => $usersOrders,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_records' => $totalRecords,
                    'limit' => $limit,
                    'has_next_page' => $hasNextPage,
                    'has_prev_page' => $hasPrevPage,
                    'next_page' => $hasNextPage ? $page + 1 : null,
                    'prev_page' => $hasPrevPage ? $page - 1 : null
                ]
            ]);

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
