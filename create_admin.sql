-- First, make sure we're using the correct database
USE job_portal;

-- Drop the existing admin if any
DELETE FROM admins WHERE email = 'admin@jobportal.com';

-- Create new admin with correct password hash
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
SELECT * FROM admins WHERE email = 'admin@jobportal.com'; 