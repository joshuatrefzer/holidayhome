-- Erstellen der Datenbank
CREATE DATABASE IF NOT EXISTS holidayhome;
USE holidayhome;

-- Erstellen der Tabellen
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255),  -- Passwort ist optional und wird nicht gehashed gespeichert
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

-- Admin-User einfügen (mit Passwortschutz)
INSERT INTO users (username, password, role)
VALUES ('admin', 'adminPassword', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- Normaler User ohne Passwortschutz einfügen
INSERT INTO users (username, role)
VALUES ('normaluser', 'user')
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
('romance'), ('scenic'), ('hiking'), ('golf'), ('ocean'), ('winter'),
('forest')
ON DUPLICATE KEY UPDATE tag_name=tag_name;

-- Beispiel-Haus 1 (Schwarzwaldhof) einfügen
INSERT INTO houses (price_per_day, name, country, street, house_number, postal_code, landlord)
VALUES (65.00, 'Schwarzwaldhof', 'Deutschland', 'Gutach', '5', '77723', 1);

-- Beispiel-Haus 2 (Schloss Mosel) einfügen
INSERT INTO houses (price_per_day, name, country, street, house_number, postal_code, landlord)
VALUES (120.00, 'Schloss Mosel', 'Deutschland', 'Mehringer Str.', '4', '72819', 1);

-- Beispiel-Haus 3 (Ostsee Paradies) einfügen
INSERT INTO houses (price_per_day, name, country, street, house_number, postal_code, landlord)
VALUES (115.00, 'Ostsee Paradies', 'Deutschland', 'Ostsee-Str.', '66', '74325', 1);

-- Verknüpfung von Facilities mit Haus 1 (Schwarzwaldhof)
INSERT INTO house_facilities (house_id, facility_id) VALUES 
(1, 1),  -- WiFi
(1, 2),  -- Swimming Pool
(1, 3);  -- Parking

-- Verknüpfung von Activities mit Haus 1 (Schwarzwaldhof)
INSERT INTO house_activities (house_id, activity_id) VALUES 
(1, 1),  -- Hiking
(1, 2),  -- Cycling
(1, 3);  -- Fishing

-- Verknüpfung von Tags mit Haus 1 (Schwarzwaldhof)
INSERT INTO house_tags (house_id, tag_id)
SELECT 1, id FROM tags WHERE tag_name IN ('nature', 'historical', 'wildlife', 'mountains', 'winter', 'forest');

-- Beispiel-Bilder für Haus 1 (Schwarzwaldhof) einfügen
INSERT INTO house_images (house_id, image_url, image_type, is_main_image) VALUES
(1, '/uploads/houses/Unknown_5520.jpeg', 'main', 1),
(1, '/uploads/houses/Unknown-5_3580.jpeg', 'indoor', 0),
(1, '/uploads/houses/Unknown-4_88b0.jpeg', 'indoor', 0),
(1, '/uploads/houses/Unknown-3_c71a.jpeg', 'indoor', 0),
(1, '/uploads/houses/Unknown-2_7a2a.jpeg', 'indoor', 0),
(1, '/uploads/houses/images-4_782e.jpeg', 'indoor', 0),
(1, '/uploads/houses/images-1_2341.jpeg', 'outdoor', 0),
(1, '/uploads/houses/images-2_d3d4.jpeg', 'outdoor', 0),
(1, '/uploads/houses/images-3_3435.jpeg', 'outdoor', 0),
(1, '/uploads/houses/images_c49c.jpeg', 'outdoor', 0),
(1, '/uploads/houses/Unknown-1_db03.jpeg', 'outdoor', 0);

-- Verknüpfung von Facilities mit Haus 2 (Schloss Mosel)
INSERT INTO house_facilities (house_id, facility_id) VALUES 
(2, 1),  -- WiFi
(2, 2),  -- Swimming Pool
(2, 3);  -- Parking

-- Verknüpfung von Activities mit Haus 2 (Schloss Mosel)
INSERT INTO house_activities (house_id, activity_id) VALUES 
(2, 1),  -- Hiking
(2, 2),  -- Cycling
(2, 3);  -- Fishing

-- Verknüpfung von Tags mit Haus 2 (Schloss Mosel)
INSERT INTO house_tags (house_id, tag_id)
SELECT 2, id FROM tags WHERE tag_name IN ('romantic', 'adventure', 'nature', 'historical', 'spa', 'wildlife', 'sunset', 'romance');

-- Beispiel-Bilder für Haus 2 (Schloss Mosel) einfügen
INSERT INTO house_images (house_id, image_url, image_type, is_main_image) VALUES
(2, '/uploads/houses/Unknown_d395.jpeg', 'main', 1),
(2, '/uploads/houses/Unknown-6_b204.jpeg', 'indoor', 0),
(2, '/uploads/houses/Unknown-9_fcca.jpeg', 'indoor', 0),
(2, '/uploads/houses/Unknown-8_c950.jpeg', 'indoor', 0),
(2, '/uploads/houses/Unknown-7_e65e.jpeg', 'indoor', 0),
(2, '/uploads/houses/Unknown-2_13fc.jpeg', 'indoor', 0),
(2, '/uploads/houses/Unknown-1_660a.jpeg', 'outdoor', 0),
(2, '/uploads/houses/Unknown-3_b63f.jpeg', 'outdoor', 0),
(2, '/uploads/houses/Unknown-4_6642.jpeg', 'outdoor', 0),
(2, '/uploads/houses/Unknown-5_f9b6.jpeg', 'outdoor', 0),
(2, '/uploads/houses/Unknown_d395.jpeg', 'outdoor', 0),
(2, '/uploads/houses/Unknown-11_e2b2.jpeg', 'outdoor', 0);

-- Verknüpfung von Facilities mit Haus 3 (Ostsee Paradies)
INSERT INTO house_facilities (house_id, facility_id) VALUES 
(3, 1),  -- WiFi
(3, 2),  -- Swimming Pool
(3, 3);  -- Parking

-- Verknüpfung von Activities mit Haus 3 (Ostsee Paradies)
INSERT INTO house_activities (house_id, activity_id) VALUES 
(3, 1),  -- Cycling
(3, 2),  -- Fishing
(3, 3);  -- Horseback Riding

-- Verknüpfung von Tags mit Haus 3 (Ostsee Paradies)
INSERT INTO house_tags (house_id, tag_id)
SELECT 3, id FROM tags WHERE tag_name IN ('beach', 'adventure', 'luxury', 'nature', 'wildlife', 'culture', 'sunset');

-- Beispiel-Bilder für Haus 3 (Ostsee Paradies) einfügen
INSERT INTO house_images (house_id, image_url, image_type, is_main_image) VALUES
(3, '/uploads/houses/Unknown_3a20.jpeg', 'main', 1),
(3, '/uploads/houses/Unknown-8_9601.jpeg', 'indoor', 0),
(3, '/uploads/houses/Unknown-7_4f6b.jpeg', 'indoor', 0),
(3, '/uploads/houses/Unknown-6_29ee.jpeg', 'indoor', 0),
(3, '/uploads/houses/Unknown-5_6882.jpeg', 'indoor', 0),
(3, '/uploads/houses/Unknown-4_4c16.jpeg', 'indoor', 0),
(3, '/uploads/houses/Unknown-9_9b85.jpeg', 'indoor', 0),
(3, '/uploads/houses/Unknown-1_2232.jpeg', 'outdoor', 0),
(3, '/uploads/houses/Unknown-3_032f.jpeg', 'outdoor', 0),
(3, '/uploads/houses/images-1_54a7.jpeg', 'outdoor', 0),
(3, '/uploads/houses/images_ca10.jpeg', 'outdoor', 0),
(3, '/uploads/houses/Unknown-2_3c50.jpeg', 'outdoor', 0);
