<?php
/**
 * OTP Debug Script
 * Check what's happening with OTP values
 */

echo "<h1>OTP Debug Information</h1>";

include 'dbConnect.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "✅ Connected to database successfully<br><br>";

// Check current time
echo "<h2>⏰ Current Time Information:</h2>";
echo "Server Time: " . date('Y-m-d H:i:s') . "<br>";
echo "Server Timezone: " . date_default_timezone_get() . "<br>";
echo "Unix Timestamp: " . time() . "<br><br>";

// Check users table structure
echo "<h2>📋 Users Table Structure:</h2>";
$sql = "DESCRIBE users";
$result = $conn->query($sql);

if ($result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
} else {
    echo "❌ Error checking table structure: " . $conn->error . "<br>";
}

// Check OTP data for all users
echo "<h2>🔐 OTP Data for All Users:</h2>";
$sql = "SELECT id, username, email, otp_code, otp_expiry FROM users";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>OTP Code</th><th>OTP Expiry</th><th>Status</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $otpStatus = "No OTP";
        $otpExpiryTime = null;
        
        if (!empty($row['otp_code'])) {
            if (!empty($row['otp_expiry'])) {
                $otpExpiryTime = strtotime($row['otp_expiry']);
                $currentTime = time();
                
                if ($otpExpiryTime > $currentTime) {
                    $timeLeft = $otpExpiryTime - $currentTime;
                    $otpStatus = "Valid (" . gmdate("i:s", $timeLeft) . " left)";
                } else {
                    $otpStatus = "Expired (" . gmdate("i:s", $currentTime - $otpExpiryTime) . " ago)";
                }
            } else {
                $otpStatus = "No expiry time";
            }
        }
        
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . ($row['otp_code'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['otp_expiry'] ?? 'NULL') . "</td>";
        echo "<td>" . $otpStatus . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
} else {
    echo "No users found or error: " . $conn->error . "<br>";
}

// Test OTP expiry calculation
echo "<h2>🧮 OTP Expiry Test:</h2>";
$testEmail = "admin@mateen.com"; // Change this to your email if different

$sql = "SELECT otp_code, otp_expiry FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $testEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        echo "Email: $testEmail<br>";
        echo "OTP Code: " . ($user['otp_code'] ?? 'NULL') . "<br>";
        echo "OTP Expiry: " . ($user['otp_expiry'] ?? 'NULL') . "<br>";
        
        if (!empty($user['otp_expiry'])) {
            $expiryTime = strtotime($user['otp_expiry']);
            $currentTime = time();
            $timeDiff = $expiryTime - $currentTime;
            
            echo "Expiry Timestamp: $expiryTime<br>";
            echo "Current Timestamp: $currentTime<br>";
            echo "Time Difference: $timeDiff seconds<br>";
            
            if ($timeDiff > 0) {
                echo "✅ OTP is still valid for " . gmdate("i:s", $timeDiff) . "<br>";
            } else {
                echo "❌ OTP expired " . gmdate("i:s", abs($timeDiff)) . " ago<br>";
            }
        } else {
            echo "❌ No OTP expiry time set<br>";
        }
    } else {
        echo "User with email $testEmail not found<br>";
    }
    
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error . "<br>";
}

$conn->close();

echo "<hr>";
echo "<h2>🔧 Troubleshooting Steps:</h2>";
echo "<ol>";
echo "<li>Check if OTP columns exist in the table above</li>";
echo "<li>Look at the OTP expiry times and status</li>";
echo "<li>If OTP expires too quickly, there might be a timezone issue</li>";
echo "<li>Try sending a new OTP and check the expiry time immediately</li>";
echo "</ol>";

echo "<h2>📧 Test OTP:</h2>";
echo "<p>1. Go to <a href='forgot.php'>forgot.php</a></p>";
echo "<p>2. Send OTP to your email</p>";
echo "<p>3. Come back here and refresh to see the new OTP data</p>";
?>
