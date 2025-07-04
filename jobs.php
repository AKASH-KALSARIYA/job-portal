<?php
session_start();
require_once 'config/db_config.php';
require_once 'includes/header.php';

// Initialize filters
$search = $_GET['search'] ?? '';
$location = $_GET['location'] ?? '';
$category = $_GET['category'] ?? '';
$experience = $_GET['experience'] ?? '';
$salary_min = $_GET['salary_min'] ?? '';
$work_mode = $_GET['work_mode'] ?? '';

// Base query
$query = "SELECT j.*, c.name as company_name, c.logo as company_logo 
          FROM jobs j 
          LEFT JOIN companies c ON j.company_id = c.id 
          WHERE 1=1";

// Add filters
if (!empty($search)) {
    $query .= " AND (j.title LIKE ? OR j.description LIKE ? OR j.skills LIKE ?)";
}
if (!empty($location)) {
    $query .= " AND j.location = ?";
}
if (!empty($category)) {
    $query .= " AND j.category = ?";
}
if (!empty($experience)) {
    $query .= " AND j.experience_required <= ?";
}
if (!empty($salary_min)) {
    $query .= " AND j.salary_min >= ?";
}
if (!empty($work_mode)) {
    $query .= " AND j.work_mode = ?";
}

$query .= " ORDER BY j.created_at DESC";

// Prepare and execute query
$stmt = $conn->prepare($query);
if ($stmt) {
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "sss";
    }
    if (!empty($location)) {
        $params[] = $location;
        $types .= "s";
    }
    if (!empty($category)) {
        $params[] = $category;
        $types .= "s";
    }
    if (!empty($experience)) {
        $params[] = $experience;
        $types .= "i";
    }
    if (!empty($salary_min)) {
        $params[] = $salary_min;
        $types .= "d";
    }
    if (!empty($work_mode)) {
        $params[] = $work_mode;
        $types .= "s";
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $jobs = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $jobs = [];
}

// Get all locations for filter - with error handling
$locations = [];
try {
    $location_result = $conn->query("SELECT DISTINCT location FROM jobs WHERE location IS NOT NULL ORDER BY location");
    if ($location_result) {
        $locations = $location_result->fetch_all(MYSQLI_ASSOC);
    }
} catch (Exception $e) {
    error_log("Error fetching locations: " . $e->getMessage());
}

// Get all categories for filter - with error handling
$categories = [];
try {
    $category_result = $conn->query("SELECT DISTINCT category FROM jobs WHERE category IS NOT NULL ORDER BY category");
    if ($category_result) {
        $categories = $category_result->fetch_all(MYSQLI_ASSOC);
    }
} catch (Exception $e) {
    error_log("Error fetching categories: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <!-- Back to Dashboard Button -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Enter skills (e.g., Java, Python)" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="location">
                        <option value="">Select Location</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?php echo htmlspecialchars($loc['location']); ?>" 
                                    <?php echo $location === $loc['location'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($loc['location']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="category">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['category']); ?>"
                                    <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Filters -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Advanced Filters</h5>
                </div>
                <div class="card-body">
                    <form action="" method="GET">
                        <!-- Experience Filter -->
                        <div class="mb-3">
                            <label class="form-label">Experience</label>
                            <input type="range" class="form-range" name="experience" min="0" max="10" 
                                   value="<?php echo htmlspecialchars($experience); ?>" 
                                   oninput="this.nextElementSibling.value = this.value + '+ years'">
                            <output><?php echo $experience; ?>+ years</output>
                        </div>

                        <!-- Salary Filter -->
                        <div class="mb-3">
                            <label class="form-label">Minimum Salary</label>
                            <input type="range" class="form-range" name="salary_min" min="0" max="200000" step="1000"
                                   value="<?php echo htmlspecialchars($salary_min); ?>"
                                   oninput="this.nextElementSibling.value = this.value + '+ per month'">
                            <output><?php echo $salary_min; ?>+ per month</output>
                        </div>

                        <!-- Work Mode Filter -->
                        <div class="mb-3">
                            <label class="form-label">Work Mode</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="work_mode" value="office" 
                                       <?php echo $work_mode === 'office' ? 'checked' : ''; ?>>
                                <label class="form-check-label">Office</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="work_mode" value="remote"
                                       <?php echo $work_mode === 'remote' ? 'checked' : ''; ?>>
                                <label class="form-check-label">Remote</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-outline-primary w-100">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Job Listings -->
        <div class="col-md-9">
            <h5 class="mb-3"><?php echo count($jobs); ?> jobs found</h5>
            
            <?php foreach ($jobs as $job): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?php echo !empty($job['company_logo']) ? 'uploads/company_logos/' . htmlspecialchars($job['company_logo']) : 'assets/img/default-company.png'; ?>" 
                                 alt="<?php echo htmlspecialchars($job['company_name']); ?>" 
                                 class="me-3" style="width: 48px; height: 48px; object-fit: cover;">
                            <div>
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($job['title']); ?></h5>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($job['company_name']); ?></p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <span class="badge bg-primary me-2">
                                <i class="fas fa-money-bill-wave me-1"></i>
                                $<?php echo number_format($job['salary_min']); ?> - $<?php echo number_format($job['salary_max']); ?>/month
                            </span>
                            <span class="badge bg-secondary me-2">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?php echo htmlspecialchars($job['location']); ?>
                            </span>
                            <span class="badge bg-info me-2">
                                <i class="fas fa-clock me-1"></i>
                                <?php echo htmlspecialchars($job['experience_required']); ?> years
                            </span>
                            <span class="badge bg-success">
                                <i class="fas fa-building me-1"></i>
                                <?php echo htmlspecialchars($job['work_mode']); ?>
                            </span>
                        </div>

                        <p class="card-text"><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <?php 
                                $skills = explode(',', $job['skills']);
                                foreach ($skills as $skill): 
                                    $skill = trim($skill);
                                    if (!empty($skill)):
                                ?>
                                    <span class="badge bg-light text-dark me-2"><?php echo htmlspecialchars($skill); ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                            <a href="job-details.php?id=<?php echo $job['id']; ?>" class="btn btn-primary">Apply Now</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($jobs)): ?>
                <div class="text-center py-5">
                    <img src="assets/img/no-jobs.svg" alt="No jobs found" style="width: 200px; margin-bottom: 20px;">
                    <h4>No jobs found</h4>
                    <p class="text-muted">Try adjusting your search criteria</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Links Section -->
    <div class="row mt-5 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <a href="jobs.php" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-search"></i>
                                Browse Jobs
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="user/applications.php" class="btn btn-outline-info w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-file-text"></i>
                                My Applications
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="user/profile.php" class="btn btn-outline-success w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-person-circle"></i>
                                Update Profile
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="user/settings.php" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-gear"></i>
                                Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 