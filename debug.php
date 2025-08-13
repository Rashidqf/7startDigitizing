<?php
/**
 * Debug File - Check what's causing the error
 */

echo "<h1>Debug Information</h1>";

// Test 1: Basic PHP
echo "<h2>✅ Test 1: Basic PHP</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current Time: " . date('Y-m-d H:i:s') . "<br>";

// Test 2: Database Connection
echo "<h2>🔍 Test 2: Database Connection</h2>";
try {
    include 'dbConnect.php';
    echo "✅ Database connection successful<br>";
    
    if (isset($conn)) {
        echo "✅ \$conn variable exists<br>";
        if ($conn instanceof mysqli) {
            echo "✅ Using mysqli connection<br>";
        } else {
            echo "⚠️ \$conn is not mysqli: " . get_class($conn) . "<br>";
        }
    } else {
        echo "❌ \$conn variable not set<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
} catch (Error $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "<br>";
}

// Test 3: File System
echo "<h2>📁 Test 3: File System</h2>";
$directories = ['uploads', 'backend', 'dashboard', 'site-admin'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        echo "✅ Directory '$dir' exists<br>";
    } else {
        echo "❌ Directory '$dir' missing<br>";
    }
}

// Test 4: Check for syntax errors in main files
echo "<h2>🔍 Test 4: File Syntax Check</h2>";
$files_to_check = ['index.php', 'dbConnect.php', 'backend/send_otp.php'];
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ File '$file' exists<br>";
        // Try to include it to check for syntax errors
        try {
            include $file;
            echo "✅ File '$file' loaded successfully<br>";
        } catch (ParseError $e) {
            echo "❌ Syntax error in '$file': " . $e->getMessage() . "<br>";
        } catch (Exception $e) {
            echo "⚠️ Error in '$file': " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ File '$file' missing<br>";
    }
}

// Test 5: Check error reporting
echo "<h2>⚙️ Test 5: Error Reporting</h2>";
echo "Error Reporting: " . (error_reporting() ? 'Enabled' : 'Disabled') . "<br>";
echo "Display Errors: " . (ini_get('display_errors') ? 'On' : 'Off') . "<br>";
echo "Log Errors: " . (ini_get('log_errors') ? 'On' : 'Off') . "<br>";

echo "<h2>🎯 Summary</h2>";
echo "If you see all ✅ marks above, your basic system is working.<br>";
echo "If you see ❌ marks, those are the issues to fix.<br>";

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Fix any ❌ issues above</li>";
echo "<li>Test your main website again</li>";
echo "<li>Once working, we can gradually add new features</li>";
echo "</ol>";
?>
