<?php
session_start();
include "../../../configurasi/koneksi.php";

$id = $_POST['id'];
$datetime = date('Y-m-d H:i:s', time());

$stmt = $db->prepare("UPDATE riwayat_pelanggan 
                        SET tgl_followup    = ?,
                            followup_by     =?            
                        WHERE id = ?");
$stmt->execute([$datetime, $_SESSION['namalengkap'], $id]);

$data = array("status"=>"success");
echo json_encode($data);
?>