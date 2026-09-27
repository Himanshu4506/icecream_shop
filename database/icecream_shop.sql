-- =========================================================
-- Ice Cream Shop Management System - Database Schema
-- Import this file in phpMyAdmin or via:
--   mysql -u root -p < icecream_shop.sql
-- =========================================================

CREATE DATABASE IF NOT EXISTS icecream_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE icecream_shop;

-- ---------------------------------------------------------
-- Table: admin
-- ---------------------------------------------------------
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin login -> username: admin | password: admin123
-- (password below is a bcrypt hash of "admin123")
INSERT INTO admin (username, password, full_name) VALUES
('admin', '$2b$12$Xp22pZIiKtef8JzAii6.zOKHXfwwQzqsFq6SkD0LgupRyt7cEyEOW', 'Shop Administrator');

-- ---------------------------------------------------------
-- Table: customers
-- ---------------------------------------------------------
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Table: products
-- ---------------------------------------------------------
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default.jpg',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (name, category, description, price, image, status) VALUES
('Vanilla Bean', 'Classic', 'Rich Madagascar vanilla bean ice cream.', 3.50, 'vanilla.jpg', 'active'),
('Chocolate Fudge', 'Classic', 'Deep chocolate ice cream with fudge swirls.', 3.75, 'chocolate.jpg', 'active'),
('Strawberry Swirl', 'Fruity', 'Fresh strawberry ice cream with fruit swirl.', 3.75, 'strawberry.jpg', 'active'),
('Mango Tango', 'Fruity', 'Sweet Alphonso mango ice cream.', 4.00, 'mango.jpg', 'active'),
('Mint Choco Chip', 'Classic', 'Cool mint ice cream loaded with choco chips.', 3.75, 'mint.jpg', 'active'),
('Cookies & Cream', 'Specialty', 'Vanilla ice cream packed with cookie crumbles.', 4.00, 'cookies.jpg', 'active'),
('Butterscotch Crunch', 'Specialty', 'Butterscotch ice cream with caramel crunch bits.', 4.00, 'butterscotch.jpg', 'active'),
('Pistachio Delight', 'Premium', 'Roasted pistachio ice cream, rich and nutty.', 4.50, 'pistachio.jpg', 'active'),
('Belgian Chocolate', 'Premium', 'Made with imported Belgian dark chocolate.', 4.75, 'belgian.jpg', 'active'),
('Salted Caramel', 'Specialty', 'Creamy caramel ice cream with a hint of sea salt.', 4.25, 'caramel.jpg', 'active');

-- ---------------------------------------------------------
-- Table: orders
-- ---------------------------------------------------------
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    delivery_type ENUM('Pickup','Home Delivery') DEFAULT 'Home Delivery',
    delivery_address TEXT,
    payment_method VARCHAR(50) DEFAULT 'Cash on Delivery',
    notes TEXT,
    order_status ENUM('Order Placed','Preparing','Out for Delivery','Delivered','Cancelled') DEFAULT 'Order Placed',
    estimated_delivery DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- Table: order_items (line items for each order)
-- ---------------------------------------------------------
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    flavor_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ---------------------------------------------------------
-- Table: delivery_status (status history / tracking log)
-- ---------------------------------------------------------
CREATE TABLE delivery_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    remarks VARCHAR(255) DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- Table: testimonials (shown on home page)
-- ---------------------------------------------------------
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    rating INT DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO testimonials (customer_name, message, rating) VALUES
('Ananya Shah', 'Best ice cream in town! The Belgian Chocolate is out of this world.', 5),
('Rohan Mehta', 'Fast delivery and the flavors are always fresh. My kids love the party orders!', 5),
('Priya Patel', 'The Pistachio Delight is unbelievably creamy. Highly recommend the custom orders.', 4),
('Karan Joshi', 'Ordered for my daughter''s birthday party - everyone loved it. Great service!', 5);

-- =========================================================
-- End of schema
-- =========================================================
