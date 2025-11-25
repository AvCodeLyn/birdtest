CREATE TABLE quiz_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(255),
    orzel INT,
    papuga INT,
    golab INT,
    sowa INT,
    dominant_birds VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE,
    value TEXT
);

INSERT INTO settings (name, value) VALUES ('quiz_password_hash', '$2y$12$aJY6SteSVsM1vn/GqRvxGu06OlmJoc8m9Gjmz0C1/zaFmJmlO8cu6');

INSERT INTO users (username, password_hash, role)
VALUES ('in2grow', '$2y$12$e1w81lYifZEOeUXTtVgtzuCqMj/1.cMPyLVOc20rng/.hRTejifGG', 'admin');