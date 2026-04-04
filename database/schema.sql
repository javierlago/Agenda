CREATE DATABASE IF NOT EXISTS agenda_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agenda_app;

/*-----------------------Table Users----------------------------------------------*/
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Longitud para hashes de password_hash()
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

/*------------------------ Table Login Attempts -------------------------------------------- */
CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(45) NOT NULL,
    email VARCHAR(100) NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip (ip),
    INDEX idx_email (email)
) ENGINE=InnoDB;

/*------------------------ Table Contacts --------------------------------------------------- */
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Relación: Si se borra el usuario, se borran sus contactos (ON DELETE CASCADE)
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
