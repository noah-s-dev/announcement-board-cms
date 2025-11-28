# 📢 Announcement Board CMS

A modern, secure, and user-friendly Content Management System for managing and displaying announcements. Built with PHP and modern web technologies, this system provides a clean public interface and a powerful admin panel for content management.

## 🛠️ Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Framework**: Bootstrap 5.3.0
- **Icons**: Font Awesome 6.0.0
- **Fonts**: Inter (Google Fonts)
- **Security**: CSRF Protection, Rate Limiting, Password Hashing
- **Server**: Apache/Nginx compatible

## 📋 Project Overview

The Announcement Board CMS is designed to provide organizations, schools, businesses, and communities with a simple yet powerful platform to share announcements, news, and updates. The system features a modern, responsive design with enhanced security measures and an intuitive user interface.

### Key Features

- **🎨 Modern UI/UX**: Beautiful, responsive design with glassmorphism effects
- **🔒 Enhanced Security**: No default credentials, rate limiting, CSRF protection
- **📱 Mobile Responsive**: Optimized for all devices and screen sizes
- **⚡ Fast Performance**: Optimized code and efficient database queries
- **🔍 Search Functionality**: Real-time search through announcements
- **📄 Pagination**: Efficient content organization with pagination
- **👥 Multi-Admin Support**: Multiple admin accounts with secure authentication
- **📊 Admin Dashboard**: Comprehensive management interface
- **🎯 SEO Friendly**: Clean URLs and meta tags for better search visibility
- **🛡️ Security Logging**: Comprehensive security event logging

## 👥 User Roles

### **Public Users**
- View published announcements
- Search through announcements
- Read full announcement content
- Navigate through paginated results

### **Administrators**
- Create, edit, and delete announcements
- Manage announcement publication status
- Access comprehensive admin dashboard
- View system statistics and logs
- Manage admin accounts (if multiple admins)

## 📁 Project Structure

```
announcement-board-cms/
├── admin/                     # Admin interface
│   ├── login.php             # Secure admin login
│   ├── setup.php             # First-time admin setup
│   ├── dashboard.php         # Admin dashboard
│   ├── announcements.php     # Announcement management
│   ├── announcement_create.php
│   ├── announcement_edit.php
│   ├── announcement_delete.php
│   ├── announcement_toggle.php
│   ├── logout.php
│   └── includes/
│       ├── header.php        # Admin header
│       └── sidebar.php       # Admin sidebar
├── assets/                   # Static assets
│   ├── css/
│   │   ├── public.css        # Public interface styles
│   │   └── admin.css         # Admin interface styles
│   └── js/
│       ├── public.js         # Public interface scripts
│       └── admin.js          # Admin interface scripts
├── config/
│   └── config.php           # Application configuration
├── includes/                # Core PHP classes
│   ├── Admin.php           # Admin management class
│   ├── Announcement.php    # Announcement management class
│   ├── Database.php        # Database connection class
│   ├── Security.php        # Security utilities class
│   └── functions.php       # Helper functions
├── logs/                   # System logs
├── sql/
│   └── database_setup.sql  # Database schema
├── index.php              # Public homepage
├── announcement.php       # Individual announcement view
├── 404.php               # Error page
└── README.md             # This file
```

## 🚀 Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (optional, for dependency management)

### Installation Steps

1. **Clone or Download the Project**
   ```bash
   git clone https://github.com/noah-s-dev/announcement-board-cms.git
   cd announcement-board-cms
   ```

2. **Place Project in Web Directory**
   - Place the project folder in your web server's document root
   - For example: `C:\xampp\htdocs\announcement-board-cms` or `/var/www/html/announcement-board-cms`
   - The project will automatically detect if it's in a subdirectory

3. **Database Setup**
   ```sql
   mysql -u your_username -p < sql/database_setup.sql
   ```

4. **Configuration**
   Edit `config/config.php` with your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'announcement_board');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```
   
   **Note**: The BASE_URL is automatically detected based on your server configuration. No manual configuration needed!

5. **Admin Account Setup**
   - Navigate to `http://localhost/announcement-board-cms/admin/setup.php` (or your project path)
   - Create your first admin account
   - **Important**: Delete `admin/setup.php` after use for security

6. **File Permissions** (Linux/Mac only)
   ```bash
   chmod 755 logs/
   chmod 644 config/config.php
   ```

7. **First Login**
   - Go to `http://localhost/announcement-board-cms/admin/login.php` (or your project path)
   - Use your created credentials
   - Start managing announcements!

### Access URLs

- **Public Site**: `http://localhost/announcement-board-cms/` (or your project path)
- **Admin Login**: `http://localhost/announcement-board-cms/admin/login.php`
- **Admin Dashboard**: `http://localhost/announcement-board-cms/admin/dashboard.php`

**Note**: Replace `announcement-board-cms` with your actual project folder name. The system automatically handles subdirectory paths and redirects.

## 📖 Usage

### For Administrators

1. **Login**: Access the admin panel at `admin/login.php`
2. **Dashboard**: View system overview and statistics
3. **Create Announcements**: Use the "Create New" button
4. **Manage Content**: Edit, delete, or toggle publication status
5. **Monitor**: Check security logs and system activity

### For Public Users

1. **Browse**: Visit the homepage to see latest announcements
2. **Search**: Use the search bar to find specific content
3. **Read**: Click on announcements to view full content
4. **Navigate**: Use pagination to browse through older announcements

### Key Features Usage

- **Search**: Real-time search through announcement titles and content
- **Pagination**: Navigate through large numbers of announcements
- **Responsive Design**: Works seamlessly on desktop, tablet, and mobile
- **Security**: All admin actions are logged and protected

## 🎯 Intended Use

This Announcement Board CMS is designed for:

- **Educational Institutions**: School announcements, event notifications
- **Business Organizations**: Company updates, policy changes
- **Community Groups**: Local news, event announcements
- **Non-Profit Organizations**: Program updates, volunteer opportunities
- **Government Entities**: Public notices, policy announcements
- **Small to Medium Businesses**: Internal communications, client updates

### Perfect For:
- Organizations needing a simple announcement system
- Teams requiring secure content management
- Communities wanting to share updates efficiently
- Businesses looking for a professional announcement platform

### Not Suitable For:
- Complex content management needs
- Multi-site deployments
- Advanced user management requirements
- E-commerce or transactional websites

---

# 📄 License

**License for RiverTheme**

RiverTheme makes this project available for demo, instructional, and personal use. You can ask for or buy a license from [RiverTheme.com](https://RiverTheme.com) if you want a pro website, sophisticated features, or expert setup and assistance. A Pro license is needed for production deployments, customizations, and commercial use.

**Disclaimer**

The free version is offered "as is" with no warranty and might not function on all devices or browsers. It might also have some coding or security flaws. For additional information or to get a Pro license, please get in touch with [RiverTheme.com](https://RiverTheme.com).

---