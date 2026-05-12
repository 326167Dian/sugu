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

// Input admin
    if ($module=='jurnalkas' AND $act=='input_jenistransaksi'){

        $cekganda = $db->prepare("SELECT nm_jurnal FROM jenis_jurnal WHERE nm_jurnal=?");
        $cekganda->execute([$_POST['nm_jurnal']]);
        $ada = $cekganda->rowCount();
        if ($ada > 0){
            echo "<script type='text/javascript'>alert('Jenis Transaksi sudah ada!');history.go(-1);</script>";
        }else{

            $stmt = $db->prepare("INSERT INTO jenis_jurnal (
                                        nm_jurnal,
                                        tipe
                                        )
								 VALUES(?, ?)");
            $stmt->execute([$_POST['nm_jurnal'], $_POST['tipe']]);


            echo "<script type='text/javascript'>alert('Data berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
            header('location:../../media_admin.php?module='.$module .'&act=jenistransaksi');

        }
    }
    //updata jurnalkas
    elseif ($module=='jurnalkas' AND $act=='update_jenistransaksi'){

        $stmt = $db->prepare("UPDATE jenis_jurnal SET   
                                    nm_jurnal = ?
									WHERE idjenis = ?");
        $stmt->execute([$_POST['nm_jurnal'], $_POST['id']]);

        //echo "<script type='text/javascript'>alert('Data berhasil diubah !');window.location='../../media_admin.php?module=".$module."'</script>";
        header('location:../../media_admin.php?module='.$module. '&act=jenistransaksi');

    }
//Hapus Proyek
    elseif ($module=='jurnalkas' AND $act=='hapus'){
        $petugas = $_SESSION['namalengkap'];
        $delete = $db->prepare("SELECT * FROM jenis_jurnal WHERE idjenis=?");
        $delete->execute([$_GET['id']]);
        $r = $delete->fetch(PDO::FETCH_ASSOC);

        if ( $petugas !== $r['petugas'] && $_SESSION['level']!=='pemilik')
        { echo "<script type='text/javascript'>alert('jurnal kas harus dihapus orang yang sama atau pemilik apotek!');history.go(-1);</script>";}
        else{
            $stmt = $db->prepare("DELETE FROM jenis_jurnal WHERE idjenis = ?");
            $stmt->execute([$_GET['id']]);
            //echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module."'</script>";
            header('location:../../media_admin.php?module='.$module.'&act=jenistransaksi');

        }
    }

}
?>
