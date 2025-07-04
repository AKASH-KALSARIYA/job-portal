<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/db_config.php';

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Debug information
    error_log("Login attempt - Email: " . $email);

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    if (empty($errors)) {
        // Ensure we're using the correct database
        if (!mysqli_select_db($conn, 'job_portal')) {
            die("Error selecting database: " . mysqli_error($conn));
        }

        $stmt = $conn->prepare("SELECT id, username, password, email FROM users WHERE email = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                
                // Store profile photo in session if exists
                $profile_stmt = $conn->prepare("SELECT profile_photo FROM users WHERE id = ?");
                $profile_stmt->bind_param("i", $user['id']);
                $profile_stmt->execute();
                $profile_result = $profile_stmt->get_result();
                $profile_data = $profile_result->fetch_assoc();
                $_SESSION['profile_photo'] = $profile_data['profile_photo'];
                
                // Log successful login
                error_log("User {$user['id']} logged in successfully");
                
                // Redirect to jobs page instead of dashboard
                header("Location: jobs.php");
                exit();
            } else {
                $errors['general'] = 'Invalid email or password';
                error_log("Password verification failed for email: " . $email);
            }
        } else {
            $errors['general'] = 'Invalid email or password';
            error_log("No user found with email: " . $email);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Job Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1d4ed8;
            --accent-color: #3b82f6;
            --gradient-start: #4f46e5;
            --gradient-end: #2563eb;
        }

        body {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
            flex-direction: row-reverse;
        }

        .login-form-section {
            flex: 1;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
        }

        .login-image-section {
            flex: 1;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-image-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('assets/images/career.svg') no-repeat center center;
            background-size: 80%;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% {
                transform: translatey(0px);
            }
            50% {
                transform: translatey(-20px);
            }
            100% {
                transform: translatey(0px);
            }
        }

        .form-control {
            height: 50px;
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
            background: rgba(255, 255, 255, 1);
        }

        .btn-login {
            height: 50px;
            border-radius: 10px;
            background: var(--primary-color);
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }

        .login-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 30px;
            background: linear-gradient(to right, var(--gradient-start), var(--gradient-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-text {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 30px 0;
            position: relative;
            z-index: 1;
        }

        .feature-list li {
            margin: 15px 0;
            display: flex;
            align-items: center;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .feature-list li i {
            margin-right: 10px;
            color: #fff;
            font-size: 1.2rem;
        }

        .social-login {
            margin-top: 20px;
        }

        .social-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            color: #1f2937;
            margin: 0 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .social-btn:hover {
            transform: translateY(-3px) rotate(8deg);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            color: var(--primary-color);
        }

        .form-floating > label {
            padding: 1rem;
            color: #6b7280;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 500px;
            }
            
            .login-image-section {
                padding: 30px;
                min-height: 300px;
            }

            .welcome-text {
                font-size: 2rem;
            }

            .feature-list li {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-form-section">
            <h2 class="login-title text-center">Welcome Back!</h2>
            
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" novalidate>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                           id="email" name="email" placeholder="name@example.com" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    <label for="email">Email address</label>
                    <?php if (isset($errors['email'])): ?>
                        <div class="error-message"><?php echo $errors['email']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-floating mb-4">
                    <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                           id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                    <?php if (isset($errors['password'])): ?>
                        <div class="error-message"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <a href="forgot-password.php" class="text-primary text-decoration-none">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-login w-100 mb-3">Sign In</button>
                
                <div class="text-center">
                    <p class="mb-0">Don't have an account? <a href="register.php" class="text-primary text-decoration-none">Sign Up</a></p>
                </div>

                <div class="text-center social-login">
                    <p class="text-muted mb-3">Or login with</p>
                    <a href="#" class="social-btn"><i class="fab fa-google"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-github"></i></a>
                </div>
            </form>
        </div>

        <div class="login-image-section">
            <h1 class="welcome-text">Find Your Dream Job</h1>
            <p class="lead">Connect with top companies and discover exciting opportunities</p>
            
            <ul class="feature-list">
                <li><i class="fas fa-check-circle"></i> Access to thousands of job listings</li>
                <li><i class="fas fa-check-circle"></i> Easy application process</li>
                <li><i class="fas fa-check-circle"></i> Professional profile building</li>
                <li><i class="fas fa-check-circle"></i> Direct communication with employers</li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 