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
    $tgl_awal = date('Y-m-d');
    $curtime = date('ymdHis');
    $nama = $_SESSION['namalengkap'];
// Input admin
    if ($module=='jurnalkas' AND $act=='input_jurnal'){

       $db->prepare("INSERT INTO jurnal (
                        tanggal,ket,petugas,idjenis,carabayar,debit,current)
						VALUES(?,?,?,?,?,?,?)")
						->execute([$tgl_awal, $_POST['ket'], $nama, $_POST['idjenis'], $_POST['carabayar'], $_POST['debit'], $curtime]);
						
//update kas debit
        //cek tambah stok
        $kurangkas = $db->prepare("SELECT * FROM kas WHERE id_kas = ?");
        $kurangkas->execute(['1']);
        $kaslama = $kurangkas->fetch(PDO::FETCH_ASSOC);
        $kasdebit = $kaslama['saldo'] - $_POST['debit'] ;

        $db->prepare("UPDATE kas SET saldo = ?
                        WHERE id_kas = ?")->execute([$kasdebit, '1']);
        // if($angka==$ttlqty) {

            //echo "<script type='text/javascript'>alert('Data berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
            header('location:../../media_admin.php?module='.$module);


    }
    elseif ($module=='jurnalkas' AND $act=='input_jurnal2'){
        
        $db->prepare("INSERT INTO jurnal (
                        tanggal,ket,petugas,idjenis,carabayar,kredit,current)
						VALUES(?,?,?,?,?,?,?)")->execute([$tgl_awal, $_POST['ket'], $nama, $_POST['idjenis'], $_POST['carabayar'], $_POST['kredit'], $curtime]);

        //update kas kredit
        //cek tambah stok
        $tambahkas = $db->prepare("SELECT * FROM kas WHERE id_kas = ?");
        $tambahkas->execute(['1']);
        $kaslama = $tambahkas->fetch(PDO::FETCH_ASSOC);
        $kaskredit = $kaslama['saldo'] + $_POST['kredit'];
        
        $db->prepare("UPDATE kas SET saldo = ?
                      WHERE id_kas = ?")->execute([$kaskredit, '1']);
        // if($angka==$ttlqty) {

        //echo "<script type='text/javascript'>alert('Data berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
        header('location:../../media_admin.php?module='.$module);

        //echo "<script type='text/javascript'>alert('Data berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
        // header('location:../../media_admin.php?module='.$module);


    }
    //updata jurnalkas
    elseif ($module=='jurnalkas' AND $act=='update_jurnal'){
        $petugas = $_SESSION['namalengkap'];
        $sesi = $_SESSION['level'];

        $edit = $db->prepare("SELECT * FROM jurnal WHERE id_jurnal = ?");
        $edit->execute([$_POST['id']]);
        $r = $edit->fetch(PDO::FETCH_ASSOC);

        if ( ($petugas == $r['petugas']) or $sesi =='pemilik' )
        {   
            $db->prepare("UPDATE jurnal SET                                       
                                    ket = ?,
                                    idjenis = ?,                                 
                                    carabayar = ?,                                 
                                    debit = ?,
                                    kredit = ?,
                                    current = ?
									WHERE id_jurnal = ?")->execute([$_POST['ket'], $_POST['idjenis'], $_POST['carabayar'], $_POST['debit'], $_POST['kredit'], $curtime, $_POST['id']]);

        //update kas

            $saldo2 = $db->prepare("SELECT SUM(kredit) AS kred, SUM(debit) AS debt FROM jurnal");
            $saldo2->execute();
            $saldo1 = $saldo2->fetch(PDO::FETCH_ASSOC);
            $saldo_akhir = $saldo1['kred'] - $saldo1['debt'];

            $db->prepare("UPDATE kas SET saldo = ?
                    WHERE id_kas = ?")->execute([$saldo_akhir, '1']);

            //echo "<script type='text/javascript'>alert('Data berhasil diubah !');window.location='../../media_admin.php?module=".$module."'</script>";
            header('location:../../media_admin.php?module=' . $module);
            
         }
        else {

              echo "<script type='text/javascript'>alert('Jurnal hanya bisa diedit orang yang sama atau pemilik apotek!');history.go(-1);</script>";
        }
    }
//Hapus Proyek
    elseif ($module=='jurnalkas' AND $act=='hapus'){
        $petugas = $_SESSION['namalengkap'];
        $sesi = $_SESSION['level'];
        $edit = $db->prepare("SELECT * FROM jurnal WHERE id_jurnal = ?");
        $edit->execute([$_GET['id']]);
        $r = $edit->fetch(PDO::FETCH_ASSOC);

        if ( $petugas == $r['petugas'] or $sesi =='pemilik')
        { 
            if($r['debit']>0)
            {
                $tambahkas = $db->prepare("SELECT * FROM kas WHERE id_kas = ?");
                $tambahkas->execute(['1']);
                $kaslama = $tambahkas->fetch(PDO::FETCH_ASSOC);
                $kasdebit = $kaslama['saldo'] + $r['debit'] ;
    
                $db->prepare("UPDATE kas SET saldo = ?
                             WHERE id_kas = ?")->execute([$kasdebit, '1']);
            }
            else
            {
                $kurangkas = $db->prepare("SELECT * FROM kas WHERE id_kas = ?");
                $kurangkas->execute(['1']);
                $kaslama = $kurangkas->fetch(PDO::FETCH_ASSOC);
                $kasdebit = $kaslama['saldo'] - $r['kredit'] ;

                $db->prepare("UPDATE kas SET saldo = ?
                             WHERE id_kas = ?")->execute([$kasdebit, '1']);

            }

            $db->prepare("DELETE FROM jurnal WHERE id_jurnal = ?")->execute([$_GET['id']]);
            //echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module."'</script>";
            header('location:../../media_admin.php?module='.$module);
        }
        else{
            echo "<script type='text/javascript'>alert('Jurnal hanya bisa dihapus orang yang sama atau pemilik apotek!');history.go(-1);</script>";
        }
    }

}
?>
