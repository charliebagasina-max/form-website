
CREATE DATABASE IF NOT EXISTS personal_info_db;
USE personal_info_db;

CREATE TABLE IF NOT EXISTS people (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    age INT,
    nationality VARCHAR(100),
    gender VARCHAR(10),
    birthdate DATE,
    hobbies TEXT,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);