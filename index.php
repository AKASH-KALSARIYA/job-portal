<?php
session_start();

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'job_portal';

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if database exists
$db_exists = mysqli_select_db($conn, $db_name);
if (!$db_exists) {
    // Redirect to setup page if database doesn't exist
    header('Location: config/setup_database.php');
    exit();
}

// Check if required tables exist
$required_tables = ['categories', 'states', 'subcategories'];
$missing_tables = false;

foreach ($required_tables as $table) {
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($table_check) == 0) {
        $missing_tables = true;
        break;
    }
}

if ($missing_tables) {
    // Redirect to setup page if any required table is missing
    header('Location: config/setup_database.php');
    exit();
}

// Get categories from database with error handling
try {
    $categories_query = "SELECT c.*, GROUP_CONCAT(sc.name) as subcategories 
                        FROM categories c 
                        LEFT JOIN subcategories sc ON c.id = sc.category_id 
                        GROUP BY c.id";
    $categories_result = mysqli_query($conn, $categories_query);

    if (!$categories_result) {
        throw new Exception(mysqli_error($conn));
    }

    $categories = [];
    while ($row = mysqli_fetch_assoc($categories_result)) {
        $categories[$row['id']] = [
            'name' => $row['name'],
            'description' => $row['description'],
            'subcategories' => $row['subcategories'] ? explode(',', $row['subcategories']) : []
        ];
    }
} catch (Exception $e) {
    // If there's an error with the complex query, fall back to simple categories query
    $categories_query = "SELECT * FROM categories";
    $categories_result = mysqli_query($conn, $categories_query);
    
    $categories = [];
    while ($row = mysqli_fetch_assoc($categories_result)) {
        $categories[$row['id']] = [
            'name' => $row['name'],
            'description' => $row['description'],
            'subcategories' => []
        ];
    }
}

// Get states from database
$states_query = "SELECT * FROM states WHERE country = 'India' ORDER BY name";
$states_result = mysqli_query($conn, $states_query);
$states = [];
while ($row = mysqli_fetch_assoc($states_result)) {
    $states[$row['id']] = $row['name'];
}

// Sample featured companies
$featured_companies = [
    [
        'name' => 'Amazon',
        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg',
        'description' => 'Global e-commerce and cloud computing company'
    ],
    [
        'name' => 'Flipkart',
        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/2/2f/Flipkart_logo.png',
        'description' => 'Indian e-commerce company'
    ],
    [
        'name' => 'Twitter',
        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/4/4f/Twitter-logo.svg',
        'description' => 'Social media platform'
    ],
    [
        'name' => 'Ody',
        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/1/1b/Ody_logo.png',
        'description' => 'Technology solutions provider'
    ],
    [
        'name' => 'Microsoft',
        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/4/44/Microsoft_logo.svg',
        'description' => 'Technology corporation'
    ]
];

// Sample recent jobs
$recent_jobs = [
    [
        'title' => 'Senior Software Engineer',
        'company' => 'Amazon',
        'location' => 'Bangalore, India',
        'type' => 'Full-time',
        'salary' => '₹25L - ₹35L'
    ],
    [
        'title' => 'Product Manager',
        'company' => 'Flipkart',
        'location' => 'Mumbai, India',
        'type' => 'Full-time',
        'salary' => '₹20L - ₹30L'
    ],
    [
        'title' => 'Data Scientist',
        'company' => 'Twitter',
        'location' => 'Remote',
        'type' => 'Full-time',
        'salary' => '₹18L - ₹28L'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal - Find Your Dream Job</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), #1976d2);
            padding: 80px 0;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('assets/images/hero-bg.jpg') center/cover;
            opacity: 0.1;
        }

        .search-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: var(--border-radius);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .search-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            height: 50px;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: white;
            box-shadow: none;
        }

        .search-select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            height: 50px;
        }

        .search-select option {
            background: var(--primary-color);
            color: white;
        }

        .search-select:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: none;
        }

        .search-btn {
            height: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stats-section {
            padding: 60px 0;
            background: var(--light-gray);
        }

        .stat-card {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 1.1rem;
        }

        .feature-section {
            padding: 80px 0;
        }

        .feature-card {
            padding: 30px;
            text-align: center;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
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
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="jobs.php">Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
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

    <!-- Hero Section with Search -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h1 class="display-4 mb-4">Find Your Dream Job Today</h1>
                    <p class="lead mb-4">Connect with top companies and discover exciting career opportunities across India.</p>
                    <div class="d-flex align-items-center">
                        <div class="me-4">
                            <i class="fas fa-check-circle me-2"></i>
                            <span>Verified Companies</span>
                        </div>
                        <div class="me-4">
                            <i class="fas fa-check-circle me-2"></i>
                            <span>Latest Jobs</span>
                        </div>
                        <div>
                            <i class="fas fa-check-circle me-2"></i>
                            <span>Easy Apply</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="search-card">
                        <form action="jobs.php" method="GET">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <input type="text" class="form-control search-input" name="keyword" 
                                           placeholder="Search by job title, company, or skills">
                                </div>
                                <div class="col-md-6">
                                    <select class="form-select search-select" name="category" id="categorySelect">
                                        <option value="">All Categories</option>
                                        <?php foreach ($categories as $id => $category): ?>
                                            <option value="<?php echo $id; ?>">
                                                <?php echo $category['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-select search-select" name="state" id="stateSelect">
                                        <option value="">All States</option>
                                        <?php foreach ($states as $id => $state): ?>
                                            <option value="<?php echo $id; ?>">
                                                <?php echo $state; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-light search-btn w-100">
                                        <i class="fas fa-search me-2"></i>Search Jobs
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">10,000+</div>
                        <div class="stat-label">Active Jobs</div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">5,000+</div>
                        <div class="stat-label">Companies</div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">1M+</div>
                        <div class="stat-label">Candidates</div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Cities</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="feature-section">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Us</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h4>Latest Jobs</h4>
                        <p>Access thousands of verified job postings from top companies across India.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h4>Career Growth</h4>
                        <p>Find opportunities that match your skills and help you grow professionally.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h4>Top Companies</h4>
                        <p>Connect with leading companies and startups across various industries.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stateSelect = document.getElementById('stateSelect');
            const citySelect = document.getElementById('citySelect');
            
            if (stateSelect && citySelect) {
                stateSelect.addEventListener('change', function() {
                    const stateId = this.value;
                    citySelect.innerHTML = '<option value="">All Cities</option>';
                    
                    if (stateId) {
                        fetch(`get_cities.php?state_id=${stateId}`)
                            .then(response => response.json())
                            .then(cities => {
                                cities.forEach(city => {
                                    const option = document.createElement('option');
                                    option.value = city.id;
                                    option.textContent = city.name;
                                    citySelect.appendChild(option);
                                });
                            });
                    }
                });
            }
        });
    </script>
</body>
</html> 