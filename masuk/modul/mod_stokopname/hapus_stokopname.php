<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href='style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
	echo "<a href=../../index.php><b>LOGIN</b></a></center>";
} else {

	include "../../../configurasi/koneksi.php";
	include "../../../configurasi/fungsi_thumb.php";
	include "../../../configurasi/library.php";


	$id_stok_opname = $_POST['id_stok'];

	$stmt = $db->prepare("DELETE FROM stok_opname WHERE id_stok_opname = ?");
	$stmt->execute([$id_stok_opname]);
}
