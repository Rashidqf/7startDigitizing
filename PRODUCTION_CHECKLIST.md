# 🚀 Production Deployment Checklist
## 7StarDigitizing Website

### 📋 Pre-Deployment Checklist

#### 🔐 **Environment Configuration**
- [ ] Create `.env` file from `env.example` template
- [ ] Update database credentials with production values
- [ ] Configure SMTP settings for production email
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate unique `APP_SECRET` key
- [ ] Update `APP_URL` to production domain
- [ ] Configure file upload limits and allowed types

#### 🗄️ **Database Setup**
- [ ] Run `database_setup.sql` on production database
- [ ] Verify all tables are created successfully
- [ ] Check foreign key constraints
- [ ] Verify indexes are created
- [ ] Test stored procedures and triggers
- [ ] Change default admin password
- [ ] Set up database user with minimal required privileges
- [ ] Enable database logging and monitoring

#### 🔒 **Security Configuration**
- [ ] Update `.htaccess` with production domain
- [ ] Enable HTTPS (SSL/TLS certificate)
- [ ] Configure security headers
- [ ] Set up Content Security Policy
- [ ] Enable HSTS
- [ ] Configure rate limiting
- [ ] Set up firewall rules
- [ ] Enable intrusion detection

#### 📁 **File Permissions**
- [ ] Set `uploads/` directory permissions to 755
- [ ] Set `logs/` directory permissions to 755
- [ ] Set `config/` directory permissions to 755
- [ ] Ensure web server can write to uploads and logs
- [ ] Restrict access to sensitive directories
- [ ] Set proper ownership for web server user

#### 🚀 **Server Configuration**
- [ ] Configure web server (Apache/Nginx)
- [ ] Enable required PHP extensions
- [ ] Set PHP memory limits
- [ ] Configure PHP execution time
- [ ] Enable OPcache for PHP
- [ ] Configure GZIP compression
- [ ] Set up caching headers
- [ ] Configure error logging

### 🚀 **Deployment Steps**

#### **Step 1: Server Preparation**
```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install required software
sudo apt install apache2 php mysql-server php-mysql php-curl php-gd php-mbstring php-xml php-zip

# Enable Apache modules
sudo a2enmod rewrite headers deflate ssl

# Restart Apache
sudo systemctl restart apache2
```

#### **Step 2: Database Setup**
```bash
# Access MySQL
sudo mysql -u root -p

# Create database and user
CREATE DATABASE mateen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mateen_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON mateen.* TO 'mateen_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import database structure
mysql -u mateen_user -p mateen < database_setup.sql
```

#### **Step 3: Application Deployment**
```bash
# Create application directory
sudo mkdir -p /var/www/mateen
sudo chown -R www-data:www-data /var/www/mateen

# Upload application files
# (Use SFTP, Git, or deployment tool)

# Set proper permissions
sudo chmod -R 755 /var/www/mateen
sudo chmod -R 777 /var/www/mateen/uploads
sudo chmod -R 777 /var/www/mateen/logs
```

#### **Step 4: Configuration**
```bash
# Create .env file
cp env.example .env
nano .env

# Update with production values:
DB_HOST=localhost
DB_USER=mateen_user
DB_PASS=secure_password_here
DB_NAME=mateen
SMTP_HOST=smtp.gmail.com
SMTP_USER=your_production_email@gmail.com
SMTP_PASS=your_app_password
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_SECRET=your_random_secret_key
```

#### **Step 5: Web Server Configuration**
```bash
# Create Apache virtual host
sudo nano /etc/apache2/sites-available/mateen.conf

# Add configuration:
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/mateen
    
    <Directory /var/www/mateen>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/mateen_error.log
    CustomLog ${APACHE_LOG_DIR}/mateen_access.log combined
</VirtualHost>

# Enable site and restart
sudo a2ensite mateen
sudo systemctl restart apache2
```

### 🔍 **Post-Deployment Verification**

#### **Functionality Tests**
- [ ] Test user registration
- [ ] Test user login
- [ ] Test password reset functionality
- [ ] Test file upload system
- [ ] Test order creation
- [ ] Test admin panel access
- [ ] Test email sending
- [ ] Test payment integration

#### **Security Tests**
- [ ] Verify HTTPS is working
- [ ] Test SQL injection protection
- [ ] Test XSS protection
- [ ] Verify file upload restrictions
- [ ] Test rate limiting
- [ ] Check security headers
- [ ] Verify admin access controls

#### **Performance Tests**
- [ ] Test page load times
- [ ] Verify GZIP compression
- [ ] Check caching headers
- [ ] Test database query performance
- [ ] Monitor server resources
- [ ] Test under load

### 📊 **Monitoring & Maintenance**

#### **Log Monitoring**
- [ ] Set up log rotation
- [ ] Monitor error logs
- [ ] Set up log analysis tools
- [ ] Configure log alerts
- [ ] Monitor security logs

#### **Backup Strategy**
- [ ] Set up automated database backups
- [ ] Backup uploaded files
- [ ] Test backup restoration
- [ ] Store backups off-site
- [ ] Document backup procedures

#### **Performance Monitoring**
- [ ] Set up server monitoring
- [ ] Monitor database performance
- [ ] Track user experience metrics
- [ ] Set up performance alerts
- [ ] Regular performance reviews

### 🚨 **Emergency Procedures**

#### **Rollback Plan**
- [ ] Document current version
- [ ] Keep backup of working version
- [ ] Test rollback procedures
- [ ] Document rollback steps

#### **Incident Response**
- [ ] Define incident severity levels
- [ ] Set up escalation procedures
- [ ] Document response procedures
- [ ] Train team on procedures

### 📚 **Documentation**

#### **Required Documents**
- [ ] Server configuration guide
- [ ] Database schema documentation
- [ ] API documentation
- [ ] User manual
- [ ] Admin manual
- [ ] Troubleshooting guide
- [ ] Contact information

#### **Maintenance Schedule**
- [ ] Weekly security updates
- [ ] Monthly performance reviews
- [ ] Quarterly security audits
- [ ] Annual disaster recovery tests

### ✅ **Final Verification**

Before going live, ensure:
- [ ] All security measures are in place
- [ ] SSL certificate is valid
- [ ] Database backups are working
- [ ] Monitoring is active
- [ ] Team is trained on procedures
- [ ] Documentation is complete
- [ ] Support procedures are defined
- [ ] Legal compliance is verified

### 🔗 **Useful Commands**

#### **Check Application Status**
```bash
# Check Apache status
sudo systemctl status apache2

# Check MySQL status
sudo systemctl status mysql

# Check PHP version
php -v

# Check disk space
df -h

# Check memory usage
free -h
```

#### **View Logs**
```bash
# Apache error logs
sudo tail -f /var/log/apache2/error.log

# Application logs
tail -f /var/www/mateen/logs/error.log

# MySQL logs
sudo tail -f /var/log/mysql/error.log
```

#### **Database Maintenance**
```bash
# Optimize tables
mysql -u mateen_user -p mateen -e "OPTIMIZE TABLE users, orders, order_items;"

# Check table status
mysql -u mateen_user -p mateen -e "SHOW TABLE STATUS;"

# Backup database
mysqldump -u mateen_user -p mateen > backup_$(date +%Y%m%d_%H%M%S).sql
```

---

**🎯 Remember:** This checklist should be reviewed and updated regularly based on your specific requirements and any changes to the application or infrastructure.
