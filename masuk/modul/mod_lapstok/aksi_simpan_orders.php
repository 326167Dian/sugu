<?php

session_start();
include "../../../configurasi/koneksi.php";

$module         = $_GET['module'];
$act            = $_GET['act'];
$count          = $_POST['check'];
$id_supplier    = $_POST['id_supplier'];


//cek apakah ada kode transaksi ON berdasarkan user
$stmt = $db->prepare("SELECT * FROM kdbm WHERE id_admin=? AND id_resto='pesan' AND stt_kdbm='ON' AND kd_trbmasuk LIKE '%ORD%'");
$stmt->execute([$_SESSION['idadmin']]);
$ketemucekkd = $stmt->rowCount();
$hcekkd = $stmt->fetch(PDO::FETCH_ASSOC);


if ($ketemucekkd > 0) {
    $kdtransaksi = $hcekkd['kd_trbmasuk'];
} else {
    $kdunik = date('dmyhis');
	$kdtransaksi = "ORD-" . $kdunik;
	
	$stmt2 = $db->prepare("SELECT * FROM kdbm WHERE kd_trbmasuk=?");
    $stmt2->execute([$kdtransaksi]);
    $ketemucekkd2 = $stmt2->rowCount();
    
    if ($ketemucekkd2 > 0) {
        $kdunik = date('dmyhis')+1;
	    $kdtransaksi = "ORD-" . $kdunik;
    }
	$db->prepare("INSERT INTO kdbm(kd_trbmasuk,id_resto,id_admin) VALUES(?,?,?)")->execute([$kdtransaksi,'pesan',$_SESSION['idadmin']]);
}

$tglharini = date('Y-m-d');
$ttl_trkasir = 0;

$data = array();
for ($i = 0; $i < count($count); $i++) {
    // echo $count[$i] . '<br>';
    
    $stmt_brg = $db->prepare("SELECT * FROM barang WHERE kd_barang=?");
    $stmt_brg->execute([$count[$i]]);
    $brg = $stmt_brg->fetch(PDO::FETCH_ASSOC);
    
    $id_barang      = $brg['id_barang'];
    $kd_barang      = $brg['kd_barang'];
    $nm_barang      = $brg['nm_barang'];
    $qty_retail     = $brg['t30'] - $brg['stok_barang'];
    $sat_barang     = $brg['sat_barang'];
    $konversi       = $brg['konversi'];
    
    if((float)$konversi <= 0){
        echo "
        <script>
            alert('Konversi harus lebih besar dari 0');
            history.back();
        </script>";
        exit;
    }
    
    $qty_grosir     = $qty_retail / $konversi;
    $sat_grosir     = $brg['sat_grosir'];
    $hna            = $brg['hna'];
    $hrgsat_barang  = $brg['hrgsat_barang'];
    $hrgjual_barang = $brg['hrgjual_barang'];
    $ttl_harga      = $hrgsat_barang * $qty_retail;
    $ttl_trkasir    = $ttl_trkasir + $ttl_harga;
    
    // echo    'ID Barang          = '.$id_barang.'<br>'.
    //         'KD Barang          = '.$kd_barang.'<br>'.
    //         'Nama Barang        = '.$nm_barang.'<br>'.
    //         'Quantity Retail    = '.$qty_retail.'<br>'.
    //         'Satuan Barang      = '.$sat_barang.'<br>'.
    //         'Konversi           = '.$konversi.'<br>'.
    //         'Quantity Grosir    = '.$qty_grosir.'<br>'.
    //         'Satuan Grosir      = '.$sat_grosir.'<br>'.
    //         'HNA                = '.$hna.'<br>'.
    //         'Harga Satuan       = '.$hrgsat_barang.'<br>'.
    //         'Harga Jual         = '.$hrgjual_barang.'<br>'.
    //         'Total Harga        = '.$ttl_harga.'<br>'.
    //         'Total Kasir        = '.$ttl_trkasir.'<br>';

    
    $data[] = array(
                $kdtransaksi,
				$id_barang,
				$kd_barang,
				$nm_barang,
				$qty_retail,
				$sat_barang,
				$hrgsat_barang,
				$hrgjual_barang,
				$hna,
				$ttl_harga,
				$konversi,
				$sat_grosir,
				$qty_grosir
            );            

}

$insert_order = $db->prepare("INSERT INTO ordersdetail(kd_trbmasuk,
									id_barang,
									kd_barang,
									nmbrg_dtrbmasuk,
									qty_dtrbmasuk,
									sat_dtrbmasuk,
									hrgsat_dtrbmasuk,
									hrgjual_dtrbmasuk,
									hnasat_dtrbmasuk,
									hrgttl_dtrbmasuk,
									konversi,
									satgrosir_dtrbmasuk,
									qtygrosir_dtrbmasuk)
							  VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
foreach ($data as $row) {
    $insert_order->execute($row);
}

header('location:../../media_admin.php?module=stok_kritis&act=orders&id='.$kdtransaksi);
