<?php
// index.php - Home Page
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Event Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Student Event Management</h1>
                    <p class="lead mb-4">Organize, Discover, and Participate in Campus Events</p>
                    <div class="d-flex gap-3">
                        <a href="events.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-calendar-alt me-2"></i>View Events
                        </a>
                        <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="login.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="images.jpg" alt="Events" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Our Platform?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-calendar-check fa-3x text-primary"></i>
                        </div>
                        <h4>Browse Events</h4>
                        <p class="text-muted">Discover workshops, seminars, and hackathons happening on campus</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-user-check fa-3x text-success"></i>
                        </div>
                        <h4>Easy Registration</h4>
                        <p class="text-muted">Register for events with a simple and intuitive form</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-chart-line fa-3x text-warning"></i>
                        </div>
                        <h4>Track Participation</h4>
                        <p class="text-muted">View and manage your event registrations easily</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <?php
                // Get statistics
                $stmt = $conn->query("SELECT COUNT(*) FROM events");
                $total_events = $stmt->fetchColumn();
                
                $stmt = $conn->query("SELECT COUNT(*) FROM registrations");
                $total_registrations = $stmt->fetchColumn();
                
                $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'student'");
                $total_students = $stmt->fetchColumn();
                ?>
                <div class="col-md-4">
                    <div class="stat-card">
                        <h2 class="display-4 text-primary"><?php echo $total_events; ?></h2>
                        <p class="text-muted">Total Events</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <h2 class="display-4 text-success"><?php echo $total_registrations; ?></h2>
                        <p class="text-muted">Registrations</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <h2 class="display-4 text-warning"><?php echo $total_students; ?></h2>
                        <p class="text-muted">Students</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
