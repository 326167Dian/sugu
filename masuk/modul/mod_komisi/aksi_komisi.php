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
    if ($module=='komisi' AND $act=='input_komisi'){
        // echo $_POST['barang'];
        // die();
        if(strtolower($_POST['barang']) == 'all'){
            if($_POST['metode'] == 'nominal'){
                
                $stmt = $db->prepare("UPDATE barang SET komisi = ?");
                $stmt->execute([$_POST['komisi']]);
            }else{
                $stmt = $db->prepare("UPDATE barang SET komisi = ROUND((hrgsat_barang*?)/100,0)");
                $stmt->execute([$_POST['komisi']]);
            }
        } else {
            if($_POST['metode'] == 'nominal'){
            
                $stmt = $db->prepare("UPDATE barang SET komisi = ? WHERE nm_barang = ?");
                $stmt->execute([$_POST['komisi'], $_POST['nm_barang']]);
            }else{
                $stmt = $db->prepare("UPDATE barang SET komisi = ROUND((hrgsat_barang*?)/100,0) WHERE nm_barang = ?");
                $stmt->execute([$_POST['komisi'], $_POST['nm_barang']]);
                
            }							
        }
        //echo "<script type='text/javascript'>alert('Data berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
        header('location:../../media_admin.php?module='.$module);

    }
    elseif ($module=='komisi' AND $act=='input_komisiglobal'){
        $tgl_awal = date('Y-m-d');
        $bulan = substr($tgl_awal,5,2);
        $petugas = $_SESSION['namalengkap'];
        //ambil data table komisiglobal
        $stat = $db->query("select * from komisiglobal where month(tgl)='$bulan' ");
        $stat2 = $stat->rowCount();
        if($stat2>0) {
            $stmt = $db->prepare("update komisiglobal set
                                         nilai = ?,
                                         tgl = ?,
										 petugas = ?,
										 status = ?
										 WHERE status='ON'");
            $stmt->execute([$_POST['nilai'], $tgl_awal, $petugas, $_POST['status']]);
            header('location:../../media_admin.php?module=' . $module . '&act=global');
        }
        else {
            $stmt = $db->prepare("insert into komisiglobal 
                                        ( 
                                        nilai,
                                        tgl,
                                        petugas,
                                        status
                                        )
                                        VALUES (?, ?, ?, ?)");
            $stmt->execute([$_POST['nilai'], $tgl_awal, $petugas, $_POST['status']]);
            $stmt = $db->prepare("update komisiglobal set status='OFF' WHERE month(tgl) < ?");
            $stmt->execute([$bulan]);
            header('location:../../media_admin.php?module=' . $module . '&act=global');
        }}
     //update barang
    elseif ($module=='komisi' AND $act=='update_komisi'){
    
        if($_POST['metode'] == 'nominal'){
            
                $stmt = $db->prepare("UPDATE barang SET komisi = ? WHERE id_barang = ?");
                $stmt->execute([$_POST['komisi'], $_POST['barang']]);
    									
        }else{
                $stmt = $db->prepare("UPDATE barang SET komisi = ROUND((hrgsat_barang*?)/100,0) WHERE id_barang = ?");
                $stmt->execute([$_POST['komisi'], $_POST['barang']]);
                
        }
         
    												
    	//echo "<script type='text/javascript'>alert('Data berhasil diubah !');window.location='../../media_admin.php?module=".$module."'</script>";
    	header('location:../../media_admin.php?module='.$module);
    	
    }
    //Hapus Proyek
    elseif ($module=='komisi' AND $act=='hapus'){
        
        if($_GET['id']=='all'){
            
            $stmt = $db->prepare("UPDATE barang SET komisi = 0");
            $stmt->execute();
            	
        } else {
            
            $stmt = $db->prepare("UPDATE barang SET komisi = 0 WHERE id_barang = ?");
            $stmt->execute([$_GET['id']]);
            	
        }
        //echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module."'</script>";
        header('location:../../media_admin.php?module='.$module);
    }
    //Close komisi
    elseif ($module=='komisi' AND $act=='close'){
        
        $stmt = $db->prepare("UPDATE komisi_pegawai SET status_komisi = 'closed' WHERE status_komisi = 'on' AND id_admin = ?");
        $stmt->execute([$_GET['id']]);
            
        //echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module."'</script>";
        header('location:../../media_admin.php?module='.$module.'&act=tutupkomisi');
    }

}
?>
