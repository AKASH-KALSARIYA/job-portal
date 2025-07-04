<?php
session_start();
require_once 'config/db_config.php';

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // Prepare and execute the SQL query
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        
        if ($stmt->execute()) {
            $success_message = "Your message has been sent successfully! We'll get back to you soon.";
            // Clear form data after successful submission
            $name = $email = $subject = $message = '';
        } else {
            $error_message = "Sorry, there was an error sending your message. Please try again later.";
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<div class="container-fluid bg-primary py-5 mb-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <h1 class="display-4 text-white">Contact Us</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Contact</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <!-- Contact Information -->
        <div class="col-lg-4 mb-4">
            <div class="bg-light p-4 rounded">
                <h3 class="mb-4">Get In Touch</h3>
                <div class="d-flex mb-3">
                    <i class="bi bi-geo-alt-fill text-primary me-3 fs-4"></i>
                    <div>
                        <h5>Our Office</h5>
                        <p class="mb-0">123 Job Street, City, Country</p>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <i class="bi bi-envelope-fill text-primary me-3 fs-4"></i>
                    <div>
                        <h5>Email Us</h5>
                        <p class="mb-0">info@jobportal.com</p>
                    </div>
                </div>
                <div class="d-flex">
                    <i class="bi bi-telephone-fill text-primary me-3 fs-4"></i>
                    <div>
                        <h5>Call Us</h5>
                        <p class="mb-0">+1 234 567 8900</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-lg-8">
            <?php if ($success_message): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="contactForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" 
                                value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                            <label for="name">Your Name</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Your Email"
                                value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                            <label for="email">Your Email</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject"
                                value="<?php echo isset($subject) ? htmlspecialchars($subject) : ''; ?>" required>
                            <label for="subject">Subject</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <textarea class="form-control" id="message" name="message" placeholder="Leave a message here" 
                                style="height: 150px" required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                            <label for="message">Message</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary py-3 px-5" type="submit">Send Message</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    let isValid = true;
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const subject = document.getElementById('subject').value.trim();
    const message = document.getElementById('message').value.trim();

    if (!name || !email || !subject || !message) {
        isValid = false;
        alert('Please fill in all fields');
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        isValid = false;
        alert('Please enter a valid email address');
    }

    if (!isValid) {
        e.preventDefault();
    }
});
</script>

<?php include 'includes/footer.php'; ?> 