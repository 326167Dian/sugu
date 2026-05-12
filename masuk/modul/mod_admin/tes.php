<?php
include "../../../configurasi/koneksi.php";

$datetime   = date('Y-m-d H:i:s', time());
$tanggal    = date('Y-m-d', time());

$stmt = $db->prepare("UPDATE barang SET waktu = ?,
                                        tgl = ?");
$stmt->execute([$datetime, $tanggal]);
?>