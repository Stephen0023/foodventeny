CREATE DATABASE IF NOT EXISTS foodventeny_db;

USE foodventeny_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  user_type ENUM('vendor', 'event_organizer') NOT NULL
);


CREATE TABLE IF NOT EXISTS application_types (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  deadline DATE NOT NULL,
  cover_photo VARCHAR(255)
);


CREATE TABLE IF NOT EXISTS applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  application_type_id INT NOT NULL,
  status ENUM('pending', 'approved', 'waitlist', 'rejected', 'withdrawn') DEFAULT 'pending',
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (application_type_id) REFERENCES application_types(id)
);



