<?php
include '../dbConnect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $requiredFields = ['username', 'full_name', 'email', 'password', 'phone_number', 'company_name', 'country', 'state', 'postal_code', 'city'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => "The field '$field' is required."]);
            exit;
        }
    }

    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone_number = $_POST['phone_number'];
    $company_name = $_POST['company_name'];
    $country = $_POST['country'];
    $state = $_POST['state'];
    $postal_code = $_POST['postal_code'];
    $phone_extension = empty($_POST['phone_extension']) ? "" : $_POST['phone_extension'];
    $city = $_POST['city'];
    $address_line1 = $_POST['address_line1'] ?? null;
    $address_line2 = $_POST['address_line2'] ?? null;

    // Set admin role (is_admin = 1)
    $is_admin = 1;  // For admin user

    // Check if username or email already exists
    $duplicateCheckSql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $duplicateStmt = $conn->prepare($duplicateCheckSql);
    if ($duplicateStmt === false) {
        echo json_encode(['success' => false, 'message' => 'Error preparing the duplicate check query.']);
        exit;
    }
    $duplicateStmt->bind_param("ss", $username, $email);
    $duplicateStmt->execute();
    $duplicateStmt->store_result();

    if ($duplicateStmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username or email already exists.']);
        $duplicateStmt->close();
        exit;
    }
    $duplicateStmt->close();

    // Prepare insert query for creating the user with admin role
    $sql = "INSERT INTO users (username, full_name, email, password, phone_number, company_name, country, state, postal_code, phone_extension, city, address_line1, address_line2, is_admin)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Error preparing the query.']);
        exit;
    }

    $stmt->bind_param("sssssssssssssi", $username, $full_name, $email, $password, $phone_number, $company_name, $country, $state, $postal_code, $phone_extension, $city, $address_line1, $address_line2, $is_admin);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Admin user created successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
