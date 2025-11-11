-- db_structure.sql - Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS event_management;
USE event_management;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    student_id VARCHAR(50) UNIQUE NOT NULL,
    contact VARCHAR(15) NOT NULL,
    role ENUM('admin', 'student') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    venue VARCHAR(200) NOT NULL,
    organizer VARCHAR(100) NOT NULL,
    category ENUM('workshop', 'hackathon', 'seminar') NOT NULL,
    capacity INT NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Registrations table
CREATE TABLE IF NOT EXISTS registrations (
    reg_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    event_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    student_id VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    contact VARCHAR(15) NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (event_id, email)
);

-- Insert default admin user
INSERT INTO users (name, email, password, student_id, contact, role) 
VALUES ('Admin User', 'admin@university.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADM001', '0771234567', 'admin');
-- Default password: admin123

-- Insert sample events
INSERT INTO events (title, description, date, time, venue, organizer, category, capacity, created_by) VALUES
('Web Development Workshop', 'Learn modern web development with HTML, CSS, and JavaScript', '2025-11-15', '14:00:00', 'Computer Lab A', 'CS Department', 'workshop', 30, 1),
('Annual Hackathon 2025', '24-hour coding challenge with exciting prizes', '2025-11-20', '09:00:00', 'Main Auditorium', 'Tech Club', 'hackathon', 50, 1),
('AI and Machine Learning Seminar', 'Expert talks on the future of AI and career opportunities', '2025-11-25', '15:30:00', 'Lecture Hall B', 'Research Department', 'seminar', 100, 1);
