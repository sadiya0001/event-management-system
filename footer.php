<?php
// includes/footer.php - Footer Component
?>
<footer class="bg-dark text-white mt-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5 class="footer-heading"><i class="fas fa-calendar-alt me-2 footer-icon"></i>Event Manager</h5>
                <p class="footer-text">
                    Your complete solution for managing campus events, workshops, seminars, and activities. Join us in creating memorable experiences!
                </p> 
            </div>
            <div class="col-md-4 mb-3">
                <h5 class="footer-heading">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="footer-link"><i class="fas fa-home me-2 footer-icon"></i>Home</a></li>
                    <li><a href="events.php" class="footer-link"><i class="fas fa-calendar me-2 footer-icon"></i>Browse Events</a></li>
                    <li><a href="create_event.php" class="footer-link"><i class="fas fa-plus-circle me-2 footer-icon"></i>Create Event</a></li>
                    <li><a href="manage_events.php" class="footer-link"><i class="fas fa-tasks me-2 footer-icon"></i>Manage Events</a></li>
                    <li><a href="dashboard.php" class="footer-link"><i class="fas fa-tachometer-alt me-2 footer-icon"></i>Dashboard</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h5 class="footer-heading">Contact</h5>
                <p class="footer-text mb-1">
                    <i class="fas fa-envelope me-2 footer-icon"></i>eventmanager@itum.edu
                </p>
                <p class="footer-text mb-1">
                    <i class="fas fa-phone me-2 footer-icon"></i>+94 11 234 5678
                </p>
                <p class="footer-text mb-1">
                    <i class="fas fa-clock me-2 footer-icon"></i>Mon-Fri: 9:00 AM - 5:00 PM
                </p>
                <p class="footer-text">
                    <i class="fas fa-map-marker-alt me-2 footer-icon"></i>Main Campus Building, INSTITUTE OF TECHNOLOGY
University of Moratuwa
                </p>
            </div>
        </div>
        <hr class="bg-secondary">
        <div class="text-center text-muted">
            <p class="mb-0">
                &copy; <?php echo date('Y'); ?> Event Management System. 
                Developed for Academic Purposes.
            </p>
        </div>
    </div>
</footer>
