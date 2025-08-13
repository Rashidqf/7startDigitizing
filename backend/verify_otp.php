<?php
/**
 * OTP Verification System
 */

include '../dbConnect.php';
header('Content-Type: application/json');

try {
    // Read input data
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['email']) || !isset($data['otp'])) {
        echo json_encode(['success' => false, 'message' => 'Email and OTP are required.']);
        exit;
    }
    
    $email = trim($data['email']);
    $otp = trim($data['otp']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }
    
    // Check if OTP is 6 digits
    if (!preg_match('/^\d{6}$/', $otp)) {
        echo json_encode(['success' => false, 'message' => 'OTP must be 6 digits.']);
        exit;
    }
    
    // Get OTP from database
    $sql = "SELECT id, otp_code, otp_expiry FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Email not found.']);
        $stmt->close();
        exit;
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Check if OTP exists
    if (empty($user['otp_code'])) {
        echo json_encode(['success' => false, 'message' => 'No OTP found. Please request a new OTP.']);
        exit;
    }
    
    // Check if OTP matches
    if ($user['otp_code'] !== $otp) {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP code.']);
        exit;
    }
    
    // Check if OTP is expired
    if (empty($user['otp_expiry']) || strtotime($user['otp_expiry']) < time()) {
        echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new OTP.']);
        exit;
    }
    
    // OTP is valid - clear it from database for security
    $clear_sql = "UPDATE users SET otp_code = NULL, otp_expiry = NULL WHERE id = ?";
    $clear_stmt = $conn->prepare($clear_sql);
    
    if ($clear_stmt) {
        $clear_stmt->bind_param("i", $user['id']);
        $clear_stmt->execute();
        $clear_stmt->close();
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'OTP verified successfully.',
        'user_id' => $user['id']
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
