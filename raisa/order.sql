-- Suggested SQL for the 'orders' table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL UNIQUE, -- Unique order identifier (e.g., PO001)
    customer_name VARCHAR(255) NOT NULL,
    item_name VARCHAR(255) NOT NULL, -- Name of the item ordered
    item_id INT, -- Optional: Link to the inventory table
    quantity INT NOT NULL,
    status ENUM('Pending', 'Processing', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES inventory(id) ON DELETE SET NULL -- Optional foreign key
);