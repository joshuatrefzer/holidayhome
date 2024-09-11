-- Erstellen der Tabellen

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user'
);

CREATE TABLE houses (
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

CREATE TABLE tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tag_name VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE house_tags (
    house_id INT,
    tag_id INT,
    PRIMARY KEY (house_id, tag_id),
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE house_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    house_id INT,
    image_url VARCHAR(255) NOT NULL,
    image_type ENUM('indoor', 'outdoor', 'main') NOT NULL, 
    is_main_image BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE
);


CREATE TABLE activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    activity_name VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE house_activities (
    house_id INT,
    activity_id INT,
    PRIMARY KEY (house_id, activity_id),
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
);

CREATE TABLE facilities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    facility_name VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE house_facilities (
    house_id INT,
    facility_id INT,
    PRIMARY KEY (house_id, facility_id),
    FOREIGN KEY (house_id) REFERENCES houses(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE
);

CREATE TABLE bookings (
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

-- Einfügen eines Beispiel-Users

INSERT INTO users (username, email, password_hash, role)
VALUES ('admin', 'admin@example.com', 'hashedPassword', 'admin');

-- Einfügen von 5 Facilities

INSERT INTO facilities (facility_name)
VALUES 
('WiFi'),
('Swimming Pool'),
('Parking'),
('Air Conditioning'),
('Heating');

-- Einfügen von 5 Activities

INSERT INTO activities (activity_name)
VALUES 
('Hiking'),
('Cycling'),
('Fishing'),
('Skiing'),
('Horseback Riding');


INSERT INTO tags (tag_name)
VALUES 
(' vacation'),
(' beach'),
('romantic');
