<?php
include "../configurasi/koneksi.php";

echo "=== Running Migration: Add column 'masuk' to orders ===\n\n";

// Check if column already exists
$check = $db->query("SHOW COLUMNS FROM orders LIKE 'masuk'");
if ($check->rowCount() > 0) {
    echo "Column 'masuk' already exists in orders table.\n";
} else {
    // Add column
    echo "Adding column 'masuk' to orders table...\n";
    $db->exec("ALTER TABLE orders ADD COLUMN masuk ENUM('0','1') NOT NULL DEFAULT '1' AFTER tandatangan");
    echo "Column added successfully.\n";
    
    // Update existing orders
    echo "Updating existing orders...\n";
    $db->exec("UPDATE orders o SET o.masuk = '0' WHERE EXISTS (SELECT 1 FROM trbmasuk t WHERE t.kd_orders = o.kd_trbmasuk)");
    echo "Updated " . $db->query("SELECT ROW_COUNT()")->fetchColumn() . " records.\n";
}

// Add indexes
echo "\nAdding indexes...\n";
try {
    $db->exec("ALTER TABLE orders ADD INDEX idx_orders_masuk (masuk)");
    echo "Index idx_orders_masuk added.\n";
} catch (Exception $e) {
    echo "Index idx_orders_masuk already exists or error: " . $e->getMessage() . "\n";
}

try {
    $db->exec("ALTER TABLE orders ADD INDEX idx_orders_id_resto (id_resto)");
    echo "Index idx_orders_id_resto added.\n";
} catch (Exception $e) {
    echo "Index idx_orders_id_resto already exists or error: " . $e->getMessage() . "\n";
}

echo "\n=== Migration Complete ===\n";

// Show sample data
echo "\n=== Sample data from orders ===\n";
$q = $db->query("SELECT id_trbmasuk, kd_trbmasuk, nm_supplier, masuk FROM orders WHERE id_resto = 'pesan' LIMIT 5");
while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$r['id_trbmasuk']}, Kode: {$r['kd_trbmasuk']}, Supplier: {$r['nm_supplier']}, Masuk: {$r['masuk']}\n";
}
?>
