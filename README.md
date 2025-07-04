# Job Portal

A comprehensive job portal system with three user panels: Admin, Company, and Job Seeker.

## Features

### Admin Panel
- Dashboard with statistics
- Manage categories
- Manage companies
- Manage users
- Manage jobs
- View total applications

### Company Panel
- Company dashboard
- Post new jobs
- Manage posted jobs
- View job applications
- Edit company profile
- View application statistics

### Job Seeker Panel
- User dashboard
- Search jobs
- Apply for jobs
- View application status
- Edit profile
- View job history

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

## Installation

1. Clone the repository to your web server directory
2. Create a MySQL database named `job_portal`
3. Import the `database.sql` file to create the necessary tables
4. Configure the database connection in `config/database.php`
5. Access the application through your web browser

## Default Admin Credentials

- Email: admin@jobportal.com
- Password: password

## Directory Structure

```
job_portal/
├── admin/              # Admin panel files
├── company/            # Company panel files
├── user/               # User panel files
├── config/             # Configuration files
├── assets/             # CSS, JS, and other assets
├── index.php           # Main entry point
├── login.php           # Login page
├── register.php        # Registration page
├── database.sql        # Database structure
└── README.md           # This file
```

## Security Features

- Password hashing
- Session management
- Input validation
- SQL injection prevention
- XSS protection

## Contributing

Feel free to submit issues and enhancement requests.

## License

This project is licensed under the MIT License. 