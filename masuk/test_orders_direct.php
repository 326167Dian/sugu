<?php
include "../configurasi/koneksi.php";

// Simulate DataTable request
$_POST['draw'] = 1;
$_POST['length'] = 10;
$_POST['start'] = 0;
$_POST['search']['value'] = '';
$_POST['order'][0]['column'] = 0;
$_POST['order'][0]['dir'] = 'DESC';

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

$query = $db->prepare("SELECT * FROM orders
                        WHERE orders.id_resto = 'pesan'
                        ORDER BY $order DESC LIMIT $limit OFFSET $start");

$data = array();
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
    $nestedData['masuk']        = $value['masuk'] ?? 'N/A';
    $nestedData['aksi'] = "<a href='?module=trbmasukpbf&act=orders_detail&id=$value[id_trbmasuk]' title='EDIT' class='btn btn-warning btn-xs'>TERIMA</a>";
    $data[] = $nestedData;
    $no++;
}

$json_data = [
    "draw"              => intval($_POST['draw']),
    "recordsTotal"      => intval($totalData),
    "recordsFiltered"   => intval($totalFiltered),
    "data"              => $data
];

header('Content-Type: application/json');
echo json_encode($json_data);
?>
