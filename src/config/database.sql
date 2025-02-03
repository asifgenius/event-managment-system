CREATE DATABASE IF NOT EXISTS event_management_system;

USE event_management_system;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,       
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration INT NOT NULL,
    max_capacity INT NOT NULL,
    current_capacity INT DEFAULT 0,
    created_by INT NOT NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS attendees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,  
    user_id INT NOT NULL,   
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,  
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,   
    UNIQUE(event_id, user_id) 
);

INSERT INTO users (name, email, phone, password, role) 
VALUES 
('asifislam', 'admin@gmail.com', '1234567890', 'hashedPassword12', 'admin'),
('asifuser', 'user@gmail.com', '12330987654', 'hashedpassworduser12', 'user');

-- Insert Events Using Subquery Instead of Variable
INSERT INTO events (title, description, start_date, end_date, location, price, duration, max_capacity, created_by) 
VALUES
('Technology Conference 2025', 'A technology conference for developers', NOW(), DATE_ADD(NOW(), INTERVAL 1 DAY), 'Dhaka', '300', 2, 200, 
(SELECT id FROM users WHERE email = 'admin@gmail.com' LIMIT 1)),

('Web Development Bootcamp', 'Bootcamp to learn web development', NOW(), DATE_ADD(NOW(), INTERVAL 5 DAY), 'Dinajpur', '500', 3, 300, 
(SELECT id FROM users WHERE email = 'admin@gmail.com' LIMIT 1));