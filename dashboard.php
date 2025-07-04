<?php
session_start();
require_once 'config/sample_data.php';
require_once 'config/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get categories from database
$categories_query = "SELECT c.*, GROUP_CONCAT(sc.name) as subcategories 
                    FROM categories c 
                    LEFT JOIN subcategories sc ON c.id = sc.category_id 
                    GROUP BY c.id";
$categories_result = mysqli_query($conn, $categories_query);
$categories = [];
while ($row = mysqli_fetch_assoc($categories_result)) {
    $categories[$row['id']] = [
        'name' => $row['name'],
        'description' => $row['description'],
        'subcategories' => $row['subcategories'] ? explode(',', $row['subcategories']) : []
    ];
}

// Get states from database
$states_query = "SELECT * FROM states WHERE country = 'India' ORDER BY name";
$states_result = mysqli_query($conn, $states_query);
$states = [];
while ($row = mysqli_fetch_assoc($states_result)) {
    $states[$row['id']] = $row['name'];
}

// Handle search
$search_results = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keyword = $_POST['keyword'] ?? '';
    $category_id = $_POST['category'] ?? '';
    $state_id = $_POST['state'] ?? '';
    $city_id = $_POST['city'] ?? '';
    
    // Get city name if city_id is selected
    $city_name = '';
    if ($city_id) {
        $city_query = "SELECT name FROM cities WHERE id = ?";
        $stmt = mysqli_prepare($conn, $city_query);
        mysqli_stmt_bind_param($stmt, "i", $city_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $city_name = $row['name'];
        }
    }
    
    // Get state name if state_id is selected
    $state_name = '';
    if ($state_id) {
        $state_name = $states[$state_id];
    }
    
    // Filter companies based on search criteria
    $search_results = array_filter($companies, function($company) use ($keyword, $category_id, $state_name, $city_name) {
        $matches = true;
        
        // Keyword search in company name, industry, and positions
        if (!empty($keyword)) {
            $matches = $matches && (
                stripos($company['name'], $keyword) !== false ||
                stripos($company['industry'], $keyword) !== false ||
                stripos($company['positions'], $keyword) !== false
            );
        }
        
        // Category/Industry filter
        if (!empty($category_id)) {
            $category_name = $categories[$category_id]['name'];
            $matches = $matches && ($company['industry'] === $category_name);
        }
        
        // Location filter
        if (!empty($state_name) || !empty($city_name)) {
            $company_location = explode(', ', $company['location']);
            $company_city = $company_location[0];
            $company_state = $company_location[1] ?? '';
            
            if (!empty($state_name)) {
                $matches = $matches && ($company_state === $state_name);
            }
            if (!empty($city_name)) {
                $matches = $matches && ($company_city === $city_name);
            }
        }
        
        return $matches;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Job Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .search-section {
            background: linear-gradient(135deg, var(--primary-color), #1976d2);
            padding: 40px 0;
            color: white;
        }

        .search-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: var(--border-radius);
            backdrop-filter: blur(10px);
        }

        .search-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
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

        .result-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
        }

        .result-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .industry-badge {
            background: #e3f2fd;
            color: var(--primary-color);
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 0.9rem;
            margin-right: 10px;
        }

        .package-badge {
            background: var(--primary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-top: 10px;
            display: inline-block;
        }

        .location-text {
            color: #666;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        .no-results {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
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
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
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
                    <a href="profile.php" class="btn btn-outline-light me-2">My Profile</a>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <div class="search-card">
                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control search-input" name="keyword" 
                                   placeholder="Search by company, industry, or position" 
                                   value="<?php echo $_POST['keyword'] ?? ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select search-select" name="category" id="categorySelect">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $id => $category): ?>
                                    <option value="<?php echo $id; ?>" 
                                            <?php echo (isset($_POST['category']) && $_POST['category'] == $id) ? 'selected' : ''; ?>>
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select search-select" name="state" id="stateSelect">
                                <option value="">All States</option>
                                <?php foreach ($states as $id => $state): ?>
                                    <option value="<?php echo $id; ?>"
                                            <?php echo (isset($_POST['state']) && $_POST['state'] == $id) ? 'selected' : ''; ?>>
                                        <?php echo $state; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select search-select" name="city" id="citySelect">
                                <option value="">All Cities</option>
                                <?php if (isset($_POST['state']) && !empty($_POST['state'])): 
                                    $cities_query = "SELECT * FROM cities WHERE state_id = ? ORDER BY name";
                                    $stmt = mysqli_prepare($conn, $cities_query);
                                    mysqli_stmt_bind_param($stmt, "i", $_POST['state']);
                                    mysqli_stmt_execute($stmt);
                                    $cities_result = mysqli_stmt_get_result($stmt);
                                    while ($city = mysqli_fetch_assoc($cities_result)): ?>
                                        <option value="<?php echo $city['id']; ?>"
                                                <?php echo (isset($_POST['city']) && $_POST['city'] == $city['id']) ? 'selected' : ''; ?>>
                                            <?php echo $city['name']; ?>
                                        </option>
                                    <?php endwhile;
                                endif; ?>
                            </select>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-light">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Results Section -->
    <section class="py-5">
        <div class="container">
            <?php if (!empty($search_results)): ?>
                <h2 class="mb-4">Search Results (<?php echo count($search_results); ?>)</h2>
                <div class="row">
                    <?php foreach ($search_results as $company): ?>
                        <div class="col-md-6 mb-4">
                            <div class="result-card">
                                <h4><?php echo $company['name']; ?></h4>
                                <span class="industry-badge"><?php echo $company['industry']; ?></span>
                                <span class="package-badge">Up to $<?php echo $company['highest_package']; ?>/year</span>
                                <div class="location-text">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo $company['location']; ?>
                                </div>
                                <p class="mb-2 mt-3"><strong>Top Positions:</strong></p>
                                <p class="text-muted"><?php echo $company['positions']; ?></p>
                                <small class="text-muted"><?php echo $company['company_type']; ?> Company</small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="no-results">
                    <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                    <h3>No Results Found</h3>
                    <p class="text-muted">Try adjusting your search criteria</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stateSelect = document.getElementById('stateSelect');
            const citySelect = document.getElementById('citySelect');
            
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
        });
    </script>
</body>
</html> 