<?php
/**
 * Production Logging System
 * Handles error logging, access logging, and security event logging
 */

class Logger {
    private static $logPath;
    private static $config;
    
    public static function init() {
        self::$config = Config::get('app');
        self::$logPath = __DIR__ . '/../logs/';
        
        // Create logs directory if it doesn't exist
        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0755, true);
        }
        
        // Set error handler
        set_error_handler([self::class, 'errorHandler']);
        set_exception_handler([self::class, 'exceptionHandler']);
        register_shutdown_function([self::class, 'shutdownHandler']);
    }
    
    /**
     * Log different types of messages
     */
    public static function log($message, $level = 'INFO', $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = [
            'timestamp' => $timestamp,
            'level' => strtoupper($level),
            'message' => $message,
            'context' => $context,
            'ip' => self::getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'url' => $_SERVER['REQUEST_URI'] ?? 'Unknown'
        ];
        
        $logFile = self::$logPath . strtolower($level) . '.log';
        $logLine = json_encode($logEntry) . PHP_EOL;
        
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
        
        // Also log to system error log if it's an error
        if (in_array(strtoupper($level), ['ERROR', 'CRITICAL'])) {
            error_log("[$level] $message - " . json_encode($context));
        }
    }
    
    /**
     * Log info messages
     */
    public static function info($message, $context = []) {
        self::log($message, 'INFO', $context);
    }
    
    /**
     * Log warning messages
     */
    public static function warning($message, $context = []) {
        self::log($message, 'WARNING', $context);
    }
    
    /**
     * Log error messages
     */
    public static function error($message, $context = []) {
        self::log($message, 'ERROR', $context);
    }
    
    /**
     * Log critical messages
     */
    public static function critical($message, $context = []) {
        self::log($message, 'CRITICAL', $context);
    }
    
    /**
     * Log security events
     */
    public static function security($message, $context = []) {
        self::log($message, 'SECURITY', $context);
    }
    
    /**
     * Log database queries (for debugging)
     */
    public static function query($sql, $params = [], $executionTime = 0) {
        if (self::$config['debug']) {
            self::log('Database Query', 'QUERY', [
                'sql' => $sql,
                'params' => $params,
                'execution_time' => $executionTime
            ]);
        }
    }
    
    /**
     * Log user actions
     */
    public static function userAction($userId, $action, $details = []) {
        self::log('User Action', 'USER', [
            'user_id' => $userId,
            'action' => $action,
            'details' => $details
        ]);
    }
    
    /**
     * Log order events
     */
    public static function orderEvent($orderId, $event, $details = []) {
        self::log('Order Event', 'ORDER', [
            'order_id' => $orderId,
            'event' => $event,
            'details' => $details
        ]);
    }
    
    /**
     * Custom error handler
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline) {
        $errorType = self::getErrorType($errno);
        
        self::log("PHP Error: $errstr", $errorType, [
            'file' => $errfile,
            'line' => $errline,
            'error_number' => $errno
        ]);
        
        // Don't execute PHP internal error handler
        return true;
    }
    
    /**
     * Custom exception handler
     */
    public static function exceptionHandler($exception) {
        self::log('Uncaught Exception: ' . $exception->getMessage(), 'CRITICAL', [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
    
    /**
     * Shutdown handler for fatal errors
     */
    public static function shutdownHandler() {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::log('Fatal Error: ' . $error['message'], 'CRITICAL', [
                'file' => $error['file'],
                'line' => $error['line'],
                'error_type' => $error['type']
            ]);
        }
    }
    
    /**
     * Get error type from error number
     */
    private static function getErrorType($errno) {
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
                return 'CRITICAL';
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
                return 'WARNING';
            case E_NOTICE:
            case E_USER_NOTICE:
                return 'INFO';
            case E_USER_ERROR:
                return 'ERROR';
            case E_USER_WARNING:
                return 'WARNING';
            case E_STRICT:
                return 'INFO';
            case E_RECOVERABLE_ERROR:
                return 'ERROR';
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return 'WARNING';
            default:
                return 'ERROR';
        }
    }
    
    /**
     * Get client IP address
     */
    private static function getClientIP() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_CLIENT_IP', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    }
    
    /**
     * Rotate log files if they get too large
     */
    public static function rotateLogs($maxSize = 10485760) { // 10MB
        $logFiles = glob(self::$logPath . '*.log');
        
        foreach ($logFiles as $logFile) {
            if (filesize($logFile) > $maxSize) {
                $backupFile = $logFile . '.' . date('Y-m-d-H-i-s') . '.bak';
                rename($logFile, $backupFile);
                
                // Compress old backup files
                if (function_exists('gzopen')) {
                    $compressedFile = $backupFile . '.gz';
                    $gz = gzopen($compressedFile, 'w9');
                    gzwrite($gz, file_get_contents($backupFile));
                    gzclose($gz);
                    unlink($backupFile);
                }
            }
        }
    }
    
    /**
     * Clean old log files (keep last 30 days)
     */
    public static function cleanOldLogs($daysToKeep = 30) {
        $logFiles = glob(self::$logPath . '*.log.*');
        $cutoffTime = time() - ($daysToKeep * 24 * 60 * 60);
        
        foreach ($logFiles as $logFile) {
            if (filemtime($logFile) < $cutoffTime) {
                unlink($logFile);
            }
        }
    }
}

// Initialize logger
Logger::init();
