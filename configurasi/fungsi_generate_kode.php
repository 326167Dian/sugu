<?php
    include "koneksi.php";
    
    function get_kode_bundle(){
        global $db;
       
        $year  = date("y", time());
        $month = date("m", time());
        
        $kd_bundle = 'BUND-'.$year.$month;
        $stmt = $db->prepare("SELECT kd_bundle, RIGHT(kd_bundle, 3) AS kode_int FROM bundle WHERE LEFT(kd_bundle, 9) LIKE ? ORDER BY kd_bundle DESC LIMIT 1");
        $stmt->execute(['%'.$kd_bundle.'%']);
        $cek = $stmt->rowCount();
            
        if ($cek > 0) {
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            $kode = $kd_bundle.str_pad($r['kode_int'] + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $kode = $kd_bundle.'001';
        }
        return $kode;
    }
    
    function get_kode_barang(){
        global $db;
       
        $year  = date("Y", time());
        $month = date("m", time());
        
        $kd_barang = $year.$month;
        $stmt = $db->prepare("SELECT kd_barang, RIGHT(kd_barang, 4) AS kode_int FROM barang WHERE LEFT(kd_barang, 6) LIKE ? ORDER BY kd_barang DESC LIMIT 1");
        $stmt->execute(['%'.$kd_barang.'%']);
        $cek = $stmt->rowCount();
        
        if ($cek > 0) {
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            $kode = $kd_barang.str_pad($r['kode_int'] + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $kode = $kd_barang.'0001';
        }
        return $kode;
    }
?>