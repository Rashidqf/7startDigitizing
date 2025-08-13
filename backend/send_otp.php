<?php
include '../dbConnect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

header('Content-Type: application/json');

// Read raw POST data
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

// Check if email exists in request
if (!isset($input['email']) || empty($input['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email is required.']);
    exit;
}

$email = $input['email'];

// Check if email exists in database
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Email not found.']);
    $stmt->close();
    exit;
}

// Generate OTP
$otp = rand(100000, 999999);

// Save OTP in the database (with expiry time)
$otp_sql = "UPDATE users SET otp_code = ?, otp_expiry = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE email = ?";
$otp_stmt = $conn->prepare($otp_sql);

if ($otp_stmt === false) {
    echo json_encode(['success' => false, 'message' => 'OTP query preparation failed: ' . $conn->error]);
    exit;
}

$otp_stmt->bind_param("ss", $otp, $email);
if ($otp_stmt->execute()) {
    // 
    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hammadyousuf.qf@gmail.com';
        $mail->Password = 'hvaorsbcsuymtlwm';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;


        $mail->setFrom('hammadyousuf.qf@gmail.com', '7StarDigitizing');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your Password Reset OTP';
        $mail->Body = '
            <!DOCTYPE html>
            <html>

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    .container {
                        font-family: "Segoe UI", Arial, sans-serif;
                        max-width: 600px;
                        margin: auto;
                        padding: 0;
                        background-color: #ffffff;
                    }

                    .header {
                        background-color: #f8f8f8;
                        padding: 15px 20px;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                    }

                    .header img {
                        max-width: 150px;
                        height: auto;
                    }

                    .header .contact-info {
                        display: flex;
                        align-items: center;
                        font-size: 14px;
                        color: #333;
                    }

                    .contact-info div {
                        margin-left: 20px;
                        display: flex;
                        gap: 5px;
                        align-items: center;
                    }

                    .contact-info img {
                        width: 20px;
                        margin-right: 8px;
                    }

                    .hero-section {
                        background: linear-gradient(135deg, #ff4d00 0%, #ff6600 100%);
                        color: white;
                        padding: 10px 20px;
                        text-align: center;
                    }

                    .hero-title {
                        font-size: 28px;
                        font-weight: bold;
                        margin: 0;
                    }

                    .message {
                        padding: 40px 30px;
                        background: #ffffff;
                        color: #333333;
                        line-height: 1.6;
                    }

                    .otp-container {
                        text-align: center;
                        margin: 30px 0;
                        background: #f8f8f8;
                        border-radius: 10px;
                    }

                    .otp {
                        font-size: 32px;
                        font-weight: bold;
                        color: #ff6600;
                        letter-spacing: 5px;
                        padding: 15px 30px;
                        background: white;
                        border: 2px solid #ff6600;
                        border-radius: 8px;
                        display: inline-block;
                    }

                    .footer {
                        background-color: #1a1a1a;
                        color: #ffffff;
                        padding: 30px 20px;
                        text-align: center;
                    }
                    


                </style>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
            </head>

            <body>
                <div class="container">
                    <div class="header">
                        <img src="http://localhost/mateen/wp-content/uploads/2021/05/logo.png" alt="7 Star Digitizing Logo">
                        <div class="contact-info">
                            <div>
                                <i style="color: #ff6600;" class="fa-solid fa-phone"></i>
                                <span><strong>+92 000 000 0000</strong></span>
                            </div>
                            <div>
                                <i style="color: #ff6600;" class="fa-solid fa-location-dot"></i>
                                <span><strong>Karachi, Sindh, Pakistan</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="hero-section">
                        <h1 class="hero-title">Password Reset Request</h1>
                        <p>Secure Your Account</p>
                    </div>

                    <div class="message">
                        <p>Hello,</p>
                        <p>We received a request to reset your password. Use the verification code below to proceed with your password reset.</p>

                        <div class="otp-container">
                            <p style="color: #666; margin-bottom: 10px;">Your verification code is:</p>
                            <div class="otp">' . $otp . '</div>
                            <p style="color: #666; font-size: 14px; margin-top: 10px;">This code will expire in 10 minutes</p>
                        </div>

                        <p>If you did not request this password reset, please ignore this email or contact our support team if you have concerns.</p>
                    </div>

                    <div class="footer">
                        <p>&copy; 2012 -  ' . date('Y') . ' 7 Star Digitizing. All rights reserved.</p>
                    </div>
                </div>
            </body>

            </html>
            ';
        $mail->send();
        echo json_encode(['success' => true, 'message' => 'OTP sent successfully.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => "Mail Error: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save OTP in database.']);
}


$stmt->close();
$otp_stmt->close();
$conn->close();
?>