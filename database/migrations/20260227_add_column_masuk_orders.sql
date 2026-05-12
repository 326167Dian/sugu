-- Add column 'masuk' to orders table
-- This column indicates whether the order has been received (0) or not (1)

ALTER TABLE orders ADD COLUMN masuk ENUM('0','1') NOT NULL DEFAULT '1' AFTER tandatangan;

-- Update existing orders: set masuk = '0' where there's already a trbmasuk record
UPDATE orders o
SET o.masuk = '0'
WHERE EXISTS (
    SELECT 1 FROM trbmasuk t 
    WHERE t.kd_orders = o.kd_trbmasuk
);

-- Add index for better performance
ALTER TABLE orders ADD INDEX idx_orders_masuk (masuk);
ALTER TABLE orders ADD INDEX idx_orders_id_resto (id_resto);
