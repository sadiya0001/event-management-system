<?php
require_once 'config.php';

// Email to check
$email = 'admin@university.edu';

try {
    $stmt = $conn->prepare("SELECT user_id, name, email, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "User found:\n";
        echo "Name: " . $user['name'] . "\n";
        echo "Role: " . $user['role'] . "\n";
        echo "Password hash exists: " . (!empty($user['password']) ? 'Yes' : 'No') . "\n";
    } else {
        echo "User not found in database\n";
        
        // Let's create the admin user if it doesn't exist
        $name = "Admin User";
        $password = "admin123";
        $role = "admin";
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$name, $email, $hashedPassword, $role]);
        
        if ($result) {
            echo "\nAdmin user created successfully!\n";
            echo "You can now log in with:\n";
            echo "Email: admin@university.edu\n";
            echo "Password: admin123\n";
        }
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>