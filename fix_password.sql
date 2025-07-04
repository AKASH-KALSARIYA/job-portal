-- First, make sure we're using the correct database
USE job_portal;

-- Update the admin password with a properly hashed password
-- The password will be 'admin123'
UPDATE admins 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE email = 'admin@jobportal.com';

-- Verify the update
SELECT email, password FROM admins WHERE email = 'admin@jobportal.com'; 