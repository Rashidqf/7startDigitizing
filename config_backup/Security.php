<?php
/**
 * Security Utility Class
 * Handles input validation, sanitization, and security functions
 */

class Security {
    
    /**
     * Sanitize input data
     */
    public static function sanitize($input, $type = 'string') {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        
        switch ($type) {
            case 'email':
                return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
            case 'url':
                return filter_var(trim($input), FILTER_SANITIZE_URL);
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'string':
            default:
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Validate email address
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number
     */
    public static function validatePhone($phone) {
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return strlen($phone) >= 10 && strlen($phone) <= 15;
    }
    
    /**
     * Validate file upload
     */
    public static function validateFile($file, $allowedTypes = [], $maxSize = 10485760) {
        $errors = [];
        
        if (!isset($file['error']) || is_array($file['error'])) {
            $errors[] = 'Invalid file parameter';
            return $errors;
        }
        
        // Check for upload errors
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $errors[] = 'No file was uploaded';
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = 'File size exceeds limit';
                break;
            default:
                $errors[] = 'Unknown upload error';
                break;
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size';
        }
        
        // Check file type
        if (!empty($allowedTypes)) {
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedTypes)) {
                $errors[] = 'File type not allowed';
            }
        }
        
        // Additional security checks
        if (!empty($file['tmp_name'])) {
            // Check if file is actually uploaded
            if (!is_uploaded_file($file['tmp_name'])) {
                $errors[] = 'Invalid file upload';
            }
            
            // Check MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            // Define safe MIME types
            $safeMimeTypes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/pdf', 'application/postscript',
                'application/illustrator', 'application/x-illustrator'
            ];
            
            if (!in_array($mimeType, $safeMimeTypes)) {
                $errors[] = 'File MIME type not allowed';
            }
        }
        
        return $errors;
    }
    
    /**
     * Generate secure random token
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = self::generateToken();
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF token
     */
    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Rate limiting for login attempts
     */
    public static function checkRateLimit($key, $maxAttempts = 5, $timeout = 900) {
        $attempts = $_SESSION['login_attempts'][$key] ?? 0;
        $lastAttempt = $_SESSION['last_attempt'][$key] ?? 0;
        
        if ($attempts >= $maxAttempts) {
            if (time() - $lastAttempt < $timeout) {
                return false; // Still blocked
            } else {
                // Reset attempts after timeout
                $_SESSION['login_attempts'][$key] = 0;
                $_SESSION['last_attempt'][$key] = 0;
            }
        }
        
        return true;
    }
    
    /**
     * Record login attempt
     */
    public static function recordLoginAttempt($key) {
        $_SESSION['login_attempts'][$key] = ($_SESSION['login_attempts'][$key] ?? 0) + 1;
        $_SESSION['last_attempt'][$key] = time();
    }
    
    /**
     * Reset login attempts
     */
    public static function resetLoginAttempts($key) {
        unset($_SESSION['login_attempts'][$key]);
        unset($_SESSION['last_attempt'][$key]);
    }
    
    /**
     * Sanitize filename for safe storage
     */
    public static function sanitizeFilename($filename) {
        // Remove path traversal attempts
        $filename = basename($filename);
        
        // Remove special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // Ensure unique filename
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        
        return $name . '_' . time() . '.' . $extension;
    }
    
    /**
     * Validate and sanitize postal code
     */
    public static function validatePostalCode($postalCode) {
        // Remove spaces and dashes
        $postalCode = preg_replace('/[\s-]/', '', $postalCode);
        
        // Basic validation (adjust for your country)
        return preg_match('/^[A-Z0-9]{3,10}$/i', $postalCode);
    }
    
    /**
     * Prevent XSS attacks
     */
    public static function preventXSS($input) {
        if (is_array($input)) {
            return array_map([self::class, 'preventXSS'], $input);
        }
        
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Validate date format
     */
    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * Check if date is in the future
     */
    public static function isFutureDate($date, $format = 'Y-m-d') {
        if (!self::validateDate($date, $format)) {
            return false;
        }
        
        $inputDate = DateTime::createFromFormat($format, $date);
        $today = new DateTime();
        
        return $inputDate > $today;
    }
}
