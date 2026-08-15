<?php
include_once '../../../configurasi/koneksi.php';

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
        $query = $db->prepare("SELECT orders.*
                                FROM orders
                                WHERE orders.id_resto = 'pesan'
                                ORDER BY $order DESC LIMIT $limit OFFSET $start");
    } else {
        $search = $_POST['search']['value'];

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

    $stmt_header = $db->prepare("SELECT COUNT(*) as jumlah FROM trbmasuk WHERE kd_orders = ? AND jenis = 'nonpbf'");
    $stmt_pending = $db->prepare("SELECT COUNT(*) as jumlah FROM ordersdetail WHERE kd_trbmasuk = ? AND masuk = '1'");

    $data = array();
    if (!empty($query)) {
        $no = $start + 1;
        $query->execute();
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
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

            // Selesai = SIMPAN TRANSAKSI sudah pernah diklik (header trbmasuk ada) DAN semua item sudah diterima
            $stmt_header->execute([$value['kd_trbmasuk']]);
            $adaHeader = $stmt_header->fetch(PDO::FETCH_ASSOC)['jumlah'] > 0;

            $stmt_pending->execute([$value['kd_trbmasuk']]);
            $adaPending = $stmt_pending->fetch(PDO::FETCH_ASSOC)['jumlah'] > 0;

            if ($adaHeader && !$adaPending) {
                $nestedData['aksi'] = "<a href='?module=trbmasuk&act=orders_detail&id=$value[id_trbmasuk]' title='SELESAI' class='btn btn-success btn-xs'>SELESAI</a>";
            } else {
                $nestedData['aksi'] = "<a href='?module=trbmasuk&act=orders_detail&id=$value[id_trbmasuk]' title='TERIMA' class='btn btn-warning btn-xs'>TERIMA</a>";
            }

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
