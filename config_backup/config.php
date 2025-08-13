<?php
/**
 * Production Configuration Loader
 * Loads configuration from environment variables with fallbacks
 */

class Config {
    private static $config = [];
    
    public static function init() {
        // Load environment variables
        self::loadEnvFile();
        
        // Set default configurations
        self::$config = [
            'database' => [
                'host' => self::getEnv('DB_HOST', 'localhost'),
                'username' => self::getEnv('DB_USER', 'root'),
                'password' => self::getEnv('DB_PASS', ''),
                'name' => self::getEnv('DB_NAME', 'mateen'),
                'charset' => 'utf8mb4',
                'options' => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            ],
            'smtp' => [
                'host' => self::getEnv('SMTP_HOST', 'smtp.gmail.com'),
                'port' => self::getEnv('SMTP_PORT', 587),
                'username' => self::getEnv('SMTP_USER', ''),
                'password' => self::getEnv('SMTP_PASS', ''),
                'from_email' => self::getEnv('SMTP_FROM', ''),
                'from_name' => self::getEnv('SMTP_FROM_NAME', '7StarDigitizing')
            ],
            'app' => [
                'env' => self::getEnv('APP_ENV', 'production'),
                'debug' => self::getEnv('APP_DEBUG', 'false') === 'true',
                'url' => self::getEnv('APP_URL', 'http://localhost'),
                'secret' => self::getEnv('APP_SECRET', 'default_secret_change_in_production'),
                'timezone' => 'UTC'
            ],
            'upload' => [
                'max_size' => (int)self::getEnv('MAX_FILE_SIZE', 10485760), // 10MB
                'allowed_types' => explode(',', self::getEnv('ALLOWED_FILE_TYPES', 'jpg,jpeg,png,gif,pdf,ai,eps,cdr')),
                'path' => self::getEnv('UPLOAD_PATH', 'uploads/')
            ],
            'security' => [
                'session_timeout' => (int)self::getEnv('SESSION_TIMEOUT', 3600),
                'otp_expiry' => (int)self::getEnv('OTP_EXPIRY', 600),
                'max_login_attempts' => (int)self::getEnv('MAX_LOGIN_ATTEMPTS', 5),
                'login_timeout' => (int)self::getEnv('LOGIN_TIMEOUT', 900)
            ]
        ];
        
        // Set timezone
        date_default_timezone_set(self::$config['app']['timezone']);
        
        // Set error reporting based on environment
        if (self::$config['app']['debug']) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
    }
    
    private static function loadEnvFile() {
        // Try multiple possible locations for .env file
        $possiblePaths = [
            __DIR__ . '/../.env',                    // From config/ directory
            __DIR__ . '/../../.env',                 // From project root
            dirname(__DIR__) . '/.env',              // Alternative path
            getcwd() . '/.env'                       // Current working directory
        ];
        
        $envFile = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $envFile = $path;
                break;
            }
        }
        
        if ($envFile) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Remove quotes if present
                    if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                        $value = $matches[2];
                    }
                    
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
    }
    
    private static function getEnv($key, $default = null) {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
    
    public static function get($key, $default = null) {
        $keys = explode('.', $key);
        $config = self::$config;
        
        foreach ($keys as $k) {
            if (isset($config[$k])) {
                $config = $config[$k];
            } else {
                return $default;
            }
        }
        
        return $config;
    }
    
    public static function all() {
        return self::$config;
    }
}

// Initialize configuration
Config::init();
