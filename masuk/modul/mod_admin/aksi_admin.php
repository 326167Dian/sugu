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

	if (isset($_POST['mpengguna'])) {
		$mpengguna = "Y";
	} else {
		$mpengguna = "N";
	}

	if (isset($_POST['mheader'])) {
		$mheader = "Y";
	} else {
		$mheader = "N";
	}

	if (isset($_POST['mjenisbayar'])) {
		$mjenisbayar = "Y";
	} else {
		$mjenisbayar = "N";
	}

	if (isset($_POST['mpelanggan'])) {
		$mpelanggan = "Y";
	} else {
		$mpelanggan = "N";
	}

	if (isset($_POST['msupplier'])) {
		$msupplier = "Y";
	} else {
		$msupplier = "N";
	}

	if (isset($_POST['msatuan'])) {
		$msatuan = "Y";
	} else {
		$msatuan = "N";
	}

	if (isset($_POST['mjenisobat'])) {
		$mjenisobat = "Y";
	} else {
		$mjenisobat = "N";
	}


	if (isset($_POST['mbarang'])) {
		$mbarang = "Y";
	} else {
		$mbarang = "N";
	}

	if (isset($_POST['tbm'])) {
		$tbm = "Y";
	} else {
		$tbm = "N";
	}

	if (isset($_POST['tbmpbf'])) {
		$tbmpbf = "Y";
	} else {
		$tbmpbf = "N";
	}

	if (isset($_POST['tpk'])) {
		$tpk = "Y";
	} else {
		$tpk = "N";
	}

	if (isset($_POST['lpitem'])) {
		$lpitem = "Y";
	} else {
		$lpitem = "N";
	}

	if (isset($_POST['lpbrgmasuk'])) {
		$lpbrgmasuk = "Y";
	} else {
		$lpbrgmasuk = "N";
	}

	if (isset($_POST['lpkasir'])) {
		$lpkasir = "Y";
	} else {
		$lpkasir = "N";
	}

	if (isset($_POST['lpsupplier'])) {
		$lpsupplier = "Y";
	} else {
		$lpsupplier = "N";
	}

	if (isset($_POST['lppelanggan'])) {
		$lppelanggan = "Y";
	} else {
		$lppelanggan = "N";
	}

	if (isset($_POST['mstok'])) {
		$mstok = "Y";
	} else {
		$mstok = "N";
	}
	if (isset($_POST['stok_kritis'])) {
		$stok_kritis = "Y";
	} else {
		$stok_kritis = "N";
	}
	if (isset($_POST['orders'])) {
		$orders = "Y";
	} else {
		$orders = "N";
	}
	if (isset($_POST['penjualansebelum'])) {
		$penjualansebelum = "Y";
	} else {
		$penjualansebelum = "N";
	}
	if (isset($_POST['labapenjualan'])) {
		$labapenjualan = "Y";
	} else {
		$labapenjualan = "N";
	}
	if (isset($_POST['byrkredit'])) {
		$byrkredit = "Y";
	} else {
		$byrkredit = "N";
	}
	if (isset($_POST['stokopname'])) {
		$stokopname = "Y";
	} else {
		$stokopname = "N";
	}
	if (isset($_POST['soharian'])) {
		$soharian = "Y";
	} else {
		$soharian = "N";
	}
	if (isset($_POST['labajenisobat'])) {
		$labajenisobat = "Y";
	} else {
		$labajenisobat = "N";
	}
	if (isset($_POST['koreksistok'])) {
		$koreksistok = "Y";
	} else {
		$koreksistok = "N";
	}
	if (isset($_POST['shiftkerja'])) {
		$shiftkerja = "Y";
	} else {
		$shiftkerja = "N";
	}

	// neraca
	if (isset($_POST['neraca'])) {
		$neraca = "Y";
	} else {
		$neraca = "N";
	}

	// komisi
	if (isset($_POST['komisi'])) {
		$komisi = "Y";
	} else {
		$komisi = "N";
	}

	// kart stok
	if (isset($_POST['kartustok'])) {
		$kartustok = "Y";
	} else {
		$kartustok = "N";
	}
	// catatan
	if (isset($_POST['catatan'])) {
		$catatan = "Y";
	} else {
		$catatan = "N";
	}
	// cek darah
	if (isset($_POST['cekdarah'])) {
		$cekdarah = "Y";
	} else {
		$cekdarah = "N";
	}

	// Jurnal Kas
	if (isset($_POST['jurnalkas'])) {
		$jurnalkas = "Y";
	} else {
		$jurnalkas = "N";
	}

	// Input admin
	if ($module == 'admin' and $act == 'input_admin') {

		$usernamenya = $_POST['username'];
		$passwordnya = $_POST['password'];

		if (!preg_match("/^[a-zA-Z0-9]*$/", $usernamenya)) {
			echo "<script type='text/javascript'>alert('Input Username hanya huruf dan angka yang diijinkan, dan tidak boleh menggunakan spasi ...!');history.go(-1);</script>";
		} else {

			if (!preg_match("/^[a-zA-Z0-9]*$/", $passwordnya)) {
				echo "<script type='text/javascript'>alert('Input Password hanya huruf dan angka yang diijinkan, dan tidak boleh menggunakan spasi ...!');history.go(-1);</script>";
			} else {

				$pass = password_hash($_POST['password'],PASSWORD_BCRYPT);

				$cekuser = $db->prepare("SELECT * FROM admin WHERE username=?");
				$cekuser->execute([$_POST['username']]);
				$ketemu = $cekuser->rowCount();
				if ($ketemu > 0) {
					echo "<script type='text/javascript'>alert('Username sudah digunakan!');history.go(-1);</script>";
				} else {

					$stmt = $db->prepare("INSERT INTO admin(username,
                                 password,
                                 nama_lengkap,
                                 no_telp,
								 blokir,
								 mpengguna,
								 mheader,
								 mjenisbayar,
								 mpelanggan,
								 msupplier,
								 msatuan,
								 mjenisobat,
								 mbarang,
								 tbm,
								 tbmpbf,
								 tpk,
								 lpitem,
								 lpbrgmasuk,
								 lpkasir,
								 lpsupplier,
								 lppelanggan,
								 mstok,
								 stok_kritis,								 
								 orders,
								 penjualansebelum,
								 labapenjualan,
								 byrkredit,
								 stokopname,
								 soharian,
								 labajenisobat,
								 koreksistok,
								 shiftkerja,
								 neraca,
								 akses_level,
								 komisi,
								 kartustok,
								 catatan,
								 cekdarah,
								 jurnalkas)
	                       VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
					$stmt->execute([$_POST['username'], $pass, $_POST['nama_lengkap'], $_POST['no_telp'], $_POST['blokir'], $mpengguna, $mheader, $mjenisbayar, $mpelanggan, $msupplier, $msatuan, $mjenisobat, $mbarang, $tbm, $tbmpbf, $tpk, $lpitem, $lpbrgmasuk, $lpkasir, $lpsupplier, $lppelanggan, $mstok, $stok_kritis, $orders, $penjualansebelum, $labapenjualan, $byrkredit, $stokopname, $soharian, $labajenisobat, $koreksistok, $shiftkerja, $neraca, $_POST['level'], $komisi, $kartustok, $catatan, $cekdarah, $jurnalkas]);

					header('location:../../media_admin.php?module=' . $module);
				}
			}
		}
	}
	// Update admin
	elseif ($module == 'admin' and $act == 'update_admin') {
		if (empty($_POST['password'])) {

			$usernamenya = $_POST['username'];

			if (!preg_match("/^[a-zA-Z0-9]*$/", $usernamenya)) {
				echo "<script type='text/javascript'>alert('Ubah Username hanya huruf dan angka yang diijinkan, dan tidak boleh menggunakan spasi ...!');history.go(-1);</script>";
			} else {

				$stmt = $db->prepare("UPDATE admin SET username = ?,
                                nama_lengkap = ?,
                                no_telp = ?,
								blokir = ?,
								mpengguna = ?,
								mheader = ?,
								mjenisbayar = ?,
								mpelanggan = ?,
								msupplier = ?,
								msatuan = ?,
								mjenisobat = ?,
								mbarang = ?,
								tbm = ?,
								tbmpbf = ?,
								tpk = ?,
								lpitem = ?,
								lpbrgmasuk = ?,
								lpkasir = ?,
								lpsupplier = ?,
                                lppelanggan = ?,
                                mstok = ?,                  
                                stok_kritis = ?,                  
                                orders = ?,                  
                                penjualansebelum = ?,
                                labapenjualan = ?,
                                byrkredit = ?,                  
                                stokopname = ?,                  
                                soharian = ?,                  
                                labajenisobat = ?,
                                koreksistok = ?,
                                shiftkerja = ?,
                                neraca = ?,
								akses_level = ?,
								komisi = ?,
								kartustok = ?,
								catatan = ?,
								cekdarah = ?,
								jurnalkas = ?
                        WHERE id_admin = ?");
				$stmt->execute([$_POST['username'], $_POST['nama_lengkap'], $_POST['no_telp'], $_POST['blokir'], $mpengguna, $mheader, $mjenisbayar, $mpelanggan, $msupplier, $msatuan, $mjenisobat, $mbarang, $tbm, $tbmpbf, $tpk, $lpitem, $lpbrgmasuk, $lpkasir, $lpsupplier, $lppelanggan, $mstok, $stok_kritis, $orders, $penjualansebelum, $labapenjualan, $byrkredit, $stokopname, $soharian, $labajenisobat, $koreksistok, $shiftkerja, $neraca, $_POST['level'], $komisi, $kartustok, $catatan, $cekdarah, $jurnalkas, $_POST['id']]);

				$login = $db->prepare("SELECT * FROM admin WHERE username=? AND password=? AND blokir='N'");
				$login->execute([$_SESSION['username'], $_SESSION['passuser']]);
				$r = $login->fetch(PDO::FETCH_ASSOC);
				$ketemu = $login->rowCount();

				if ($ketemu > 0) {
					session_reset();

					$_SESSION['idadmin']    = $r['id_admin'];
					$_SESSION['username']    = $r['username'];
					$_SESSION['namauser']    = $r['username'];
					$_SESSION['namalengkap'] = $r['nama_lengkap'];
					$_SESSION['passuser']    = $r['password'];
					$_SESSION['leveluser']   = "admin";
					$_SESSION['mpengguna']   = $r['mpengguna'];
					$_SESSION['mheader']   = $r['mheader'];
					$_SESSION['mjenisbayar']     = $r['mjenisbayar'];
					$_SESSION['mpelanggan']     = $r['mpelanggan'];
					$_SESSION['msupplier']     = $r['msupplier'];
					$_SESSION['msatuan']   = $r['msatuan'];
					$_SESSION['mjenisobat']   = $r['mjenisobat'];
					$_SESSION['mbarang']      = $r['mbarang'];
					$_SESSION['tbm']    = $r['tbm'];
					$_SESSION['tbmpbf']    = $r['tbmpbf'];
					$_SESSION['tpk']    = $r['tpk'];
					$_SESSION['lpitem'] = $r['lpitem'];
					$_SESSION['lpbrgmasuk'] = $r['lpbrgmasuk'];
					$_SESSION['lpkasir'] = $r['lpkasir'];
					$_SESSION['lpsupplier'] = $r['lpsupplier'];
					$_SESSION['lppelanggan'] = $r['lppelanggan'];
					$_SESSION['mstok'] = $r['mstok'];
					$_SESSION['stok_kritis'] = $r['stok_kritis'];
					$_SESSION['orders'] = $r['orders'];
					$_SESSION['penjualansebelum'] = $r['penjualansebelum'];
					$_SESSION['labapenjualan'] = $r['labapenjualan'];
					$_SESSION['byrkredit'] = $r['byrkredit'];
					$_SESSION['stokopname'] = $r['stokopname'];
					$_SESSION['soharian'] = $r['soharian'];
					$_SESSION['labajenisobat'] = $r['labajenisobat'];
					$_SESSION['koreksistok'] = $r['koreksistok'];
					$_SESSION['shiftkerja'] = $r['shiftkerja'];
					$_SESSION['neraca'] = $r['neraca'];
					$_SESSION['level'] = $r['akses_level'];
					$_SESSION['komisi'] = $r['komisi'];
					$_SESSION['kartustok'] = $r['kartustok'];
					$_SESSION['catatan'] = $r['catatan'];
					$_SESSION['cekdarah'] = $r['cekdarah'];
					$_SESSION['jurnalkas'] = $r['jurnalkas'];
				}
			}
		}
		// Apabila password diubah
		else {

			$usernamenya = $_POST['username'];
			$passwordnya = $_POST['password'];

			if (!preg_match("/^[a-zA-Z0-9]*$/", $usernamenya)) {
				echo "<script type='text/javascript'>alert('Ubah Username hanya huruf dan angka yang diijinkan, dan tidak boleh menggunakan spasi ...!');history.go(-1);</script>";
			} else {

				if (!preg_match("/^[a-zA-Z0-9]*$/", $passwordnya)) {
					echo "<script type='text/javascript'>alert('Ubah Password hanya huruf dan angka yang diijinkan, dan tidak boleh menggunakan spasi ...!');history.go(-1);</script>";
				} else {

					$pass = password_hash($_POST['password'],PASSWORD_BCRYPT);
					$stmt = $db->prepare("UPDATE admin SET password = ?,
                                nama_lengkap = ?,
                                no_telp = ?,
								blokir = ?,
                                mpengguna = ?,
								mheader = ?,
								mjenisbayar = ?,
								mpelanggan = ?,
								msupplier = ?,
								msatuan = ?,
								mjenisobat = ?,
								mbarang = ?,
								tbm = ?,
								tbmpbf = ?,
								tpk = ?,
								lpitem = ?,
								lpbrgmasuk = ?,
								lpkasir = ?,
								lpsupplier = ?,
                                lppelanggan = ?,
                                mstok = ?,
                                stok_kritis = ?,
                                orders = ?,
                                penjualansebelum = ?,
                                labapenjualan = ?,
                                byrkredit = ?,
                                stokopname = ?,
                                soharian = ?,
                                labajenisobat = ?,
                                koreksistok = ?,
                                shiftkerja = ?,
                                neraca = ?,
								akses_level = ?,
								komisi = ?,
								kartustok = ?,
								catatan = ?,
								cekdarah = ?,
								jurnalkas = ?
                           WHERE id_admin = ?");
					$stmt->execute([$pass, $_POST['nama_lengkap'], $_POST['no_telp'], $_POST['blokir'], $mpengguna, $mheader, $mjenisbayar, $mpelanggan, $msupplier, $msatuan, $mjenisobat, $mbarang, $tbm, $tbmpbf, $tpk, $lpitem, $lpbrgmasuk, $lpkasir, $lpsupplier, $lppelanggan, $mstok, $stok_kritis, $orders, $penjualansebelum, $labapenjualan, $byrkredit, $stokopname, $soharian, $labajenisobat, $koreksistok, $shiftkerja, $neraca, $_POST['level'], $komisi, $kartustok, $catatan, $cekdarah, $jurnalkas, $_POST['id']]);

					$login = $db->prepare("SELECT * FROM admin WHERE username=? AND password=? AND blokir='N'");
					$login->execute([$_SESSION['username'], $_SESSION['passuser']]);
					$r = $login->fetch(PDO::FETCH_ASSOC);
					$ketemu = $login->rowCount();

					if ($ketemu > 0) {
						session_reset();

						$_SESSION['idadmin']    = $r['id_admin'];
						$_SESSION['username']    = $r['username'];
						$_SESSION['namauser']    = $r['username'];
						$_SESSION['namalengkap'] = $r['nama_lengkap'];
						$_SESSION['passuser']    = $r['password'];
						$_SESSION['leveluser']   = "admin";
						$_SESSION['mpengguna']   = $r['mpengguna'];
						$_SESSION['mheader']   = $r['mheader'];
						$_SESSION['mjenisbayar']     = $r['mjenisbayar'];
						$_SESSION['mpelanggan']     = $r['mpelanggan'];
						$_SESSION['msupplier']     = $r['msupplier'];
						$_SESSION['msatuan']   = $r['msatuan'];
						$_SESSION['mjenisobat']   = $r['mjenisobat'];
						$_SESSION['mbarang']      = $r['mbarang'];
						$_SESSION['tbm']    = $r['tbm'];
						$_SESSION['tbmpbf']    = $r['tbmpbf'];
						$_SESSION['tpk']    = $r['tpk'];
						$_SESSION['lpitem'] = $r['lpitem'];
						$_SESSION['lpbrgmasuk'] = $r['lpbrgmasuk'];
						$_SESSION['lpkasir'] = $r['lpkasir'];
						$_SESSION['lpsupplier'] = $r['lpsupplier'];
						$_SESSION['lppelanggan'] = $r['lppelanggan'];
						$_SESSION['mstok'] = $r['mstok'];
						$_SESSION['stok_kritis'] = $r['stok_kritis'];
						$_SESSION['orders'] = $r['orders'];
						$_SESSION['penjualansebelum'] = $r['penjualansebelum'];
						$_SESSION['labapenjualan'] = $r['labapenjualan'];
						$_SESSION['byrkredit'] = $r['byrkredit'];
						$_SESSION['stokopname'] = $r['stokopname'];
						$_SESSION['soharian'] = $r['soharian'];
						$_SESSION['labajenisobat'] = $r['labajenisobat'];
						$_SESSION['koreksistok'] = $r['koreksistok'];
						$_SESSION['shiftkerja'] = $r['shiftkerja'];
						$_SESSION['neraca'] = $r['neraca'];
						$_SESSION['level'] = $r['akses_level'];
						$_SESSION['komisi'] = $r['komisi'];
						$_SESSION['kartustok'] = $r['kartustok'];
						$_SESSION['catatan'] = $r['catatan'];
						$_SESSION['cekdarah'] = $r['cekdarah'];
						$_SESSION['jurnalkas'] = $r['jurnalkas'];
					}
				}
			}
		}
		header('location:../../media_admin.php?module=' . $module);
	}
	//Hapus
	elseif ($module == 'admin' and $act == 'hapus') {

		$stmt = $db->prepare("DELETE FROM admin WHERE id_admin = ?");
		$stmt->execute([$_GET['id']]);
		header('location:../../media_admin.php?module=' . $module);
	}
}
