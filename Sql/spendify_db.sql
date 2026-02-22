CREATE DATABASE IF NOT EXISTS spendify;
USE spendify;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Stored as hashed strings
    profile_pic VARCHAR(255) DEFAULT 'default_avatar.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    category_id INT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL
);

INSERT INTO categories (category_id, category_name) VALUES
(1, 'Bills'),
(2, 'Education'),
(3, 'Entertainment'),
(4, 'Fashion'),
(5, 'Health'),
(6, 'Household'),
(7, 'Personal care'),
(8, 'Saving'),
(9, 'Transport'),
(10, 'Other');


CREATE TABLE expenses (
    expense_id INT AUTO_INCREMENT PRIMARY KEY,
    expense_date DATE NOT NULL,
    category_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description VARCHAR(255) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);