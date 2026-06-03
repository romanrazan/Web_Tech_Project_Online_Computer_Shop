-- Sample data for Razon final project. Run after schema is created.
USE computer_shop;

INSERT INTO users (id, name, email, password_hash, role) VALUES
(1, 'Admin User', 'admin@shop.com', '$2y$12$ZpsllcWYKqa/NueCxBgjOOtSwLBIxmHkg8zd8pseOrTZ/RJVtz65S', 'admin'),
(2, 'Customer User', 'customer@shop.com', '$2y$12$Ds4Jw72bEIl3vmJo8VTEgO7bTTgL.e9xwpl00YuM0kLM.nF4CJpCi', 'customer');

INSERT INTO categories (id, name, parent_id) VALUES
(1, 'Laptop', NULL),
(2, 'Monitor', NULL),
(3, 'Keyboard', NULL),
(4, 'Mouse', NULL),
(5, 'Headphone', NULL),
(6, 'Storage', NULL),
(7, 'SSD', 6),
(8, 'HDD', 6);

INSERT INTO brands (id, name, category_id) VALUES
(1, 'ASUS', 1),
(2, 'Dell', 1),
(3, 'HP', 1),
(4, 'LG', 2),
(5, 'Samsung', 2),
(6, 'A4Tech', 3),
(7, 'Logitech', 3),
(8, 'Logitech', 4),
(9, 'A4Tech', 4),
(10, 'Generic', 5),
(11, 'Samsung', 7),
(12, 'Western Digital', 8);

INSERT INTO products (name, description, manufacturer_review, price, category_id, brand_id, image_path, stock) VALUES
('ASUS Gaming Laptop', 'High performance laptop for study, coding and gaming.', 'Strong processor, good build quality and smooth daily performance.', 85000.00, 1, 1, 'public/uploads/products/laptop1.jpg', 8),
('Dell Student Laptop', 'Reliable laptop for university assignments and online classes.', 'Balanced performance with good battery backup for students.', 62000.00, 1, 2, 'public/uploads/products/laptop2.jpg', 10),
('HP Office Laptop', 'Office laptop for browsing, document work and light programming.', 'Clean design and stable performance for regular users.', 58000.00, 1, 3, 'public/uploads/products/laptop3.jpg', 12),
('LG 24 Inch Monitor', 'Full HD monitor for office, study and entertainment.', 'Good color quality and comfortable viewing angle.', 18500.00, 2, 4, 'public/uploads/products/monitor1.jpg', 15),
('Samsung 27 Inch Monitor', 'Large display monitor for multitasking and gaming.', 'Sharp display output with a modern slim design.', 24500.00, 2, 5, 'public/uploads/products/monitor2.jpg', 7),
('A4Tech Keyboard', 'Durable keyboard for typing and regular desktop use.', 'Comfortable key response and long service life.', 950.00, 3, 6, 'public/uploads/products/keyboard1.jpg', 30),
('Logitech Wireless Keyboard', 'Wireless keyboard for clean desk setup.', 'Stable connectivity and comfortable typing experience.', 1850.00, 3, 7, 'public/uploads/products/keyboard2.jpg', 20),
('Logitech Gaming Mouse', 'Gaming mouse with accurate tracking and comfortable grip.', 'Responsive sensor and good control for gaming.', 1450.00, 4, 8, 'public/uploads/products/mouse1.jpg', 25),
('A4Tech Optical Mouse', 'Simple wired optical mouse for daily use.', 'Budget friendly and reliable for regular work.', 550.00, 4, 9, 'public/uploads/products/mouse2.jpg', 35),
('Gaming Headphone', 'Headphone with microphone for gaming and online meetings.', 'Clear sound, comfortable ear cushions and good voice pickup.', 2200.00, 5, 10, 'public/uploads/products/headphone1.jpg', 18),
('Samsung 500GB SSD', 'Fast solid state drive for laptop and desktop upgrade.', 'Excellent read/write speed and better system boot time.', 6200.00, 7, 11, 'public/uploads/products/ssd1.jpg', 14),
('Western Digital 1TB HDD', 'Large capacity hard drive for storage and backup.', 'Reliable storage option for files, videos and software.', 4200.00, 8, 12, 'public/uploads/products/hdd1.jpg', 11);

INSERT INTO reviews (product_id, user_id, reviewer_name, comment) VALUES
(1, 2, 'Customer User', 'Good laptop for study and project work.'),
(8, 2, 'Customer User', 'Mouse response is smooth for gaming.'),
(11, 2, 'Customer User', 'SSD makes the system much faster.');
