<?php
include_once '../../../configurasi/koneksi.php';
$aksi = "modul/mod_orders/aksi_orders.php";
if ($_GET['action'] == "table_data") {
    $columns = array(
        0 => 'orders.id_trbmasuk',
        1 => 'orders.petugas',
        2 => 'orders.kd_trbmasuk',
        3 => 'orders.tgl_trbmasuk',
        4 => 'orders.nm_supplier',
        5 => 'orders.ket_trbmasuk',
        6 => 'orders.ttl_trbmasuk',
        7 => 'orders.dp_bayar',
        8 => 'orders.sisa_bayar',
        9 => 'orders.id_trbmasuk'
    );

    $querycount = $db->prepare("SELECT count(id_trbmasuk) as jumlah FROM orders WHERE id_resto = 'pesan'");
    $querycount->execute();
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalData = $datacount['jumlah'];

    $totalFiltered = $totalData;

    $limit = $_POST['length'];
    $start = $_POST['start'];
    $order = $columns[$_POST['order']['0']['column']];
    $dir = $_POST['order']['0']['dir'];

    if (empty($_POST['search']['value'])) {
        // $query = $db->query("SELECT *
        //     FROM orders
        //     JOIN ordersdetail ON orders.kd_trbmasuk = ordersdetail.kd_trbmasuk 
        //     WHERE orders.id_resto = 'pesan'
        //     AND orders.kd_trbmasuk NOT IN (SELECT kd_orders FROM trx_orders)
        //     GROUP BY ordersdetail.kd_trbmasuk
        //     ORDER BY $order DESC LIMIT $limit OFFSET $start");
        
        // $query = $db->prepare("SELECT * FROM orders
        //             WHERE id_resto = 'pesan'
        //             AND kd_trbmasuk NOT IN (SELECT kd_orders FROM trx_orders)
        //             ORDER BY $order DESC LIMIT $limit OFFSET $start");
        // $query = $db->prepare("SELECT * FROM orders
        //                         INNER JOIN ordersdetail ON ordersdetail.kd_trbmasuk = orders.kd_trbmasuk
        //                         WHERE orders.id_resto = 'pesan'
        //                         ORDER BY $order DESC LIMIT $limit OFFSET $start");
        $query = $db->prepare("SELECT orders.* 
                                FROM orders
                                WHERE orders.id_resto = 'pesan'
                                ORDER BY $order DESC LIMIT $limit OFFSET $start");
                    
    } else {
        $search = $_POST['search']['value'];
        
        // $query = $db->prepare("SELECT * FROM orders
        //             WHERE id_resto = 'pesan'
        //             AND kd_trbmasuk NOT IN (SELECT kd_orders FROM trx_orders)
        //             AND kd_trbmasuk LIKE '%$search%'
        //             OR tgl_trbmasuk LIKE '%$search%'
        //             OR nm_supplier  LIKE '%$search%'
        //             OR ket_trbmasuk LIKE '%$search%'
        //             OR ttl_trbmasuk LIKE '%$search%'
        //             OR dp_bayar     LIKE '%$search%'
        //             OR sisa_bayar   LIKE '%$search%'
        //             ORDER BY $order DESC LIMIT $limit OFFSET $start");

       
        // $querycount = $db->prepare("SELECT count(id_trbmasuk) as jumlah FROM orders
        //                 WHERE id_resto = 'pesan'
        //                 AND kd_trbmasuk NOT IN (SELECT kd_orders FROM trx_orders)
        //                 AND kd_trbmasuk LIKE '%$search%'
        //                 OR tgl_trbmasuk LIKE '%$search%'
        //                 OR nm_supplier  LIKE '%$search%'
        //                 OR ket_trbmasuk LIKE '%$search%'
        //                 OR ttl_trbmasuk LIKE '%$search%'
        //                 OR dp_bayar     LIKE '%$search%'
        //                 OR sisa_bayar   LIKE '%$search%'");
        // $query = $db->prepare("SELECT * FROM orders
        //                         INNER JOIN ordersdetail ON ordersdetail.kd_trbmasuk = orders.kd_trbmasuk
        //                         WHERE orders.id_resto = 'pesan'
        //                         AND (orders.kd_trbmasuk LIKE '%$search%'
        //                         OR orders.tgl_trbmasuk LIKE '%$search%'
        //                         OR orders.nm_supplier  LIKE '%$search%'
        //                         OR orders.ket_trbmasuk LIKE '%$search%'
        //                         OR orders.ttl_trbmasuk LIKE '%$search%'
        //                         OR orders.dp_bayar     LIKE '%$search%'
        //                         OR orders.sisa_bayar   LIKE '%$search%')
        //                         ORDER BY $order DESC LIMIT $limit OFFSET $start");
        $query = $db->prepare("SELECT orders.* 
                                FROM orders
                                WHERE orders.id_resto = 'pesan'
                                AND (orders.kd_trbmasuk LIKE '%$search%'
                                OR orders.tgl_trbmasuk LIKE '%$search%'
                                OR orders.nm_supplier  LIKE '%$search%'
                                OR orders.ket_trbmasuk LIKE '%$search%'
                                OR orders.ttl_trbmasuk LIKE '%$search%'
                                OR orders.dp_bayar     LIKE '%$search%'
                                OR orders.sisa_bayar   LIKE '%$search%')
                                ORDER BY $order DESC LIMIT $limit OFFSET $start");

       
        // $querycount = $db->prepare("SELECT count(id_trbmasuk) as jumlah FROM orders
        //                         INNER JOIN ordersdetail ON ordersdetail.kd_trbmasuk = orders.kd_trbmasuk
        //                         WHERE orders.id_resto = 'pesan'
        //                         AND (orders.kd_trbmasuk LIKE '%$search%'
        //                         OR orders.tgl_trbmasuk LIKE '%$search%'
        //                         OR orders.nm_supplier  LIKE '%$search%'
        //                         OR orders.ket_trbmasuk LIKE '%$search%'
        //                         OR orders.ttl_trbmasuk LIKE '%$search%'
        //                         OR orders.dp_bayar     LIKE '%$search%'
        //                         OR orders.sisa_bayar   LIKE '%$search%')");
        $querycount = $db->prepare("SELECT count(orders.id_trbmasuk) as jumlah FROM orders
                                WHERE orders.id_resto = 'pesan'
                                AND (orders.kd_trbmasuk LIKE '%$search%'
                                OR orders.tgl_trbmasuk LIKE '%$search%'
                                OR orders.nm_supplier  LIKE '%$search%'
                                OR orders.ket_trbmasuk LIKE '%$search%'
                                OR orders.ttl_trbmasuk LIKE '%$search%'
                                OR orders.dp_bayar     LIKE '%$search%'
                                OR orders.sisa_bayar   LIKE '%$search%')");
        
        $querycount->execute();
        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();
    // $totalNilaiBarang = 0;
    if (!empty($query)) {
        $no = $start + 1;
        $query->execute();
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            // for column
            
            $nestedData['no']           = $no;
            $nestedData['petugas']      = $value['petugas'];
            $nestedData['kd_trbmasuk']  = $value['kd_trbmasuk'];
            $nestedData['tgl_trbmasuk'] = $value['tgl_trbmasuk'];
            $nestedData['nm_supplier']  = $value['nm_supplier'];
            $nestedData['ket_trbmasuk'] = $value['ket_trbmasuk'];
            $nestedData['ttl_trbmasuk'] = $value['ttl_trbmasuk'];
            $nestedData['dp_bayar']     = $value['dp_bayar'];
            $nestedData['sisa_bayar']   = $value['sisa_bayar'];
            $nestedData['masuk']        = $value['masuk'];
            // $nestedData['aksi'] = "<a href='?module=orders&act=ubah&id=$value[id_trbmasuk]' title='EDIT' class='btn btn-warning btn-xs'>EDIT</a> 
            // <a href=javascript:confirmdelete('$aksi?module=orders&act=hapus&id=$value[id_trbmasuk]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
            // <BR>
            // <a href='modul/mod_orders/tampil_orders.php?id=$value[kd_trbmasuk]' target='_blank' title='EDIT' class='btn btn-primary btn-xs'>REGULER&nbsp;<i class='glyphicon glyphicon-print'></i>&nbsp;</a><BR>
            // <a href='modul/mod_orders/tampil_prekursor.php?id=$value[kd_trbmasuk]' target='_blank' title='EDIT' class='btn btn-pinterest btn-xs'>PREKURSOR&nbsp;<i class='glyphicon glyphicon-print'></i>&nbsp;</a><BR>
            // <a href='modul/mod_orders/tampil_oot.php?id=$value[kd_trbmasuk]' target='_blank' title='EDIT' class='btn btn-success btn-xs'>OOT&nbsp;<i class='glyphicon glyphicon-print'></i>&nbsp;</a>
            // ";
            $nestedData['aksi'] = "<a href='?module=trbmasukpbf&act=orders_detail&id=$value[id_trbmasuk]' title='EDIT' class='btn btn-warning btn-xs'>TERIMA</a>";
            $data[] = $nestedData;
            $no++;
        }
    }


    $json_data = [
        "draw"              => intval($_POST['draw']),
        "recordsTotal"      => intval($totalData),
        "recordsFiltered"   => intval($totalFiltered),
        "data"              => $data
    ];

    echo json_encode($json_data);
}
