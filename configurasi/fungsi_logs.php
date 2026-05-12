<?php
include "koneksi.php";

function insertlogs($id, $petugas, $action)
{
    global $db;
    $tglcurrent = date("Y-m-d H:i:s", time());
    $stmt = $db->prepare("INSERT INTO logs(id_admin, petugas, aksi, waktu) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id, $petugas, $action, $tglcurrent]);
}
