<?php
include '../dbConnect.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'];
$otp = $data['otp'];

$sql = "SELECT otp_code, otp_expiry FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($db_otp, $otp_expiry);
$stmt->fetch();
$stmt->close();

if ($db_otp === $otp && strtotime($otp_expiry) > time()) {
    echo json_encode(['success' => true, 'message' => 'OTP verified.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP.']);
}
$conn->close();
?>
