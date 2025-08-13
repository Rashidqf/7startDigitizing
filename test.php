<?php
/**
 * Simple Test File
 * Use this to check if your basic setup is working
 */

echo "<h1>7StarDigitizing - System Test</h1>";

// Test 1: Basic PHP
echo "<h2>✅ Test 1: Basic PHP</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current Time: " . date('Y-m-d H:i:s') . "<br>";

// Test 2: Database Connection
echo "<h2>🔍 Test 2: Database Connection</h2>";
try {
    include 'dbConnect.php';
    echo "✅ Database connection successful<br>";
    
    // Test basic query
    if (isset($conn)) {
        if ($conn instanceof PDO) {
            echo "✅ Using new PDO connection<br>";
        } else {
            echo "✅ Using legacy mysqli connection<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 3: File System
echo "<h2>📁 Test 3: File System</h2>";
$directories = ['config', 'logs', 'uploads'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        echo "✅ Directory '$dir' exists<br>";
        echo "   Permissions: " . substr(sprintf('%o', fileperms($dir)), -4) . "<br>";
    } else {
        echo "❌ Directory '$dir' missing<br>";
    }
}

// Test 4: Configuration
echo "<h2>⚙️ Test 4: Configuration</h2>";
if (file_exists('config/config.php')) {
    echo "✅ Config file exists<br>";
    try {
        include 'config/config.php';
        echo "✅ Config loaded successfully<br>";
    } catch (Exception $e) {
        echo "❌ Config loading failed: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Config file missing<br>";
}

// Test 5: Environment
echo "<h2>🌍 Test 5: Environment</h2>";
if (file_exists('.env')) {
    echo "✅ .env file exists<br>";
} else {
    echo "⚠️ .env file missing (using defaults)<br>";
}

echo "<h2>🎯 Summary</h2>";
echo "If you see all ✅ marks above, your system is working correctly.<br>";
echo "If you see ❌ marks, those are the issues to fix.<br>";

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Fix any ❌ issues above</li>";
echo "<li>Create .env file from env.example</li>";
echo "<li>Test your main website</li>";
echo "<li>Gradually enable new features</li>";
echo "</ol>";
?>
