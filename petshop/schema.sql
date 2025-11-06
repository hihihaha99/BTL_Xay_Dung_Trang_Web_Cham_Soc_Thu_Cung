
-- Create database (run once)
CREATE DATABASE IF NOT EXISTS petcare_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE petcare_db;

-- Users
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Appointments
CREATE TABLE IF NOT EXISTS appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  pet_name VARCHAR(120) NOT NULL,
  visit_date DATETIME NOT NULL,
  notes VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Demo user (password: 123456)
INSERT INTO users (name, email, password_hash)
VALUES ('Demo User', 'demo@petcare.local', '$2y$10$Cq0Xy7fVyr5J2tH4YpJ7ReiGvXk5V1qk6xX2j9m1iR7k1m7cGxJ1a')
ON DUPLICATE KEY UPDATE email=email;
