<?php
include_once '../../../configurasi/koneksi.php';

if ($_GET['action'] == "table_data") {

    $columns = array(
        0 => 'id_brgsup',
        1 => 'kd_barang',
        2 => 'nm_barang',
        3 => 'stok_barang',
        4 => 'sat_barang',
        5 => 'hrgsat_brgsupplier',
        6 => 'id_brgsup'
    );

    $querycount = $db->query("SELECT count(id_brgsup) as jumlah FROM barang_supplier WHERE id_supplier = '$_GET[id]'");
    $datacount = $querycount->fetch_array();

    $totalData = $datacount['jumlah'];

    $totalFiltered = $totalData;

    $limit = $_POST['length'];
    $start = $_POST['start'];
    $order = $columns[$_POST['order']['0']['column']];
    $dir = $_POST['order']['0']['dir'];

    if (empty($_POST['search']['value'])) {
        $query = $db->query("SELECT *
            FROM barang_supplier a 
            JOIN barang b ON a.id_barang = b.id_barang
            WHERE a.id_supplier = '$_GET[id]' 
            ORDER BY $order $dir LIMIT $limit OFFSET $start");
    } else {
        $search = $_POST['search']['value'];
        $query = $db->query("SELECT * 
            FROM barang_supplier a 
            JOIN barang b ON a.id_barang = b.id_barang
            WHERE a.id_supplier = '$_GET[id]'
                        AND b.kd_barang LIKE '%$search%' 
                        OR b.nm_barang LIKE '%$search%'
                        OR b.stok_barang LIKE '%$search%'
                        OR b.sat_barang LIKE '%$search%'
                        OR a.hrgsat_brgsupplier LIKE '%$search%'
            ORDER BY $order $dir LIMIT $limit OFFSET $start");

        $querycount = $db->query("SELECT count(id_barang) as jumlah 
            FROM barang_supplier a 
            JOIN barang b ON a.id_barang = b.id_barang
            WHERE a.id_supplier = '$_GET[id]'
                        AND b.kd_barang LIKE '%$search%' 
                        OR b.nm_barang LIKE '%$search%'
                        OR b.stok_barang LIKE '%$search%'
                        OR b.sat_barang LIKE '%$search%'
                        OR a.hrgsat_brgsupplier LIKE '%$search%'");

        $datacount = $querycount->fetch_array();
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();
    // $totalNilaiBarang = 0;
    if (!empty($query)) {
        $no = $start + 1;
        while ($value = $query->fetch_array()) {

            // for column
            $nestedData['no'] = $no;
            $nestedData['kd_barang'] = $value['kd_barang'];
            $nestedData['nm_barang'] = $value['nm_barang'];
            $nestedData['stok_barang'] = $value['stok_barang'];
            $nestedData['sat_barang'] = $value['sat_barang'];
            $nestedData['hrgsat_brgsupplier'] = $value['hrgsat_brgsupplier'];
            $nestedData['pilih'] = "<button class='btn btn-xs btn-info' id='pilihbarang' 
            data-id_barang='$value[id_barang]'
            data-kd_barang='$value[kd_barang]'
            data-nm_barang='$value[nm_barang]'
            data-stok_barang='$value[stok_barang]'
            data-sat_barang='$value[sat_barang]'
            data-hrgsat_barang='$value[hrgsat_brgsupplier]'>
            <i class='fa fa-check'></i>
            </button>";
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
