-- Property Management System Database Schema
-- Run this in your MySQL/MariaDB database

CREATE DATABASE IF NOT EXISTS pms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pms_db;

-- =====================
-- USERS TABLE
-- =====================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,  -- bcrypt hashed
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('Admin', 'Staff') NOT NULL DEFAULT 'Staff',
    status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================
-- PROPERTIES TABLE
-- =====================
CREATE TABLE IF NOT EXISTS properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    location VARCHAR(255) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,  -- used in guest registration URL
    status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================
-- GUESTS TABLE
-- =====================
CREATE TABLE IF NOT EXISTS guests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    id_booking VARCHAR(50) NOT NULL UNIQUE,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    date_of_birth DATE,
    age INT,
    nationality VARCHAR(100),
    occupation VARCHAR(100),
    address TEXT,
    room_number VARCHAR(20),
    check_in DATE,
    check_out DATE,
    previous_stay_location VARCHAR(255),
    next_stay_location VARCHAR(255),
    has_japan_address ENUM('yes', 'no') NOT NULL DEFAULT 'no',
    passport_photo VARCHAR(255),  -- file path for uploaded passport
    submission_date DATE NOT NULL DEFAULT (CURRENT_DATE),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- =====================
-- NOTIFICATIONS TABLE
-- =====================
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    property_name VARCHAR(150) NOT NULL,
    guest_name VARCHAR(150) NOT NULL,
    guest_email VARCHAR(100),
    id_booking VARCHAR(50),
    message TEXT NOT NULL,
    type VARCHAR(50) DEFAULT 'guest_submission',
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- =====================
-- SEED DATA
-- =====================

-- Default users (passwords: admin123, staff123)
INSERT INTO users (username, password, email, role, status, created_at) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@pms.com', 'Admin', 'Active', '2026-01-15 00:00:00'),
('john_manager', '$2y$10$TKh8H1.PfetFdQBGmkAH3e28IQ7P1VO9gNDWXmpAJ5XFHSEfVFUOe', 'john@pms.com', 'Staff', 'Active', '2026-02-10 00:00:00'),
('sarah_desk', '$2y$10$TKh8H1.PfetFdQBGmkAH3e28IQ7P1VO9gNDWXmpAJ5XFHSEfVFUOe', 'sarah@pms.com', 'Staff', 'Active', '2026-03-05 00:00:00'),
('mike_support', '$2y$10$TKh8H1.PfetFdQBGmkAH3e28IQ7P1VO9gNDWXmpAJ5XFHSEfVFUOe', 'mike@pms.com', 'Staff', 'Inactive', '2026-01-20 00:00:00');
-- Note: admin password = 'admin123', staff passwords = 'password' (change in production!)
-- To generate proper hashes: password_hash('admin123', PASSWORD_BCRYPT)

-- Default properties
INSERT INTO properties (name, location, slug, status) VALUES
('Sunset Villa', 'Los Angeles, CA', 'sunset-villa-1', 'Active'),
('Ocean View Hotel', 'Miami, FL', 'ocean-view-2', 'Active'),
('Mountain Resort', 'Denver, CO', 'mountain-resort-3', 'Active'),
('City Center Inn', 'New York, NY', 'city-center-4', 'Active'),
('Lakeside Cottage', 'Seattle, WA', 'lakeside-cottage-5', 'Inactive'),
('Desert Oasis', 'Phoenix, AZ', 'desert-oasis-6', 'Active');

