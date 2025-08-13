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

    // SQL query to fetch contact_us data
    $sql = "
        SELECT 
            id,
            name,
            email,
            phone,
            subject,
            message,
            submitted_at
        FROM 
            contact_us
        ORDER BY 
            submitted_at DESC
        LIMIT ? OFFSET ?
    ";

    // Prepare and execute the SQL statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters (limit, offset)
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result === false) {
            echo json_encode(['success' => false, 'message' => 'Error fetching contact data.']);
            exit;
        }

        $contacts = [];
        while ($row = $result->fetch_assoc()) {
            $contacts[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'subject' => $row['subject'],
                'message' => $row['message'],
                'submitted_at' => $row['submitted_at']
            ];
        }

        // Get total count for pagination
        $countSql = "SELECT COUNT(*) AS total_contacts FROM contact_us";
        $countStmt = $conn->prepare($countSql);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalContacts = $countResult->fetch_assoc()['total_contacts'];
        $totalPages = ceil($totalContacts / $limit);

        echo json_encode([
            'success' => true,
            'data' => $contacts,
            'total_contacts' => $totalContacts,
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
