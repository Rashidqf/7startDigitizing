-- =====================================================
-- 7StarDigitizing Production Database Setup
-- Complete database structure with sample data
-- =====================================================

-- Create database
CREATE DATABASE IF NOT EXISTS mateen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mateen;

-- =====================================================
-- USERS TABLE
-- =====================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100) DEFAULT 'Pakistan',
    user_type ENUM('user', 'admin', 'super_admin') DEFAULT 'user',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    email_verified BOOLEAN DEFAULT FALSE,
    phone_verified BOOLEAN DEFAULT FALSE,
    otp_code VARCHAR(10),
    otp_expiry DATETIME,
    reset_token VARCHAR(255),
    reset_token_expiry DATETIME,
    last_login DATETIME,
    login_attempts INT DEFAULT 0,
    locked_until DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_status (status),
    INDEX idx_user_type (user_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ORDERS TABLE
-- =====================================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    service_type ENUM('digitizing', 'vector', 'both') NOT NULL,
    order_type ENUM('regular', 'rush', 'super_rush') DEFAULT 'regular',
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'refunded') DEFAULT 'pending',
    total_amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_transaction_id VARCHAR(255),
    rush_fee DECIMAL(10,2) DEFAULT 0.00,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    final_amount DECIMAL(10,2) NOT NULL,
    estimated_delivery DATE,
    actual_delivery DATE,
    special_instructions TEXT,
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_order_number (order_number),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ORDER_ITEMS TABLE
-- =====================================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    item_type ENUM('digitizing', 'vector', 'other') NOT NULL,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    specifications TEXT,
    file_path VARCHAR(500),
    original_filename VARCHAR(255),
    file_size INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id),
    INDEX idx_item_type (item_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ORDER_FILES TABLE
-- =====================================================
CREATE TABLE order_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    file_type ENUM('source', 'result', 'reference') NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_size INT,
    mime_type VARCHAR(100),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    uploaded_by INT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_order_id (order_id),
    INDEX idx_file_type (file_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ORDER_STATUS_HISTORY TABLE
-- =====================================================
CREATE TABLE order_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'refunded') NOT NULL,
    changed_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_order_id (order_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SERVICES TABLE
-- =====================================================
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    base_price DECIMAL(10,2) NOT NULL,
    rush_price DECIMAL(10,2),
    super_rush_price DECIMAL(10,2),
    category ENUM('digitizing', 'vector', 'other') NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CONTACT_INQUIRIES TABLE
-- =====================================================
CREATE TABLE contact_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- STATES TABLE
-- =====================================================
CREATE TABLE states (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    country_code VARCHAR(2) DEFAULT 'PK',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_country_code (country_code),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CITIES TABLE
-- =====================================================
CREATE TABLE cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    state_id INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE CASCADE,
    INDEX idx_state_id (state_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SYSTEM_SETTINGS TABLE
-- =====================================================
CREATE TABLE system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'integer', 'float', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key),
    INDEX idx_is_public (is_public)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- AUDIT_LOGS TABLE
-- =====================================================
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(100),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_table_name (table_name),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERT SAMPLE DATA
-- =====================================================

-- Insert default admin user
INSERT INTO users (username, full_name, email, password, user_type, status, email_verified, phone_verified) VALUES
('admin', 'System Administrator', 'admin@mateen.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 'active', TRUE, TRUE);

-- Insert Pakistan states
INSERT INTO states (name, country_code) VALUES
('Sindh', 'PK'),
('Punjab', 'PK'),
('Khyber Pakhtunkhwa', 'PK'),
('Balochistan', 'PK'),
('Gilgit-Baltistan', 'PK'),
('Azad Jammu and Kashmir', 'PK'),
('Islamabad Capital Territory', 'PK');

-- Insert major cities
INSERT INTO cities (name, state_id) VALUES
('Karachi', 1),
('Hyderabad', 1),
('Sukkur', 1),
('Lahore', 2),
('Faisalabad', 2),
('Rawalpindi', 2),
('Peshawar', 3),
('Quetta', 4);

-- Insert default services
INSERT INTO services (name, description, base_price, rush_price, super_rush_price, category, sort_order) VALUES
('Basic Digitizing', 'Standard embroidery digitizing service', 10.00, 15.00, 20.00, 'digitizing', 1),
('Premium Digitizing', 'High-quality embroidery digitizing with multiple formats', 15.00, 22.50, 30.00, 'digitizing', 2),
('Complex Digitizing', 'Advanced embroidery digitizing for complex designs', 25.00, 37.50, 50.00, 'digitizing', 3),
('Vector Artwork', 'Convert images to vector format', 25.00, 37.50, 50.00, 'vector', 4),
('Premium Vector', 'High-quality vector conversion with multiple formats', 35.00, 52.50, 70.00, 'vector', 5),
('Logo Vectorization', 'Professional logo vectorization service', 40.00, 60.00, 80.00, 'vector', 6);

-- Insert system settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description, is_public) VALUES
('site_name', '7StarDigitizing', 'string', 'Website name', TRUE),
('site_description', 'Professional Embroidery Digitizing Services', 'string', 'Website description', TRUE),
('contact_email', 'info@mateen.com', 'string', 'Contact email address', TRUE),
('contact_phone', '+92 000 000 0000', 'string', 'Contact phone number', TRUE),
('contact_address', 'Karachi, Sindh, Pakistan', 'string', 'Contact address', TRUE),
('business_hours', 'Mon-Fri: 9:00 AM - 6:00 PM', 'string', 'Business hours', TRUE),
('default_currency', 'USD', 'string', 'Default currency for pricing', TRUE),
('maintenance_mode', 'false', 'boolean', 'Maintenance mode status', FALSE),
('max_file_size', '10485760', 'integer', 'Maximum file upload size in bytes', FALSE),
('allowed_file_types', 'jpg,jpeg,png,gif,pdf,ai,eps,cdr', 'string', 'Allowed file types for upload', FALSE);

-- Insert sample contact inquiry
INSERT INTO contact_inquiries (name, email, phone, subject, message, status) VALUES
('John Doe', 'john@example.com', '+1234567890', 'General Inquiry', 'I would like to know more about your digitizing services.', 'new');

-- Insert sample order (for testing)
INSERT INTO orders (order_number, user_id, service_type, order_type, status, total_amount, final_amount, estimated_delivery) VALUES
('ORD-2024-001', 1, 'digitizing', 'regular', 'pending', 10.00, 10.00, DATE_ADD(CURDATE(), INTERVAL 3 DAY));

-- Insert sample order item
INSERT INTO order_items (order_id, item_name, item_type, quantity, unit_price, total_price, specifications) VALUES
(1, 'Logo Digitizing', 'digitizing', 1, 10.00, 10.00, 'Simple logo design, 2 inches');

-- Insert sample order file
INSERT INTO order_files (order_id, file_type, file_path, original_filename, file_size, mime_type, uploaded_by) VALUES
(1, 'source', 'uploads/orders/1/source_logo.png', 'logo.png', 1024000, 'image/png', 1);

-- Insert order status history
INSERT INTO order_status_history (order_id, status, changed_by, notes) VALUES
(1, 'pending', 1, 'Order created successfully');

-- =====================================================
-- CREATE INDEXES FOR PERFORMANCE
-- =====================================================

-- Composite indexes for better query performance
CREATE INDEX idx_orders_user_status ON orders(user_id, status);
CREATE INDEX idx_orders_status_created ON orders(status, created_at);
CREATE INDEX idx_order_items_order_type ON order_items(order_id, item_type);
CREATE INDEX idx_users_type_status ON users(user_type, status);
CREATE INDEX idx_contact_status_created ON contact_inquiries(status, created_at);

-- =====================================================
-- CREATE VIEWS FOR COMMON QUERIES
-- =====================================================

-- View for order summary
CREATE VIEW order_summary AS
SELECT 
    o.id,
    o.order_number,
    o.user_id,
    u.full_name,
    u.email,
    o.service_type,
    o.order_type,
    o.status,
    o.total_amount,
    o.final_amount,
    o.payment_status,
    o.estimated_delivery,
    o.created_at,
    COUNT(oi.id) as item_count
FROM orders o
JOIN users u ON o.user_id = u.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id;

-- View for user orders
CREATE VIEW user_orders AS
SELECT 
    u.id as user_id,
    u.full_name,
    u.email,
    COUNT(o.id) as total_orders,
    SUM(CASE WHEN o.status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
    SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
    SUM(o.final_amount) as total_spent
FROM users u
LEFT JOIN orders o ON u.id = o.user_id
GROUP BY u.id;

-- =====================================================
-- CREATE STORED PROCEDURES
-- =====================================================

DELIMITER //

-- Procedure to get order details with items and files
CREATE PROCEDURE GetOrderDetails(IN orderId INT)
BEGIN
    SELECT 
        o.*,
        u.full_name,
        u.email,
        u.phone
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = orderId;
    
    SELECT * FROM order_items WHERE order_id = orderId;
    SELECT * FROM order_files WHERE order_id = orderId;
    SELECT * FROM order_status_history WHERE order_id = orderId ORDER BY created_at DESC;
END //

-- Procedure to update order status
CREATE PROCEDURE UpdateOrderStatus(
    IN orderId INT, 
    IN newStatus VARCHAR(50), 
    IN adminId INT, 
    IN notes TEXT
)
BEGIN
    DECLARE currentStatus VARCHAR(50);
    
    -- Get current status
    SELECT status INTO currentStatus FROM orders WHERE id = orderId;
    
    -- Update order status
    UPDATE orders SET status = newStatus, updated_at = NOW() WHERE id = orderId;
    
    -- Add to status history
    INSERT INTO order_status_history (order_id, status, changed_by, notes)
    VALUES (orderId, newStatus, adminId, notes);
    
    -- Log the change
    INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values)
    VALUES (adminId, 'status_update', 'orders', orderId, 
            JSON_OBJECT('status', currentStatus), 
            JSON_OBJECT('status', newStatus));
END //

DELIMITER ;

-- =====================================================
-- CREATE TRIGGERS
-- =====================================================

DELIMITER //

-- Trigger to update order total when items change
CREATE TRIGGER update_order_total_after_insert
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE orders 
    SET total_amount = (
        SELECT SUM(total_price) 
        FROM order_items 
        WHERE order_id = NEW.order_id
    ),
    final_amount = total_amount + rush_fee - discount_amount
    WHERE id = NEW.order_id;
END //

-- Trigger to update order total when items are updated
CREATE TRIGGER update_order_total_after_update
AFTER UPDATE ON order_items
FOR EACH ROW
BEGIN
    UPDATE orders 
    SET total_amount = (
        SELECT SUM(total_price) 
        FROM order_items 
        WHERE order_id = NEW.order_id
    ),
    final_amount = total_amount + rush_fee - discount_amount
    WHERE id = NEW.order_id;
END //

-- Trigger to update order total when items are deleted
CREATE TRIGGER update_order_total_after_delete
AFTER DELETE ON order_items
FOR EACH ROW
BEGIN
    UPDATE orders 
    SET total_amount = (
        SELECT COALESCE(SUM(total_price), 0) 
        FROM order_items 
        WHERE order_id = OLD.order_id
    ),
    final_amount = total_amount + rush_fee - discount_amount
    WHERE id = OLD.order_id;
END //

-- Trigger to log user actions
CREATE TRIGGER log_user_changes
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status OR OLD.user_type != NEW.user_type THEN
        INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values)
        VALUES (NEW.id, 'user_update', 'users', NEW.id,
                JSON_OBJECT('status', OLD.status, 'user_type', OLD.user_type),
                JSON_OBJECT('status', NEW.status, 'user_type', NEW.user_type));
    END IF;
END //

DELIMITER ;

-- =====================================================
-- FINAL COMMENTS
-- =====================================================

-- Database setup complete!
-- 
-- Default admin credentials:
-- Email: admin@mateen.com
-- Password: password
--
-- Remember to:
-- 1. Change the default admin password
-- 2. Update the .env file with your actual database credentials
-- 3. Configure your SMTP settings
-- 4. Set up proper file permissions for uploads and logs directories
-- 5. Enable HTTPS in production
-- 6. Set up regular database backups
-- 7. Monitor the audit logs for security
-- 8. Configure proper error logging
