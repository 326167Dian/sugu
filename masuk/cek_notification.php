<?php
include "../configurasi/koneksi.php";

$act = $_GET['act'];
if ($act == 'icon') {
    // code...
    // $cek = $db->query(
    //     "SELECT * FROM order_online WHERE kode_pesanan IS NOT NULL AND (status != 'selesai' OR status != 'SELESAI' OR status != 'Selesai')"
    //   );
    $cek = $db->query(
        "SELECT * FROM order_online WHERE kode_pesanan IS NOT NULL AND status != 'Selesai' AND status != 'Dibatalkan'"
      );
    
    $count = $cek->rowCount();
    echo $count;

    
} elseif ($act == 'data') {
    // code..
    // $data = $db->query(
    //     "SELECT * FROM order_online WHERE kode_pesanan IS NOT NULL AND (status != 'selesai' OR status != 'SELESAI')"
    //   );
    $data = $db->query(
        "SELECT * FROM order_online WHERE kode_pesanan IS NOT NULL AND status != 'Selesai' AND status != 'Dibatalkan'"
      );
    
    $json = [];
    while($re = $data->fetch(PDO::FETCH_ASSOC)){
        // $json[] = $re['kode_pesanan'];
        $json[] = array(
                'kode'=> $re['kode_pesanan'],
			    'date'=> $re['created_at']
			);
    }
    echo json_encode($json);
    
}

?>