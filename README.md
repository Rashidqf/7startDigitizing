# 7StarDigitizing - Embroidery Digitizing Services Website

![7StarDigitizing Logo](wp-content/uploads/2021/05/logo.png)

## 📋 Table of Contents
- [Project Overview](#project-overview)
- [Features](#features)
- [Website Sections](#website-sections)
- [Technical Architecture](#technical-architecture)
- [Database Structure](#database-structure)
- [Installation & Setup](#installation--setup)
- [Admin Panel](#admin-panel)
- [User Management](#user-management)
- [Order Management](#order-management)
- [File Structure](#file-structure)
- [Technologies Used](#technologies-used)
- [Screenshots](#screenshots)
- [API Endpoints](#api-endpoints)
- [Contributing](#contributing)
- [License](#license)

## 🎯 Project Overview

**7StarDigitizing** is a comprehensive embroidery digitizing services website that has been serving customers since 1998. The platform offers professional embroidery digitizing, vector artwork services, and custom design solutions for businesses and individuals worldwide.

### Key Highlights
- **24+ Years** of industry experience
- **400K+ Projects** completed successfully
- **7000+ Worldwide** clients served
- **Fast turnaround** (8-24 hours)
- **100% satisfaction** guaranteed

## ✨ Features

### 🔐 Authentication System
- User registration and login
- Admin panel with role-based access
- Password reset with OTP verification
- Session management
- Secure password hashing

### 📋 Order Management
- Quick order placement
- Vector artwork orders
- File upload support
- Order status tracking
- Rush order options (+$10)

### 💳 Payment Integration
- 2Checkout payment gateway
- Secure checkout process
- Multiple currency support
- Order confirmation system

### 📱 Responsive Design
- Mobile-friendly interface
- Cross-browser compatibility
- Modern UI/UX design
- Fast loading times

## 🌐 Website Sections

### 1. Header & Navigation
- **Top Bar**: Contact info and social media links
- **Main Header**: Logo, contact details, navigation menu
- **Navigation**: Home, About Us, Price, Login, Contact, Terms, Privacy Policy
- **Call-to-Action**: "ORDER NOW" button

### 2. Hero Section
- **Background**: Collage of embroidered patches and designs
- **Main Message**: "FAST TURNAROUND"
- **Slogan**: "100% satisfaction - 7StarDigitizing delivers excellence in every stitch"
- **Action Buttons**: About Us, Contact Us

### 3. Digitizing Services
- **$10 Flat Rate**: Left chest size 5x5 inches (Unlimited Stitches)
- **$35 Flat Rate**: Jacket Back logos (Unlimited Stitches)
- **Fast Turnaround**: 8-24 hours delivery
- **Quality Assurance**: Well-trained professionals, no hidden charges

### 4. Vector Artwork Services
- **$25 Flat Rate**: Simple designs (1-3 colors)
- **$40 Flat Rate**: Complex designs (4+ colors)
- **Package Deals**: Multiple designs at discounted rates
- **Quality Features**: Fast turnaround, unlimited revisions

### 5. About Us Section
- Company history since 1998
- Industry expertise and experience
- Key metrics and achievements
- Professional team information

### 6. Our Services Gallery
- **Left Chest Digitizing** ($10)
- **Caps Digitizing** ($10)
- **Pocket Size Digitizing** ($10)
- **Jacket Back Digitizing** ($35)
- **Simple Vector Designs** ($10)
- **Complex Vector Designs** ($35)

### 7. Customer Reviews
- Client testimonials
- Rating system
- Success stories
- Customer satisfaction feedback

### 8. Footer
- Company information
- Quick links
- Project portfolio
- Social media integration
- Copyright information

## 🏗️ Technical Architecture

### Frontend
- **HTML5**: Semantic markup
- **CSS3**: Modern styling with animations
- **JavaScript**: Interactive functionality
- **Bootstrap**: Responsive framework
- **jQuery**: DOM manipulation and AJAX

### Backend
- **PHP**: Server-side scripting
- **MySQL**: Database management
- **Apache**: Web server
- **PHPMailer**: Email functionality

### Security Features
- **Password Hashing**: BCRYPT encryption
- **SQL Injection Prevention**: Prepared statements
- **Session Management**: Secure user sessions
- **File Upload Security**: Validation and sanitization

## 🗄️ Database Structure

### Tables Overview
1. **users** - User accounts and profiles
2. **orders** - Order management and tracking
3. **contact_us** - Contact form submissions

### Key Relationships
- Users can have multiple orders
- Orders are linked to user accounts
- Contact submissions are stored separately

## 🚀 Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependencies)

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/7StarDigitizing.git
   cd 7StarDigitizing
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Database setup**
   ```sql
   -- Create database
   CREATE DATABASE mateen;
   USE mateen;
   
   -- Run the complete database setup script from the SQL file
   ```

4. **Configure database connection**
   - Update `dbConnect.php` with your database credentials
   - Ensure database name matches your setup

5. **Set up file permissions**
   ```bash
   chmod 755 uploads/
   chmod 644 *.php
   ```

6. **Configure web server**
   - Point document root to project directory
   - Enable URL rewriting (Apache mod_rewrite)

### Default Admin Credentials
- **Email**: `admin@mateen.com`
- **Password**: `password`

## 👨‍💼 Admin Panel

### Access Points
- **Main Admin**: `/site-admin/`
- **Backend Admin**: `/backend/admin/`

### Admin Features
- **User Management**: View, manage all registered users
- **Order Management**: Track and update order statuses
- **Contact Management**: View contact form submissions
- **Dashboard Analytics**: Overview of system activity

### Order Status Management
- Pending
- In Progress
- Completed
- Cancelled

## 👥 User Management

### User Registration
- Required fields: username, full name, email, password, phone, company, address
- Optional fields: phone extension, additional address lines
- Duplicate prevention for username and email

### User Profiles
- Personal information management
- Company details
- Address information
- Order history

### Password Security
- BCRYPT hashing
- OTP-based password reset
- Secure session management

## 📦 Order Management

### Order Types
1. **Quick Orders**: Standard embroidery digitizing
2. **Vector Orders**: Vector artwork and design services

### Order Process
1. User selects service type
2. Fills order form with design details
3. Uploads design file (optional)
4. Selects delivery options
5. Proceeds to payment
6. Order confirmation and tracking

### Order Details
- Design specifications
- File format requirements
- Color specifications
- Delivery timeline
- Rush order options

## 📁 File Structure

```
7StarDigitizing/
├── backend/                 # Backend PHP files
│   ├── admin/              # Admin panel files
│   ├── uploads/            # File uploads
│   ├── vendor/             # Composer dependencies
│   ├── createOrder.php     # Order creation
│   ├── login.php           # User login
│   ├── register.php        # User registration
│   └── ...
├── component/               # Reusable components
│   ├── header.php          # Site header
│   └── footer.php          # Site footer
├── dashboard/               # User dashboard
│   ├── css/                # Dashboard styles
│   ├── js/                 # Dashboard scripts
│   ├── pages/              # Dashboard pages
│   └── ...
├── site-admin/             # Admin panel
│   ├── css/                # Admin styles
│   ├── js/                 # Admin scripts
│   ├── allOrders.php       # Order management
│   ├── allUsers.php        # User management
│   └── ...
├── wp-content/             # WordPress content (if applicable)
├── images/                 # Website images
├── config.php              # Configuration file
├── dbConnect.php           # Database connection
├── .htaccess               # Apache configuration
└── README.md               # This file
```

## 🛠️ Technologies Used

### Frontend Technologies
- **HTML5**: Modern semantic markup
- **CSS3**: Advanced styling and animations
- **JavaScript (ES6+)**: Modern JavaScript features
- **Bootstrap 5**: Responsive CSS framework
- **jQuery**: DOM manipulation library

### Backend Technologies
- **PHP 8.0+**: Server-side programming
- **MySQL 8.0**: Relational database
- **Apache 2.4**: Web server
- **Composer**: Dependency management

### Third-Party Services
- **2Checkout**: Payment gateway
- **PHPMailer**: Email functionality
- **Geonames API**: Location services

## 📸 Screenshots

### Main Website
![Main Website](pageScreenShot.png)

### Key Sections
- **Header**: Professional navigation with company branding
- **Hero Section**: Eye-catching banner with service highlights
- **Services**: Clear pricing and service descriptions
- **About Us**: Company history and achievements
- **Portfolio**: Sample work and project examples
- **Footer**: Complete site information and links

## 🔌 API Endpoints

### Authentication
- `POST /backend/login.php` - User login
- `POST /backend/register.php` - User registration
- `POST /backend/admin/adminLogin.php` - Admin login

### Orders
- `POST /backend/createOrder.php` - Create new order
- `GET /backend/getAllOrders.php` - Retrieve orders
- `POST /backend/admin/updateOrderStatus.php` - Update order status

### User Management
- `GET /backend/admin/get_users.php` - Get all users
- `GET /checkSession.php` - Check user session

### Contact
- `POST /backend/contact-form.php` - Submit contact form
- `GET /backend/admin/getcontact-us.php` - Get contact submissions

### Password Reset
- `POST /backend/send_otp.php` - Send OTP for password reset
- `POST /backend/verify_otp.php` - Verify OTP
- `POST /backend/reset_password.php` - Reset password

## 🤝 Contributing

### Development Setup
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

### Code Standards
- Follow PSR-12 coding standards
- Use meaningful variable names
- Add comments for complex logic
- Ensure proper error handling

## 📄 License

This project is proprietary software owned by 7StarDigitizing. All rights reserved.

## 📞 Support

For technical support or questions:
- **Email**: admin@7StarDigitizing.com
- **Phone**: +92 000 000 0000
- **Address**: Karachi, Sindh, Pakistan

## 🔄 Version History

- **v1.0.0** - Initial release with core functionality
- **v1.1.0** - Added admin panel and user management
- **v1.2.0** - Enhanced order tracking and payment integration
- **v1.3.0** - Improved UI/UX and mobile responsiveness

---

**Built with ❤️ by the 7StarDigitizing Team**

*Last updated: January 2024*
