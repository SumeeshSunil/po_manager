CREATE DATABASE IF NOT EXISTS po_manager;
USE po_manager;

DROP TABLE IF EXISTS po_items;
DROP TABLE IF EXISTS purchase_orders;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    role ENUM('super','admin','user') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE purchase_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_number VARCHAR(100) NOT NULL,
    release_date DATE NOT NULL,
    expiry_date DATE NOT NULL,
    platform VARCHAR(100) NOT NULL,
    factory_name VARCHAR(100) NOT NULL,
    po_status ENUM('pending','in_progress','done') DEFAULT 'pending',
    delivery_decision ENUM('pending','can_deliver','cannot_deliver','partial_deliver') DEFAULT 'pending',
    delivery_reason TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE po_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_id INT NOT NULL,
    item_code VARCHAR(100) NOT NULL,
    item_description VARCHAR(255) NOT NULL,
    qty INT NOT NULL,
    user_status ENUM('pending','full','partial','cannot') DEFAULT 'pending',
    deliverable_qty INT DEFAULT 0,
    reason TEXT,
    updated_by INT DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

INSERT INTO users (name, username, password, role)
VALUES ('Super Admin', 'super', 'super123', 'super');