<?php
// Quick CLI test for pelanggan_serverside.php
require_once __DIR__ . '/../../../configurasi/koneksi.php';
// create test pelanggan
$nm = 'Test Pel '.time();
$tlp = '081234567';
$alamat = 'Alamat Test';
$ket = 'Keterangan Test';
$stmt = $db->prepare("INSERT INTO pelanggan (nm_pelanggan, tlp_pelanggan, alamat_pelanggan, ket_pelanggan) VALUES (?,?,?,?)");
$stmt->execute([$nm, $tlp, $alamat, $ket]);
$id = $db->lastInsertId();
// insert riwayat
$follow = 'Follow up test '.date('Y-m-d H:i:s');
$db->prepare("INSERT INTO riwayat_pelanggan (id_pelanggan, tgl, diagnosa, tindakan, followup, created_at) VALUES (?, ?, 'diag', 'tindakan', ?, NOW())")->execute([$id, date('Y-m-d'), $follow]);
// set GET action and prepare POST variables
$_GET['action'] = 'table_data';
$_POST['draw'] = 1;
$_POST['length'] = 10;
$_POST['start'] = 0;
$_POST['order'] = array(array('column' => 0, 'dir' => 'asc'));
$_POST['search'] = array('value' => '');
// include the server-side script and capture output
chdir(__DIR__);
ob_start();
include 'pelanggan_serverside.php';
$output = ob_get_clean();
echo "Response:\n";
echo $output . "\n";
// cleanup
$db->prepare("DELETE FROM riwayat_pelanggan WHERE id_pelanggan=?")->execute([$id]);
$db->prepare("DELETE FROM pelanggan WHERE id_pelanggan=?")->execute([$id]);
?>