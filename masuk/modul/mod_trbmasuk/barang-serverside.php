<?php
include_once '../../../configurasi/koneksi.php';

if ($_GET['action'] == "table_data") {

    $columns = array(
        0 => 'id_barang',
        1 => 'kd_barang',
        2 => 'nm_barang',
        3 => 'stok_barang',
        4 => 'sat_barang',
        5 => 'hrgsat_barang',
        6 => 'hrgjual_barang',
        7 => 'id_barang'
    );

    $querycount = $db->prepare("SELECT count(id_barang) as jumlah FROM barang");
    $querycount->execute();
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalData = $datacount['jumlah'];

    $totalFiltered = $totalData;

    $limit = $_POST['length'];
    $start = $_POST['start'];
    $order = $columns[$_POST['order']['0']['column']];
    $dir = $_POST['order']['0']['dir'];

    if (empty($_POST['search']['value'])) {
        $query = $db->query("SELECT *
            FROM barang ORDER BY $order $dir LIMIT $limit OFFSET $start");
    } else {
        $search = $_POST['search']['value'];
        $query = $db->query("SELECT *
            FROM barang WHERE kd_barang LIKE '%$search%' 
                        OR nm_barang LIKE '%$search%'
                        OR stok_barang LIKE '%$search%'
                        OR sat_barang LIKE '%$search%'
                        OR hrgsat_barang LIKE '%$search%'
                        OR hrgjual_barang LIKE '%$search%'
            ORDER BY $order $dir LIMIT $limit OFFSET $start");

        $querycount = $db->query("SELECT count(id_barang) as jumlah 
            FROM barang WHERE kd_barang LIKE '%$search%' 
                        OR nm_barang LIKE '%$search%'
                        OR stok_barang LIKE '%$search%'
                        OR sat_barang LIKE '%$search%'
                        OR hrgsat_barang LIKE '%$search%'
                        OR hrgjual_barang LIKE '%$search%'");

        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();
    if (!empty($query)) {
        $no = $start + 1;
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            $nestedData['no'] = $no;
            $nestedData['kd_barang'] = (string)$value['kd_barang'];
            $nestedData['nm_barang'] = $value['nm_barang'];
            $nestedData['stok_barang'] = $value['stok_barang'];
            $nestedData['sat_barang'] = $value['sat_barang'];
            $nestedData['hrgsat_barang'] = $value['hrgsat_barang'];
            $nestedData['hrgjual_barang'] = $value['hrgjual_barang'];
            $nestedData['pilih'] = "<button class='btn btn-xs btn-info' id='pilihbarang' 
            data-id_barang='$value[id_barang]'
            data-kd_barang='$value[kd_barang]'
            data-nm_barang='$value[nm_barang]'
            data-stok_barang='$value[stok_barang]'
            data-sat_barang='$value[sat_barang]'
            data-hrgsat_barang='$value[hrgsat_barang]'
            data-hrgjual_barang='$value[hrgjual_barang]'
            data-hrgjual_barang1='$value[hrgjual_barang1]'
            data-hrgjual_barang2='$value[hrgjual_barang2]'>
            <i class='fa fa-check'></i>
            </button>";
            $data[] = $nestedData;
            $no++;
        }
    }

    $json_data = [
        "draw"            => intval($_POST['draw']),
        "recordsTotal"    => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data"            => $data
    ];

    echo json_encode($json_data);
}
