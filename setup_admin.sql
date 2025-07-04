-- Create database if not exists
CREATE DATABASE IF NOT EXISTS job_portal;

-- Use the database
USE job_portal;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS admin_login_attempts;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS admins;

-- Create admins table
CREATE TABLE admins (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert admin account
INSERT INTO admins (
    email,
    password,
    first_name,
    last_name,
    is_active
) VALUES (
    'admin@jobportal.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Admin',
    'User',
    1
);

-- Verify the admin was created
SELECT * FROM admins; 