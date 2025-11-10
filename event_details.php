<?php
// event_details.php - Event Details and Registration
require_once 'config.php';

$errors = [];
$success = '';

// Get event ID
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch event details
try {
    $stmt = $conn->prepare("SELECT e.*, 
            (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.event_id) as registered_count
            FROM events e WHERE e.event_id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event) {
        header('Location: events.php');
        exit;
    }
} catch(PDOException $e) {
    header('Location: events.php');
    exit;
}

// Check if event is full
$spots_left = $event['capacity'] - $event['registered_count'];
$is_full = $spots_left <= 0;

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$is_full) {
    $name = trim($_POST['name']);
    $student_id = trim($_POST['student_id']);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $contact = trim($_POST['contact']);
    
    // Validation
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($student_id) || strlen($student_id) < 4) {
        $errors[] = 'Student ID must be at least 4 characters';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    
    if (empty($contact) || !preg_match('/^[0-9]{10}$/', $contact)) {
        $errors[] = 'Valid 10-digit contact number is required';
    }
    
    // Check if already registered
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("SELECT reg_id FROM registrations WHERE event_id = ? AND email = ?");
            $stmt->execute([$event_id, $email]);
            if ($stmt->fetch()) {
                $errors[] = 'You have already registered for this event';
            }
        } catch(PDOException $e) {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
    
    // Insert registration if no errors
    if (empty($errors)) {
        try {
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $stmt = $conn->prepare("INSERT INTO registrations (user_id, event_id, name, student_id, email, contact) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $event_id, $name, $student_id, $email, $contact]);
            
            $success = 'Registration successful! You will receive a confirmation email shortly.';
        } catch(PDOException $e) {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> - Event Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4 mb-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow">
                    <div class="card-img-top event-image-large bg-gradient-primary">
                        <i class="fas fa-calendar-alt fa-5x text-white"></i>
                    </div>
                    <div class="card-body p-4">
                        <h2 class="card-title mb-3"><?php echo htmlspecialchars($event['title']); ?></h2>
                        
                        <div class="event-info mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <i class="fas fa-calendar text-primary me-2"></i>
                                        <strong>Date:</strong> 
                                        <?php echo date('F d, Y', strtotime($event['date'])); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <i class="fas fa-clock text-primary me-2"></i>
                                        <strong>Time:</strong> 
                                        <?php echo date('g:i A', strtotime($event['time'])); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <strong>Venue:</strong> 
                                        <?php echo htmlspecialchars($event['venue']); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <i class="fas fa-users text-primary me-2"></i>
                                        <strong>Capacity:</strong> 
                                        <?php echo $event['registered_count']; ?> / <?php echo $event['capacity']; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <i class="fas fa-user-tie text-primary me-2"></i>
                                        <strong>Organizer:</strong> 
                                        <?php echo htmlspecialchars($event['organizer']); ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <i class="fas fa-tag text-primary me-2"></i>
                                        <strong>Category:</strong> 
                                        <span class="badge bg-primary text-capitalize"><?php echo htmlspecialchars($event['category']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="event-description mb-4">
                            <h5>Description</h5>
                            <p class="text-muted"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                        </div>
                        
                        <?php if ($spots_left > 0 && $spots_left <= 10): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Only <?php echo $spots_left; ?> spots left!
                            </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <?php if (!$is_full): ?>
                            <h4 class="mb-4">Register for this Event</h4>
                            
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($success): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php else: ?>
                                <form method="POST" action="" id="registrationForm">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   placeholder="John Doe" required
                                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="student_id" name="student_id" 
                                                   placeholder="STU2024001" required minlength="4"
                                                   value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : ''; ?>">
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   placeholder="your.email@university.edu" required
                                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="contact" class="form-label">Contact Number <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" id="contact" name="contact" 
                                                   placeholder="0771234567" required pattern="[0-9]{10}"
                                                   value="<?php echo isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : ''; ?>">
                                            <small class="text-muted">10 digits only</small>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-3 mt-4">
                                        <button type="submit" class="btn btn-primary flex-fill">
                                            <i class="fas fa-check me-2"></i>Submit Registration
                                        </button>
                                        <a href="events.php" class="btn btn-secondary flex-fill">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                    </div>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                This event is full. Registration is closed.
                            </div>
                            <a href="events.php" class="btn btn-secondary w-100">Back to Events</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/validation.js"></script>
</body>
</html>
