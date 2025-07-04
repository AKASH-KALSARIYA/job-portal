<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Job Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), #1976d2);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('assets/images/pattern.svg') repeat;
            opacity: 0.1;
        }

        .mission-section {
            padding: 80px 0;
            background: #fff;
        }

        .stats-section {
            background: var(--light-gray);
            padding: 80px 0;
        }

        .team-section {
            padding: 80px 0;
            background: #fff;
        }

        .stat-card {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .team-card {
            text-align: center;
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-10px);
        }

        .team-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: var(--box-shadow);
        }

        .social-links {
            margin-top: 15px;
        }

        .social-links a {
            color: var(--text-color);
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color: var(--primary-color);
        }

        .mission-card {
            padding: 30px;
            border-radius: var(--border-radius);
            background: white;
            box-shadow: var(--box-shadow);
            height: 100%;
            transition: transform 0.3s ease;
        }

        .mission-card:hover {
            transform: translateY(-5px);
        }

        .mission-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: var(--text-color);
            margin-bottom: 20px;
        }

        .section-title p {
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Job Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="jobs.php">Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="btn btn-outline-light me-2">My Profile</a>
                        <a href="logout.php" class="btn btn-danger">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-light me-2">Employee Login</a>
                        <a href="company/login.php" class="btn btn-primary">Company Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 mb-4">Connecting Talent with Opportunity</h1>
                    <p class="lead mb-4">We're on a mission to revolutionize the way people find jobs and companies hire talent. Our platform brings together job seekers and employers in a seamless, efficient way.</p>
                    <a href="jobs.php" class="btn btn-light btn-lg">Explore Jobs</a>
                </div>
                <div class="col-md-6">
                    <img src="assets/images/about-hero.svg" alt="About Us Hero" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mission-section">
        <div class="container">
            <div class="section-title">
                <h2>Our Mission</h2>
                <p>We strive to create meaningful connections between talented individuals and forward-thinking companies.</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="mission-card">
                        <div class="mission-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3>Vision</h3>
                        <p>To be the leading platform that transforms how people discover and secure their dream careers.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="mission-card">
                        <div class="mission-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3>Values</h3>
                        <p>Integrity, innovation, and inclusivity are at the heart of everything we do.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="mission-card">
                        <div class="mission-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Goals</h3>
                        <p>To create a world where everyone can find meaningful work and achieve their career aspirations.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="section-title">
                <h2>Our Impact</h2>
                <p>Making a difference in the job market through our platform.</p>
            </div>
            <div class="row">
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">10K+</div>
                        <div class="stat-label">Active Jobs</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">5K+</div>
                        <div class="stat-label">Companies</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">50K+</div>
                        <div class="stat-label">Job Seekers</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">15K+</div>
                        <div class="stat-label">Success Stories</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <div class="section-title">
                <h2>Meet Our Team</h2>
                <p>The passionate individuals behind our success.</p>
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="team-card">
                        <img src="assets/images/team1.jpg" alt="Team Member" class="team-image">
                        <h4>John Doe</h4>
                        <p class="text-muted">CEO & Founder</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="team-card">
                        <img src="assets/images/team2.jpg" alt="Team Member" class="team-image">
                        <h4>Jane Smith</h4>
                        <p class="text-muted">Head of Operations</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="team-card">
                        <img src="assets/images/team3.jpg" alt="Team Member" class="team-image">
                        <h4>Mike Johnson</h4>
                        <p class="text-muted">Lead Developer</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="team-card">
                        <img src="assets/images/team4.jpg" alt="Team Member" class="team-image">
                        <h4>Sarah Wilson</h4>
                        <p class="text-muted">Marketing Director</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 