<?php
session_start();
include '../dbConnect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User is not logged in.']);
        exit;
    }

    // Required fields
    $requiredFields = [
        'designName',
        'format',
        'location',
        'price',
        'colorName',
        'deliveryDate'
    ];

    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => "The field '$field' is required."]);
            exit;
        }
    }

    $userId = $_SESSION['user_id'];

    // Get form data
    $designName = $_POST['designName'];
    $format = $_POST['format'];
    $price = $_POST['price'];
    $height = isset($_POST['height']) ? $_POST['height'] : null;
    $width = isset($_POST['width']) ? $_POST['width'] : null;
    $colorName = $_POST['colorName'];
    $numColors = isset($_POST['colorCount']) ? $_POST['colorCount'] : null; // FIXED: color_count → num_colors
    $fabricType = isset($_POST['fabricType']) ? $_POST['fabricType'] : "";
    $expectedDelivery = $_POST['deliveryDate'];
    $rushOrder = isset($_POST['rushOrder']) ? (int)$_POST['rushOrder'] : 0;
    $location = $_POST['location'];
    $comments = isset($_POST['comments']) ? $_POST['comments'] : "";
    $orderType = isset($_POST['orderType']) ? $_POST['orderType'] : "";
    $status = "pending"; // Default order status

    // Handle file upload (designFile)
    $imagePath = null;
    if (isset($_FILES['designFile']) && $_FILES['designFile']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        $fileName = time() . "_" . basename($_FILES['designFile']['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['designFile']['tmp_name'], $filePath)) {
            $imagePath = $filePath;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload design file.']);
            exit;
        }
    }

    // Insert Order into Database
    $sql = "INSERT INTO orders (
        user_id, design_name, rush_order, format, location, price, height, width, 
        color_name, num_colors, fabric_type, expected_delivery, comments, orderType, status, image_path
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Error preparing the query.', 'error' => $conn->error]);
        exit;
    }

    $stmt->bind_param(
        "isissddssdssssss",
        $userId,
        $designName,
        $rushOrder,
        $format,
        $location,
        $price,
        $height,
        $width,
        $colorName,
        $numColors,
        $fabricType,
        $expectedDelivery,
        $comments,
        $orderType,
        $status,
        $imagePath
    );

    if ($stmt->execute()) {
        $orderId = $stmt->insert_id;
        $stmt->close();

        // Fetch User Details
        $userQuery = "SELECT * FROM users WHERE id = ?";
        $userStmt = $conn->prepare($userQuery);

        if ($userStmt === false) {
            echo json_encode(['success' => false, 'message' => 'Error preparing user query.', 'error' => $conn->error]);
            exit;
        }

        $userStmt->bind_param("i", $userId);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        $user = $userResult->fetch_assoc();
        $userStmt->close();

        echo json_encode([
            'success' => true,
            'message' => 'Order created successfully!',
            'order_id' => $orderId,
            'order_details' => [
                'design_name' => $designName,
                'format' => $format,
                'location' => $location,
                'price' => $price,
                'height' => $height,
                'width' => $width,
                'color_name' => $colorName,
                'num_colors' => $numColors,
                'fabric_type' => $fabricType,
                'expected_delivery' => $expectedDelivery,
                'rush_order' => $rushOrder,
                'comments' => $comments,
                'orderType' => $orderType,
                'status' => $status,
                'image_path' => $imagePath
            ],
            'user_details' => $user
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create the order.', 'error' => $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
