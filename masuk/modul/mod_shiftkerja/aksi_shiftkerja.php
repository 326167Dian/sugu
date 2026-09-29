<?php
session_start();
 if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
  echo "<link href='style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
  echo "<a href=../../index.php><b>LOGIN</b></a></center>";
}
else{
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_thumb.php";
include "../../../configurasi/library.php";

$module=$_GET['module'];
$act=$_GET['act'];
$waktu = date('y-m-d H:i:s');


// Input admin
if ($module=='shiftkerja' AND $act=='input_shiftkerja'){

    $tglharini = date('Y-m-d');

$stmt = $db->prepare("SELECT * FROM waktukerja WHERE tanggal=? AND status=?");
$stmt->execute([$tglharini, 'ON']);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$ada = count($results);
if ($ada > 0){
echo "<script type='text/javascript'>alert('Kasir sudah dibuka!');history.go(-1);</script>";
}
else{

    $stmt = $db->prepare("INSERT INTO waktukerja (petugasbuka, petugastutup, tanggal, waktubuka, waktututup, shift, saldoawal, saldoakhir, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['petugasbuka'],
        '',
        $_POST['tanggal'],
        $_POST['waktubuka'],
        '00:00:00',
        $_POST['shift'],
        $_POST['saldoawal'],
        0,
        $_POST['status']
    ]);
										
	
                                        
	//echo "<script type='text/javascript'>alert('Data berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
	header('location:../../media_admin.php?module='.$module);

}
}

//updata waktu kerja
 elseif ($module=='shiftkerja' AND $act=='update_waktukerja'){

     $stmt = $db->prepare("UPDATE waktukerja SET petugastutup=?, waktututup=?, status=?, saldoakhir=? WHERE shift=? AND tanggal=?");
     $stmt->execute([$_POST['petugastutup'], $_POST['waktututup'], $_POST['status'], $_POST['saldoakhir'], $_POST['shift'], $_POST['tanggal']]);

    // Catat pendapatan harian shift ini ke jurnal kas, dipisah TUNAI dan TRANSFER
    $bulanindo = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $tsshift = strtotime($_POST['tanggal']);
    $tglindo = (int)date('j', $tsshift) . ' ' . $bulanindo[(int)date('n', $tsshift)] . ' ' . date('Y', $tsshift);

    $namashift = $db->prepare("SELECT nama_shift FROM namashift WHERE shift = ?");
    $namashift->execute([$_POST['shift']]);
    $rshift = $namashift->fetch(PDO::FETCH_ASSOC);
    $namashiftteks = !empty($rshift['nama_shift']) ? $rshift['nama_shift'] : ($_POST['shift'] == 1 ? 'PAGI' : 'SORE');

    $ket = 'shift ' . strtolower($namashiftteks) . ' ' . $tglindo;
    $idjenis_pendapatan = 1; // Pendapatan Harian Apotek
    $curtime = date('ymdHis');

    $carabayarmap = [1 => 'TUNAI', 2 => 'TRANSFER'];
    foreach ($carabayarmap as $id_carabayar => $carabayar) {
        $jual = $db->prepare("SELECT SUM(ttl_trkasir) AS total FROM trkasir WHERE shift = ? AND tgl_trkasir = ? AND id_carabayar = ?");
        $jual->execute([$_POST['shift'], $_POST['tanggal'], $id_carabayar]);
        $rjual = $jual->fetch(PDO::FETCH_ASSOC);
        $total = $rjual['total'] ? $rjual['total'] : 0;

        if ($total > 0) {
            $db->prepare("INSERT INTO jurnal (tanggal, ket, petugas, idjenis, carabayar, debit, kredit, current) VALUES (?, ?, ?, ?, ?, 0, ?, ?)")
               ->execute([date('Y-m-d'), $ket, $_POST['petugastutup'], $idjenis_pendapatan, $carabayar, $total, $curtime]);

            $kas = $db->prepare("SELECT saldo FROM kas WHERE id_kas = ?");
            $kas->execute(['1']);
            $rkas = $kas->fetch(PDO::FETCH_ASSOC);
            $saldobaru = $rkas['saldo'] + $total;
            $db->prepare("UPDATE kas SET saldo = ? WHERE id_kas = ?")->execute([$saldobaru, '1']);
        }
    }

	//echo "<script type='text/javascript'>alert('Data berhasil diubah !');window.location='../../media_admin.php?module=".$module."'</script>";

    header('location:../../media_admin.php?module='.$module);
 }
 //updata waktu kerja
 elseif ($module=='shiftkerja' AND $act=='update_waktukerjakoreksi'){

     $stmt = $db->prepare("UPDATE waktukerja 
                                SET petugasbuka = ?, 
                                    petugastutup = ?, 
                                    shift  = ?,
                                    tanggal = ?,
                                    waktubuka = ?,
                                    waktututup = ?,
                                    saldoawal = ?,
                                    saldoakhir = ?,
                                    status = ?                                   
								WHERE id_shift = ?");
     $stmt->execute([$_POST['petugasbuka'], $_POST['petugastutup'], $_POST['shift'], $_POST['tanggal'], $_POST['waktubuka'], $_POST['waktututup'], $_POST['saldoawal'], $_POST['saldoakhir'], $_POST['status'], $_POST['id']]);

	//echo "<script type='text/javascript'>alert('Data berhasil diubah !');window.location='../../media_admin.php?module=".$module."'</script>";
	header('location:../../media_admin.php?module='.$module);
	
}
//Hapus Proyek
elseif ($module=='shiftkerja' AND $act=='hapus'){

  $stmt = $db->prepare("DELETE FROM waktukerja WHERE id_shift = ?");
  $stmt->execute([$_GET['id']]);
  //echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module."'</script>";
  header('location:../../media_admin.php?module='.$module);
}

}
?>
