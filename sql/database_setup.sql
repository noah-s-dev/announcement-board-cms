-- Announcement Board CMS Database Setup
-- Create database and tables for the announcement board system

-- Create database
CREATE DATABASE IF NOT EXISTS announcement_board CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE announcement_board;

-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create announcements table
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    admin_id INT NOT NULL,
    is_published BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
    INDEX idx_published (is_published),
    INDEX idx_created_at (created_at)
);

-- IMPORTANT: No default admin user is created for security reasons.
-- To create your first admin account, run the setup script:
-- 1. Navigate to: admin/setup.php
-- 2. Create your admin account securely
-- 3. Delete the setup.php file after use for security

-- Insert sample announcements
INSERT INTO announcements (title, content, admin_id) VALUES 
('Welcome to Our Announcement Board', 'This is the first announcement on our new board. We hope you find this system useful for staying updated with the latest news and information.', 1),
('System Maintenance Notice', 'We will be performing scheduled maintenance on our systems this weekend. Please expect brief interruptions in service between 2:00 AM and 4:00 AM on Saturday.', 1),
('New Features Coming Soon', 'We are excited to announce that new features will be added to our platform next month. Stay tuned for more updates and improvements to enhance your experience.', 1);

