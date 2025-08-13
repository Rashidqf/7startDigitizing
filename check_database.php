<?php
/**
 * Database Structure Check and Fix
 */

echo "<h1>Database Structure Check</h1>";

include 'dbConnect.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "✅ Connected to database successfully<br><br>";

// Check if users table exists and show its structure
$sql = "DESCRIBE users";
$result = $conn->query($sql);

if ($result) {
    echo "<h2>Current Users Table Structure:</h2>";
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
    
    // Check if OTP columns exist
    $result = $conn->query("DESCRIBE users");
    $hasOtpCode = false;
    $hasOtpExpiry = false;
    
    while ($row = $result->fetch_assoc()) {
        if ($row['Field'] === 'otp_code') $hasOtpCode = true;
        if ($row['Field'] === 'otp_expiry') $hasOtpExpiry = true;
    }
    
    if (!$hasOtpCode || !$hasOtpExpiry) {
        echo "<h2>⚠️ Missing OTP Columns - Adding them now...</h2>";
        
        if (!$hasOtpCode) {
            $sql = "ALTER TABLE users ADD COLUMN otp_code VARCHAR(10) NULL";
            if ($conn->query($sql)) {
                echo "✅ Added otp_code column<br>";
            } else {
                echo "❌ Failed to add otp_code: " . $conn->error . "<br>";
            }
        }
        
        if (!$hasOtpExpiry) {
            $sql = "ALTER TABLE users ADD COLUMN otp_expiry DATETIME NULL";
            if ($conn->query($sql)) {
                echo "✅ Added otp_expiry column<br>";
            } else {
                echo "❌ Failed to add otp_expiry: " . $conn->error . "<br>";
            }
        }
        
        echo "<br><h3>🎉 OTP columns added successfully!</h3>";
    } else {
        echo "<h2>✅ OTP columns already exist</h2>";
    }
    
} else {
    echo "❌ Users table doesn't exist or error: " . $conn->error . "<br>";
}

// Show sample user data
echo "<h2>Sample User Data:</h2>";
$sql = "SELECT id, username, email, role, otp_code, otp_expiry FROM users LIMIT 5";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>OTP Code</th><th>OTP Expiry</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "<td>" . ($row['otp_code'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['otp_expiry'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No users found or error: " . $conn->error . "<br>";
}

$conn->close();

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>If OTP columns were added, try the OTP functionality again</li>";
echo "<li>Test sending OTP from forgot.php</li>";
echo "<li>Test verifying OTP</li>";
echo "<li>Delete this file after successful testing</li>";
echo "</ol>";
?>
