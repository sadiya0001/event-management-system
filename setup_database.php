<?php
require_once 'config.php';

try {
    // Create users table if it doesn't exist
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        student_id VARCHAR(20) UNIQUE,
        contact VARCHAR(15),
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Check if admin user exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute(['admin@university.edu']);
    $admin = $stmt->fetch();

    if (!$admin) {
        // Create admin user if it doesn't exist
        $password = 'admin123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Admin User', 'admin@university.edu', $hashedPassword, 'admin']);
        echo "Admin user created successfully!\n";
        echo "Email: admin@university.edu\n";
        echo "Password: admin123\n";
    } else {
        // Reset admin password if user exists
        $password = 'admin123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, 'admin@university.edu']);
        echo "Admin password reset successfully!\n";
        echo "Email: admin@university.edu\n";
        echo "Password: admin123\n";
    }

    // Create events table if it doesn't exist
    $conn->exec("CREATE TABLE IF NOT EXISTS events (
        event_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        description TEXT,
        date DATE NOT NULL,
        time TIME NOT NULL,
        venue VARCHAR(200) NOT NULL,
        capacity INT NOT NULL,
        category ENUM('workshop', 'hackathon', 'seminar') NOT NULL,
        organizer VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create registrations table if it doesn't exist
    $conn->exec("CREATE TABLE IF NOT EXISTS registrations (
        reg_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        event_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        student_id VARCHAR(20) NOT NULL,
        email VARCHAR(100) NOT NULL,
        contact VARCHAR(15) NOT NULL,
        registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
        FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE
    )");

    echo "Database setup completed successfully!\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>