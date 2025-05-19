-- Create the database
CREATE DATABASE IF NOT EXISTS easyev_charging;
USE easyev_charging;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create charging_locations table
CREATE TABLE IF NOT EXISTS charging_locations (
    location_id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255) NOT NULL,
    num_stations INT NOT NULL,
    cost_per_hour DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create charging_sessions table
CREATE TABLE IF NOT EXISTS charging_sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    location_id INT NOT NULL,
    check_in_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    check_out_time TIMESTAMP NULL DEFAULT NULL,
    total_cost DECIMAL(10, 2) DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (location_id) REFERENCES charging_locations(location_id)
);

-- Insert admin user (password: admin123)
INSERT INTO users (name, email, phone, password, user_type) 
VALUES ('Admin', 'admin@easyev.com', '1234567890', '$2y$10$8y1f0DjgwXwR1g8UhQMjYuHp.jQU9ZQgYB0YwJRrX7r6Nrm7kPjnq', 'admin');

-- Insert sample charging locations
INSERT INTO charging_locations (description, num_stations, cost_per_hour) 
VALUES 
('City Center EV Station', 5, 10.50),
('Westfield Mall Charging Hub', 8, 12.75),
('North Beach Charging Station', 3, 9.99),
('Downtown Parking Garage', 10, 8.50),
('Airport Terminal Charging', 6, 15.00); 