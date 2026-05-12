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

	//echo "<script type='text/javascript'>alert('Data berhasil diubah !');window.location='../../media_admin.php?module=".$module."'</script>";

// if($_POST['shift']=1)
//  { $idtx = 1;}
// elseif($_POST['shift']=2) 
//  { $idtx = 2;}
 
//     mysqli_query($GLOBALS["___mysqli_ston"],"insert into jurnal (
//         tanggal,
//         ket,
//         petugas,
//         idjenis,
//         debit,
//         kredit,
//         carabayar,
//         current
//         )
// values( '$_POST[tanggal]',
//         'Tutup Kasir Shift $_POST[shift]',
//         '$_POST[petugastutup]',
//         '$idtx',
//         '0',
//         '$_POST[saldoakhir]',
//         'TUNAI',
//         '$waktu'
//         )
//         ");


//     $transfer = $db->query("select sum(ttl_trkasir) as tf from trkasir 
//                 where shift='$_POST[shift]' and tgl_trkasir='$_POST[tanggal]' and id_carabayar='2' ");   
//     $tfr = $transfer->fetch_array();
//     mysqli_query($GLOBALS["___mysqli_ston"],"insert into jurnal (
//         tanggal,
//         ket,
//         petugas,
//         idjenis,
//         debit,
//         kredit,
//         carabayar,
//         current
//         )
// values( '$_POST[tanggal]',
//         'Tutup Kasir Shift $_POST[shift]',
//         '$_POST[petugastutup]',
//         '$idtx',
//         '0',
//         '$tfr[tf]',
//         'TRANSFER',
//         '$waktu'
//         )
//         ");

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
