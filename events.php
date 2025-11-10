<?php
// events.php - Display all events
require_once 'config.php';

// Get search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Build query
$sql = "SELECT e.*, 
        (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.event_id) as registered_count
        FROM events e WHERE 1=1";

$params = [];

if (!empty($search)) {
    $sql .= " AND (e.title LIKE ? OR e.description LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if ($category != 'all') {
    $sql .= " AND e.category = ?";
    $params[] = $category;
}

$sql .= " ORDER BY e.date ASC, e.time ASC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $events = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Event Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4 mb-5">
        <div class="row mb-4">
            <div class="col">
                <h2 class="mb-4">Available Events</h2>
                
                <!-- Search and Filter Form -->
                <form method="GET" action="" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Search events..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="category">
                            <option value="all" <?php echo $category == 'all' ? 'selected' : ''; ?>>All Categories</option>
                            <option value="workshop" <?php echo $category == 'workshop' ? 'selected' : ''; ?>>Workshop</option>
                            <option value="hackathon" <?php echo $category == 'hackathon' ? 'selected' : ''; ?>>Hackathon</option>
                            <option value="seminar" <?php echo $category == 'seminar' ? 'selected' : ''; ?>>Seminar</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="row g-4">
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $event): ?>
                    <?php
                    $spots_left = $event['capacity'] - $event['registered_count'];
                    $is_full = $spots_left <= 0;
                    $event_date = new DateTime($event['date']);
                    $is_past = $event_date < new DateTime();
                    ?>
                    <div class="col-md-4">
                        <div class="card h-100 event-card shadow-sm">
                            <div class="card-img-top event-image bg-gradient-primary">
                                <i class="fas fa-calendar-alt fa-4x text-white"></i>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-primary text-capitalize"><?php echo htmlspecialchars($event['category']); ?></span>
                                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="edit_event.php?id=<?php echo $event['event_id']; ?>">
                                                    <i class="fas fa-edit me-2"></i>Edit
                                                </a></li>
                                                <li><a class="dropdown-item text-danger" href="delete_event.php?id=<?php echo $event['event_id']; ?>" 
                                                       onclick="return confirm('Are you sure you want to delete this event?')">
                                                    <i class="fas fa-trash me-2"></i>Delete
                                                </a></li>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                                <p class="card-text text-muted small"><?php echo substr(htmlspecialchars($event['description']), 0, 100) . '...'; ?></p>
                                
                                <div class="event-details mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-calendar text-primary me-2"></i>
                                        <small><?php echo $event_date->format('M d, Y'); ?> at <?php echo date('g:i A', strtotime($event['time'])); ?></small>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <small><?php echo htmlspecialchars($event['venue']); ?></small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users text-primary me-2"></i>
                                        <small><?php echo $event['registered_count']; ?> / <?php echo $event['capacity']; ?> registered</small>
                                    </div>
                                </div>
                                
                                <?php if ($is_past): ?>
                                    <button class="btn btn-secondary w-100" disabled>Event Ended</button>
                                <?php elseif ($is_full): ?>
                                    <button class="btn btn-danger w-100" disabled>Event Full</button>
                                <?php else: ?>
                                    <a href="event_details.php?id=<?php echo $event['event_id']; ?>" 
                                       class="btn btn-primary w-100">
                                        <i class="fas fa-ticket-alt me-2"></i>Register Now
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                                    <a href="view_registrations.php?event_id=<?php echo $event['event_id']; ?>" 
                                       class="btn btn-outline-secondary w-100 mt-2">
                                        <i class="fas fa-list me-2"></i>View Registrations (<?php echo $event['registered_count']; ?>)
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>No events found matching your criteria.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
