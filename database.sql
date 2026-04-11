-- Database schema for Bajari Store
-- Run this using phpMyAdmin, MySQL CLI, or another MySQL client.

CREATE DATABASE IF NOT EXISTS bajari_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bajari_store;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  image VARCHAR(255) NOT NULL,
  stock INT NOT NULL DEFAULT 20,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS carts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id VARCHAR(128) NOT NULL,
  user_id INT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  session_id VARCHAR(128) NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO products (name, price, image, stock) VALUES
('A4 Paper', 200, 'photos/A4paper.png', 20),
('Bed', 12000, 'photos/bed.jpg', 8),
('Bodycon Dress', 1800, 'photos/bodycon.jpg', 5),
('Book Rack', 4000, 'photos/book rack.jpg', 14),
('Book', 500, 'photos/book.jpg', 20),
('Coat', 3500, 'photos/coat.jpg', 7),
('Copy', 100, 'photos/copy.jpg', 40),
('Daraz Product', 9999, 'photos/daraz.jpg', 2),
('Daura Suruwal', 2500, 'photos/daurasurwal.jpg', 11),
('Formal Pant', 1500, 'photos/formalpant.jpg', 18),
('Harpic', 300, 'photos/harpic.jpg', 0),
('iPhone', 95000, 'photos/iphone.jpg', 0),
('Iron', 2500, 'photos/iron.jpg', 12),
('Karua', 800, 'photos/karuwa.jpg', 23),
('Kitchen Rack', 6000, 'photos/kitchen-Rack.jpg', 6),
('Laptop', 85000, 'photos/laptop.jpg', 3),
('Macbook', 125000, 'photos/macbook.jpg', 1),
('Mouse', 900, 'photos/mouse.jpg', 45),
('Pants', 1200, 'photos/pants.jpg', 17),
('Party Dress', 3000, 'photos/partydress.jpg', 10),
('Pen', 40, 'photos/pen.jpg', 60),
('Rabbit Toy', 1200, 'photos/rabbit.jpg', 9),
('Samsung Phone', 70000, 'photos/samsung.jpg', 2),
('Soap', 120, 'photos/soap.jpg', 70),
('Stainless Steel', 2000, 'photos/stainless steel.webp', 8),
('Tide', 500, 'photos/tide.jpg', 25),
('T-Shirt', 700, 'photos/tshirt.jpg', 30),
('Wooden Spoon', 250, 'photos/wooden-spoon.jpg', 20);