-- Sample guests
INSERT INTO guests (property_id, name, id_booking, phone, email, date_of_birth, age, nationality, occupation, address, room_number, check_in, check_out, previous_stay_location, next_stay_location, has_japan_address, submission_date) VALUES
(1, 'John Smith', 'BK001234', '+1 234-567-8901', 'john@email.com', '1985-03-15', 41, 'USA', 'Software Engineer', '123 Main St, Los Angeles, CA 90001', '101', '2026-05-04', '2026-05-07', 'San Francisco Hotel', 'Las Vegas Resort', 'no', '2026-05-04'),
(2, 'Sarah Johnson', 'BK002001', '+1 234-567-8902', 'sarah@email.com', '1993-01-12', 33, 'USA', 'Photographer', '111 Bay St, Miami, FL 33139', '201', '2026-05-04', '2026-05-10', 'Orlando Theme Park Hotel', 'Key West Resort', 'no', '2026-05-04'),
(3, 'Michael Chen', 'BK003001', '+1 234-567-8903', 'michael@email.com', '1990-03-30', 36, 'USA', 'Data Scientist', '333 Peak Ave, Denver, CO 80202', '301', '2026-05-03', '2026-05-08', 'Boulder Mountain Inn', 'Aspen Lodge', 'no', '2026-05-03'),
(1, 'Emma Davis', 'BK001235', '+1 234-567-8904', 'emma@email.com', '1990-07-22', 35, 'USA', 'Marketing Manager', '456 Oak Ave, Los Angeles, CA 90002', '102', '2026-05-03', '2026-05-06', 'Seattle Downtown Inn', 'Portland Hotel', 'no', '2026-05-03'),
(1, 'Yuki Tanaka', 'BK001237', '+81 90-1234-5678', 'yuki@email.com', '1982-05-10', 44, 'Japan', 'Business Consultant', '1-2-3 Shibuya, Tokyo 150-0002, Japan', '104', '2026-05-01', '2026-05-05', 'Osaka Business Hotel', 'Kyoto Ryokan', 'yes', '2026-05-03'),
(1, 'Lisa Anderson', 'BK001236', '+1 234-567-8906', 'lisa@email.com', '1988-11-30', 37, 'Canada', 'Graphic Designer', '789 Pine Rd, Vancouver, BC V6B 1A1', '103', '2026-05-02', '2026-05-08', 'San Diego Beach Resort', 'Phoenix Hotel', 'no', '2026-05-02'),
(2, 'David Garcia', 'BK001239', '+34 91 123 4567', 'david@email.com', '1987-12-05', 38, 'Spain', 'Architect', '987 Cedar Ln, Madrid, Spain 28001', '106', '2026-04-29', '2026-05-03', 'Barcelona Beach Hotel', 'Miami Resort', 'no', '2026-05-02'),
(3, 'Jennifer Lee', 'BK001240', '+1 234-567-8910', 'jennifer@email.com', '1995-04-27', 31, 'USA', 'Teacher', '147 Birch Ct, Los Angeles, CA 90003', '107', '2026-04-28', '2026-05-02', 'Denver Mountain Lodge', 'Austin Hotel', 'no', '2026-05-01'),
(6, 'Robert Martinez', 'BK001241', '+52 55 1234 5678', 'robert@email.com', '1989-08-14', 36, 'Mexico', 'Chef', '258 Willow Way, Mexico City, Mexico 01000', '108', '2026-04-27', '2026-05-01', 'Cancun Beach Resort', 'San Antonio Hotel', 'no', '2026-05-01'),
(4, 'Kenji Yamamoto', 'BK001242', '+81 80-9876-5432', 'kenji@email.com', '1991-06-08', 34, 'Japan', 'Software Developer', '5-6-7 Shinjuku, Tokyo 160-0022, Japan', '109', '2026-04-26', '2026-04-30', 'Nagoya City Hotel', 'Fukuoka Business Inn', 'yes', '2026-04-30');

-- Sample notifications
INSERT INTO notifications (property_id, property_name, guest_name, guest_email, id_booking, message, is_read) VALUES
(1, 'Sunset Villa', 'John Smith', 'john@email.com', 'BK001234', 'New guest registration from John Smith', 0),
(2, 'Ocean View Hotel', 'Sarah Johnson', 'sarah@email.com', 'BK002001', 'New guest registration from Sarah Johnson', 0),
(3, 'Mountain Resort', 'Michael Chen', 'michael@email.com', 'BK003001', 'New guest registration from Michael Chen', 1);

-- =====================
-- USEFUL INDEXES
-- =====================
CREATE INDEX idx_guests_property ON guests(property_id);
CREATE INDEX idx_guests_submission_date ON guests(submission_date);
CREATE INDEX idx_notifications_property ON notifications(property_id);
CREATE INDEX idx_notifications_read ON notifications(is_read);
