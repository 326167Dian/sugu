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

	$module = $_GET['module'];
	$act = $_GET['act'];

	// Input admin
	if ($module == 'supplier' and $act == 'input_supplier') {

		$stmt = $db->prepare("SELECT COUNT(*) FROM supplier WHERE nm_supplier = ? AND tlp_supplier = ?");
		$stmt->execute([$_POST['nm_supplier'], $_POST['tlp_supplier']]);
		$ada = $stmt->fetchColumn();
		if ($ada > 0) {
			echo "<script type='text/javascript'>alert('Nama Supplier dengan nomor telepon ini sudah ada!');history.go(-1);</script>";
		} else {

			$stmt = $db->prepare("INSERT INTO supplier(nm_supplier, tlp_supplier, alamat_supplier, ket_supplier)
								 VALUES(?, ?, ?, ?)");
			$stmt->execute([$_POST['nm_supplier'], $_POST['tlp_supplier'], $_POST['alamat_supplier'], $_POST['ket_supplier']]);


			//echo "<script type='text/javascript'>alert('Data berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
			header('location:../../media_admin.php?module=' . $module);
		}
	}
	//updata supplier
	elseif ($module == 'supplier' and $act == 'update_supplier') {

		$stmt = $db->prepare("UPDATE supplier SET nm_supplier = ?,
									tlp_supplier = ?,
									alamat_supplier = ?,
									ket_supplier = ?
									WHERE id_supplier = ?");
		$stmt->execute([$_POST['nm_supplier'], $_POST['tlp_supplier'], $_POST['alamat_supplier'], $_POST['ket_supplier'], $_POST['id']]);

		//echo "<script type='text/javascript'>alert('Data berhasil diubah !');window.location='../../media_admin.php?module=".$module."'</script>";
		header('location:../../media_admin.php?module=' . $module);
	}
	//Hapus Proyek
	elseif ($module == 'supplier' and $act == 'hapus') {

		$stmt = $db->prepare("DELETE FROM supplier WHERE id_supplier = ?");
		$stmt->execute([$_GET['id']]);
		//echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module."'</script>";
		header('location:../../media_admin.php?module=' . $module);
	}
	// simpan data obat supplier
	elseif ($module == 'supplier' and $act == 'simpanbarang') {
		# code...
		$idsupplier = $_POST['id_supplier'];
		$idbarang = $_POST['id_barang'];
		$stmt = $db->prepare("INSERT INTO barang_supplier(id_supplier, id_barang) VALUES(?, ?)");
		$stmt->execute([$idsupplier, $idbarang]);
	}
	// hapus data obat supplier
	elseif ($module == 'supplier' and $act == 'hapusbarang') {
		# code...
		$stmt = $db->prepare("DELETE FROM barang_supplier WHERE id_brgsup = ?");
		$stmt->execute([$_POST['id_brgsup']]);
	}
}
