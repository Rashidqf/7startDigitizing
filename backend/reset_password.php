<?php
include '../dbConnect.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'];
$new_password = password_hash($data['new_password'], PASSWORD_BCRYPT);

$sql = "UPDATE users SET password=?, otp_code=NULL, otp_expiry=NULL WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $new_password, $email);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Password reset successful!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to reset password.']);
}
$stmt->close();
$conn->close();
?>
