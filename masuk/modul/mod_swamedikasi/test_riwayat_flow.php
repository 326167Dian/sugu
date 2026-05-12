<?php
// Test script: insert -> update -> delete riwayat_pelanggan
require_once __DIR__ . '/../../../configurasi/koneksi.php';
$mysqli = $GLOBALS["___mysqli_ston"];

echo "Starting riwayat test...\n";

// 1) Insert test pelanggan
$nm = 'TEST_PEL_' . time();
$tlp = '0812345678';
$alamat = 'Alamat test';
$ket = 'Ket test';
$stmt = $db->prepare("INSERT INTO pelanggan (nm_pelanggan, tlp_pelanggan, alamat_pelanggan, ket_pelanggan) VALUES (?,?,?,?)");
if (!$stmt->execute([$nm, $tlp, $alamat, $ket])) {
    echo "Failed to insert pelanggan\n";
    exit(1);
}
$id_p = $db->lastInsertId();
echo "Inserted pelanggan id: $id_p\n";

// 2) Insert riwayat
$tgl = date('Y-m-d');
$diagnosa = 'Diagnosis test';
$tindakan = 'Tindakan test';
$followup = 'Followup test';
$stmt = $db->prepare("INSERT INTO riwayat_pelanggan (id_pelanggan, tgl, diagnosa, tindakan, followup, created_at) VALUES (?,?,?,?,?,NOW())");
if (!$stmt->execute([$id_p, $tgl, $diagnosa, $tindakan, $followup])) {
    echo "Failed to insert riwayat\n";
    // cleanup pelanggan
    $db->prepare("DELETE FROM pelanggan WHERE id_pelanggan=?")->execute([$id_p]);
    exit(1);
}
$id_r = $db->lastInsertId();
echo "Inserted riwayat id: $id_r\n";

// 3) Read inserted riwayat
$stmt = $db->prepare("SELECT * FROM riwayat_pelanggan WHERE id=?");
$stmt->execute([$id_r]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    echo "Riwayat row:\n";
    print_r($row);
} else {
    echo "Inserted riwayat not found.\n";
}

// 4) Update riwayat
$new_diag = 'Diagnosis updated';
if (!$db->prepare("UPDATE riwayat_pelanggan SET diagnosa=? WHERE id=?")->execute([$new_diag, $id_r])) {
    echo "Failed to update riwayat\n";
}
$stmt = $db->prepare("SELECT diagnosa FROM riwayat_pelanggan WHERE id=?");
$stmt->execute([$id_r]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "After update diagnosa: " . ($row['diagnosa'] ?? '(not found)') . "\n";

// 5) Delete riwayat
$db->prepare("DELETE FROM riwayat_pelanggan WHERE id=?")->execute([$id_r]);
$stmt = $db->prepare("SELECT count(*) as cnt FROM riwayat_pelanggan WHERE id=?");
$stmt->execute([$id_r]);
$cnt = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
echo "Deleted riwayat exists count: $cnt\n";

// Cleanup test pelanggan
$db->prepare("DELETE FROM pelanggan WHERE id_pelanggan=?")->execute([$id_p]);
echo "Cleanup done. Test completed successfully.\n";
exit(0);
