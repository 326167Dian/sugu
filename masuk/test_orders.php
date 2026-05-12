<?php
include "../configurasi/koneksi.php";

// Check table structure
echo "=== Table Structure ===\n";
$q = $db->query("DESCRIBE orders");
while($r = $q->fetch(PDO::FETCH_ASSOC)){
    echo $r['Field'] . ' - ' . $r['Type'] . "\n";
}

echo "\n=== Check id_resto values ===\n";
$q = $db->query("SELECT DISTINCT id_resto FROM orders");
while($r = $q->fetch(PDO::FETCH_ASSOC)){
    echo "id_resto: '" . $r['id_resto'] . "'\n";
}

echo "\n=== Count with id_resto = 'pesan' ===\n";
$q = $db->prepare("SELECT COUNT(*) as total FROM orders WHERE id_resto = 'pesan'");
$q->execute();
$r = $q->fetch(PDO::FETCH_ASSOC);
echo "Total: " . $r['total'] . "\n";

echo "\n=== Sample data ===\n";
$q = $db->prepare("SELECT * FROM orders WHERE id_resto = 'pesan' LIMIT 5");
$q->execute();
while($r = $q->fetch(PDO::FETCH_ASSOC)){
    print_r($r);
}
?>
