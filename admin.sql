-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS job_portal;

-- Use the database
USE job_portal;

-- Drop tables in correct order (child tables first)
DROP TABLE IF EXISTS admin_login_attempts;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS admins;

-- Create the admins table
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

-- Insert the default admin user with simple password
INSERT INTO admins (email, password, first_name, last_name, is_active) 
VALUES ('admin@jobportal.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 1);

-- Create index for faster lookups
CREATE INDEX idx_admin_email ON admins(email);

-- Create password resets table
CREATE TABLE password_resets (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    used TINYINT(1) DEFAULT 0,
    expiry DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create indexes for password resets
CREATE INDEX idx_reset_email ON password_resets(email);
CREATE INDEX idx_reset_token ON password_resets(token);

-- Create admin login attempts table
CREATE TABLE admin_login_attempts (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    admin_id INT(11),
    ip_address VARCHAR(45) NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success TINYINT(1) DEFAULT 0,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create indexes for tracking login attempts
CREATE INDEX idx_login_ip ON admin_login_attempts(ip_address);
CREATE INDEX idx_login_time ON admin_login_attempts(attempted_at); 