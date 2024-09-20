-- Erstellen der Datenbank
CREATE DATABASE IF NOT EXISTS holidayhome;
USE holidayhome;

-- Erstellen der Tabellen
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user'
);

CREATE TABLE IF NOT EXISTS houses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    price_per_day DECIMAL(10, 2) NOT NULL,
    name VARCHAR(255) NOT NULL,
    country VARCHAR(255) NOT NULL,
    street VARCHAR(255) NOT NULL,
    house_number VARCHAR(20),
    postal_code VARCHAR(20),
    landlord INT,
    FOREIGN KEY (landlord) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tag_name VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS house_tags (
    house_id INT,
    tag_id INT,
    PRIMARY KEY (house_id, tag_id),
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS house_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    house_id INT,
    image_url VARCHAR(255) NOT NULL,
    image_type ENUM('indoor', 'outdoor', 'main') NOT NULL, 
    is_main_image BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    activity_name VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS house_activities (
    house_id INT,
    activity_id INT,
    PRIMARY KEY (house_id, activity_id),
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS facilities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    facility_name VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS house_facilities (
    house_id INT,
    facility_id INT,
    PRIMARY KEY (house_id, facility_id),
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    house_id INT,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    total_price DECIMAL(10, 2),
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE
);

-- Tabelle für die Verknüpfung von Buchungen und Einrichtungen
CREATE TABLE IF NOT EXISTS booking_facilities (
    booking_id INT,
    facility_id INT,
    PRIMARY KEY (booking_id, facility_id),
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE
);

-- Admin-User einfügen
INSERT INTO users (username, email, password_hash, role)
VALUES ('admin', 'admin@example.com', 'hashedPassword', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- Normaler User einfügen
INSERT INTO users (username, email, password_hash, role)
VALUES ('normaluser', 'user@example.com', 'hashedPassword', 'user')
ON DUPLICATE KEY UPDATE username=username;

-- Einfügen von Facilities
INSERT INTO facilities (facility_name) VALUES 
('WiFi'), ('Swimming Pool'), ('Parking'), ('Air Conditioning'), ('Heating');

-- Einfügen von Activities
INSERT INTO activities (activity_name) VALUES 
('Hiking'), ('Cycling'), ('Fishing'), ('Skiing'), ('Horseback Riding');

-- Einfügen von Tags
INSERT INTO tags (tag_name) VALUES 
('vacation'), ('beach'), ('romantic'), ('adventure'), ('family'),
('luxury'), ('nature'), ('historical'), ('spa'), ('wildlife'),
('culture'), ('city'), ('mountains'), ('sunset'), ('relaxation'),
('romance'), ('scenic'), ('hiking'), ('golf'), ('ocean'), ('winter');

-- Beispiel-Haus einfügen
INSERT INTO houses (price_per_day, name, country, street, house_number, postal_code, landlord)
VALUES (20.50, 'Mein Haus', 'Deutschland', 'Strohbach', '12', '77723', 1);

-- Beispiel-Bilder einfügen
INSERT INTO house_images (house_id, image_url, image_type, is_main_image) VALUES
(1, '/uploads/houses/1thumb_18ae.png', 'main', 1),
(1, '/uploads/houses/9thumb_7ff5.png', 'indoor', 0),
(1, '/uploads/houses/10thumb_4cce.png', 'indoor', 0),
(1, '/uploads/houses/11thumb_75d3.png', 'indoor', 0),
(1, '/uploads/houses/12thumb_bc08.png', 'indoor', 0),
(1, '/uploads/houses/13thumb_198c.png', 'indoor', 0),
(1, '/uploads/houses/2thumb_1243.png', 'outdoor', 0),
(1, '/uploads/houses/3thumb_46a0.png', 'outdoor', 0),
(1, '/uploads/houses/6thumb_96d5.png', 'outdoor', 0),
(1, '/uploads/houses/7thumb_d95d.png', 'outdoor', 0),
(1, '/uploads/houses/8thumb_be22.png', 'outdoor', 0);

-- Verknüpfung von Facilities mit dem Beispiel-Haus
INSERT INTO house_facilities (house_id, facility_id) VALUES 
(1, 1),  -- WiFi
(1, 2),  -- Swimming Pool
(1, 3);  -- Parking

-- Verknüpfung von Activities mit dem Beispiel-Haus
INSERT INTO house_activities (house_id, activity_id) VALUES 
(1, 1),  -- Hiking
(1, 2),  -- Cycling
(1, 3);  -- Fishing
