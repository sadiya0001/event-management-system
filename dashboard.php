<?php
// dashboard.php - User Dashboard
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Get statistics
if ($user_role == 'admin') {
    // Admin statistics
    $stmt = $conn->query("SELECT COUNT(*) FROM events");
    $total_events = $stmt->fetchColumn();
    
    $stmt = $conn->query("SELECT COUNT(*) FROM registrations");
    $total_registrations = $stmt->fetchColumn();
    
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'student'");
    $total_students = $stmt->fetchColumn();
    
    // Recent events with registration count
    $stmt = $conn->query("SELECT e.*, 
            (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.event_id) as registered_count
            FROM events e ORDER BY e.created_at DESC LIMIT 5");
    $recent_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} else {
    // Student statistics
    $stmt = $conn->prepare("SELECT COUNT(*) FROM registrations WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $my_registrations_count = $stmt->fetchColumn();
    
    $stmt = $conn->query("SELECT COUNT(*) FROM events WHERE date >= CURDATE()");
    $upcoming_events_count = $stmt->fetchColumn();
    
    // My registrations
    $stmt = $conn->prepare("SELECT e.*, r.registered_at 
            FROM events e 
            INNER JOIN registrations r ON e.event_id = r.event_id 
            WHERE r.user_id = ? 
            ORDER BY e.date ASC");
    $stmt->execute([$user_id]);
    $my_registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Event Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4 mb-5">
        <h2 class="mb-4">Dashboard</h2>
        
        <!-- Welcome Message -->
        <div class="alert alert-info">
            <i class="fas fa-user-circle me-2"></i>
            Welcome back, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!
            <?php if ($user_role == 'admin'): ?>
                <span class="badge bg-danger ms-2">Admin</span>
            <?php endif; ?>
        </div>

        <?php if ($user_role == 'admin'): ?>
            <!-- Admin Dashboard -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card stats-card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle mb-2">Total Events</h6>
                                    <h2 class="card-title mb-0"><?php echo $total_events; ?></h2>
                                </div>
                                <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card stats-card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle mb-2">Total Registrations</h6>
                                    <h2 class="card-title mb-0"><?php echo $total_registrations; ?></h2>
                                </div>
                                <i class="fas fa-users fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card stats-card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle mb-2">Total Students</h6>
                                    <h2 class="card-title mb-0"><?php echo $total_students; ?></h2>
                                </div>
                                <i class="fas fa-user-graduate fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="create_event.php" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-2"></i>Create Event
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="events.php" class="btn btn-info w-100">
                                <i class="fas fa-list me-2"></i>View All Events
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="manage_events.php" class="btn btn-warning w-100">
                                <i class="fas fa-cog me-2"></i>Manage Events
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="all_registrations.php" class="btn btn-success w-100">
                                <i class="fas fa-clipboard-list me-2"></i>All Registrations
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Events -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Events</h5>
                </div>
                <div class="card-body">
                    <?php if (count($recent_events) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Date</th>
                                        <th>Venue</th>
                                        <th>Registrations</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_events as $event): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($event['title']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($event['date'])); ?></td>
                                            <td><?php echo htmlspecialchars($event['venue']); ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo $event['registered_count']; ?> / <?php echo $event['capacity']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="edit_event.php?id=<?php echo $event['event_id']; ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="view_registrations.php?event_id=<?php echo $event['event_id']; ?>" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center mb-0">No events created yet.</p>
                    <?php endif; ?>
                </div>
            </div>

        <?php else: ?>
            <!-- Student Dashboard -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card stats-card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle mb-2">My Registrations</h6>
                                    <h2 class="card-title mb-0"><?php echo $my_registrations_count; ?></h2>
                                </div>
                                <i class="fas fa-ticket-alt fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card stats-card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle mb-2">Upcoming Events</h6>
                                    <h2 class="card-title mb-0"><?php echo $upcoming_events_count; ?></h2>
                                </div>
                                <i class="fas fa-calendar-check fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="events.php" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Browse Events
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="my_registrations.php" class="btn btn-info w-100">
                                <i class="fas fa-list me-2"></i>My Registrations
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Registered Events -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>My Registered Events</h5>
                </div>
                <div class="card-body">
                    <?php if (count($my_registrations) > 0): ?>
                        <div class="row g-3">
                            <?php foreach ($my_registrations as $event): ?>
                                <div class="col-md-6">
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                                            <p class="card-text text-muted small">
                                                <?php echo substr(htmlspecialchars($event['description']), 0, 80) . '...'; ?>
                                            </p>
                                            <div class="mb-2">
                                                <i class="fas fa-calendar text-primary me-2"></i>
                                                <small><?php echo date('M d, Y', strtotime($event['date'])); ?> at <?php echo date('g:i A', strtotime($event['time'])); ?></small>
                                            </div>
                                            <div class="mb-2">
                                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                                <small><?php echo htmlspecialchars($event['venue']); ?></small>
                                            </div>
                                            <div class="mb-3">
                                                <span class="badge bg-success">
                                                    Registered on <?php echo date('M d, Y', strtotime($event['registered_at'])); ?>
                                                </span>
                                            </div>
                                            <a href="event_details.php?id=<?php echo $event['event_id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <p class="text-muted">You haven't registered for any events yet.</p>
                            <a href="events.php" class="btn btn-primary">Browse Events</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
