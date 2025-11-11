<?php
// create_event.php - Create New Event (Admin Only)
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $venue = trim($_POST['venue']);
    $organizer = trim($_POST['organizer']);
    $category = $_POST['category'];
    $capacity = (int)$_POST['capacity'];
    
    // Validation
    if (empty($title)) {
        $errors[] = 'Event title is required';
    }
    
    if (empty($description)) {
        $errors[] = 'Event description is required';
    }
    
    if (empty($date)) {
        $errors[] = 'Event date is required';
    } elseif (strtotime($date) < strtotime(date('Y-m-d'))) {
        $errors[] = 'Event date cannot be in the past';
    }
    
    if (empty($time)) {
        $errors[] = 'Event time is required';
    }
    
    if (empty($venue)) {
        $errors[] = 'Event venue is required';
    }
    
    if (empty($organizer)) {
        $errors[] = 'Event organizer is required';
    }
    
    if ($capacity < 1) {
        $errors[] = 'Capacity must be at least 1';
    }
    
    // Insert event if no errors
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO events (title, description, date, time, venue, organizer, category, capacity, created_by) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $date, $time, $venue, $organizer, $category, $capacity, $_SESSION['user_id']]);
            
            $success = 'Event created successfully!';
            // Clear form
            $_POST = array();
        } catch(PDOException $e) {
            $errors[] = 'Failed to create event. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Event Management</title>
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
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Create New Event</h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
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
                                <a href="events.php" class="alert-link ms-2">View all events</a>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" id="createEventForm">
                            <div class="mb-3">
                                <label for="title" class="form-label">Event Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       placeholder="e.g., Web Development Workshop" required
                                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Describe the event in detail..." required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                            </div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="date" class="form-label">Event Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" required
                                           min="<?php echo date('Y-m-d'); ?>"
                                           value="<?php echo isset($_POST['date']) ? $_POST['date'] : ''; ?>">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="time" class="form-label">Event Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="time" name="time" required
                                           value="<?php echo isset($_POST['time']) ? $_POST['time'] : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="venue" class="form-label">Venue <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="venue" name="venue" 
                                       placeholder="e.g., Computer Lab A" required
                                       value="<?php echo isset($_POST['venue']) ? htmlspecialchars($_POST['venue']) : ''; ?>">
                            </div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="organizer" class="form-label">Organizer <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="organizer" name="organizer" 
                                           placeholder="e.g., CS Department" required
                                           value="<?php echo isset($_POST['organizer']) ? htmlspecialchars($_POST['organizer']) : ''; ?>">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="workshop" <?php echo (isset($_POST['category']) && $_POST['category'] == 'workshop') ? 'selected' : ''; ?>>Workshop</option>
                                        <option value="hackathon" <?php echo (isset($_POST['category']) && $_POST['category'] == 'hackathon') ? 'selected' : ''; ?>>Hackathon</option>
                                        <option value="seminar" <?php echo (isset($_POST['category']) && $_POST['category'] == 'seminar') ? 'selected' : ''; ?>>Seminar</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="capacity" class="form-label">Capacity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="capacity" name="capacity" 
                                       placeholder="Maximum number of participants" required min="1"
                                       value="<?php echo isset($_POST['capacity']) ? $_POST['capacity'] : ''; ?>">
                                <small class="text-muted">Enter the maximum number of participants allowed</small>
                            </div>
                            
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="fas fa-save me-2"></i>Create Event
                                </button>
                                <a href="dashboard.php" class="btn btn-secondary flex-fill">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Help Card -->
                <div class="card mt-4 shadow">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Tips for Creating Events</h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Make sure the title is clear and descriptive</li>
                            <li>Provide detailed information in the description</li>
                            <li>Set realistic capacity based on venue size</li>
                            <li>Double-check the date and time</li>
                            <li>Include the exact venue location</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.getElementById('createEventForm').addEventListener('submit', function(e) {
            const capacity = parseInt(document.getElementById('capacity').value);
            const date = new Date(document.getElementById('date').value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (capacity < 1) {
                e.preventDefault();
                alert('Capacity must be at least 1');
                return false;
            }
            
            if (date < today) {
                e.preventDefault();
                alert('Event date cannot be in the past');
                return false;
            }
        });
    </script>
</body>
</html>
