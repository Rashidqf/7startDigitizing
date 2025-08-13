<?php
/**
 * Database Setup Script
 * Run this to create your database and tables
 */

echo "<h1>Database Setup</h1>";

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";

try {
    // Connect to MySQL without selecting a database
    $conn = new mysqli($servername, $username, $password);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "✅ Connected to MySQL successfully<br>";
    
    // Read and execute the SQL file
    $sqlFile = 'create_database.sql';
    
    if (file_exists($sqlFile)) {
        echo "✅ Found SQL file: $sqlFile<br>";
        
        $sql = file_get_contents($sqlFile);
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    if ($conn->query($statement)) {
                        $successCount++;
                        echo "✅ Executed: " . substr($statement, 0, 50) . "...<br>";
                    } else {
                        $errorCount++;
                        echo "❌ Error: " . $conn->error . "<br>";
                    }
                } catch (Exception $e) {
                    $errorCount++;
                    echo "❌ Exception: " . $e->getMessage() . "<br>";
                }
            }
        }
        
        echo "<hr>";
        echo "<h2>Setup Summary</h2>";
        echo "✅ Successful statements: $successCount<br>";
        echo "❌ Failed statements: $errorCount<br>";
        
        if ($errorCount == 0) {
            echo "<h3>🎉 Database setup completed successfully!</h3>";
            echo "<p>Your database 'mateen' is now ready with:</p>";
            echo "<ul>";
            echo "<li>Users table</li>";
            echo "<li>Orders table</li>";
            echo "<li>Services table</li>";
            echo "<li>Contact inquiries table</li>";
            echo "<li>Sample data</li>";
            echo "</ul>";
            
            echo "<h3>🔑 Admin Login Credentials:</h3>";
            echo "<p><strong>Username:</strong> admin<br>";
            echo "<strong>Email:</strong> admin@mateen.com<br>";
            echo "<strong>Password:</strong> password</p>";
            
            echo "<p><a href='index.php'>Go to your website</a></p>";
        } else {
            echo "<h3>⚠️ Some errors occurred during setup</h3>";
            echo "<p>Please check the errors above and try again.</p>";
        }
        
    } else {
        echo "❌ SQL file not found: $sqlFile<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Setup failed: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>If setup was successful, delete this file for security</li>";
echo "<li>Test your website at <a href='index.php'>index.php</a></li>";
echo "<li>Try logging in with admin credentials</li>";
echo "</ol>";
?>
