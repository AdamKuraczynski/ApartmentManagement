CREATE DATABASE IF NOT EXISTS apartment_management;

USE apartment_management;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('administrator', 'renter') NOT NULL
);

INSERT INTO users (username, password, user_type) VALUES
('admin', 'admin_password', 'administrator'),
('john_doe', 'johns_password', 'renter'),
('jane_doe', 'janes_password', 'renter');