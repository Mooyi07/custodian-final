-- Create inventory history table
CREATE TABLE IF NOT EXISTS inventory_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    item_name VARCHAR(255),
    quantity_added INT NOT NULL,
    item_unit VARCHAR(50),
    date_added DATE NOT NULL,
    added_by INT,
    item_type VARCHAR(50) NOT NULL,
    FOREIGN KEY (item_id) REFERENCES academic_item(item_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (added_by) REFERENCES users(userId) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create trigger to update academic_item quantity
DELIMITER //
CREATE TRIGGER after_inventory_history_insert
AFTER INSERT ON inventory_history
FOR EACH ROW
BEGIN
    UPDATE academic_item
    SET item_qty = item_qty + NEW.quantity_added
    WHERE item_id = NEW.item_id;
END //
DELIMITER ;