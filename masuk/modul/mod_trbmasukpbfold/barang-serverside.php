<?php
include_once '../../../configurasi/koneksi.php';

if ($_GET['action'] == "table_data") {

    $columns = array(
        0 => 'id_barang',
        1 => 'kd_barang',
        2 => 'nm_barang',
        3 => 'stok_barang',
        4 => 'sat_grosir',
        5 => 'konversi',
        6 => 'hna',
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
        $query = $db->prepare("SELECT *
            FROM barang ORDER BY $order $dir LIMIT $limit OFFSET $start");
    } else {
        $search = $_POST['search']['value'];
        $query = $db->prepare("SELECT *
            FROM barang WHERE kd_barang LIKE '%$search%' 
                        OR nm_barang LIKE '%$search%'
                        OR stok_barang LIKE '%$search%'
                        OR sat_grosir LIKE '%$search%'
                        OR hrgsat_barang LIKE '%$search%'
                        OR konversi LIKE '%$search%'
                        OR hrgjual_barang LIKE '%$search%'
            ORDER BY $order $dir LIMIT $limit OFFSET $start");

        $querycount = $db->prepare("SELECT count(id_barang) as jumlah 
            FROM barang WHERE kd_barang LIKE '%$search%' 
                        OR nm_barang LIKE '%$search%'
                        OR stok_barang LIKE '%$search%'
                        OR sat_grosir LIKE '%$search%'
                        OR hrgsat_barang LIKE '%$search%'
                        OR konversi LIKE '%$search%'
                        OR hrgjual_barang LIKE '%$search%'");

        $querycount->execute();
        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();
    if (!empty($query)) {
        $no = $start + 1;
        $query->execute();
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            $nestedData['no'] = $no;
            $nestedData['kd_barang'] = $value['kd_barang'];
            $nestedData['nm_barang'] = $value['nm_barang'];
            $nestedData['stok_barang'] = $value['stok_barang'];
            $nestedData['sat_grosir'] = $value['sat_grosir'];
            $nestedData['konversi'] = $value['konversi'];
            $nestedData['hna'] = $value['hna'];
            $nestedData['pilih'] = "<button class='btn btn-xs btn-info' id='pilihbarang' 
            data-id_barang='$value[id_barang]'
            data-kd_barang='$value[kd_barang]'
            data-nm_barang='$value[nm_barang]'
            data-stok_barang='$value[stok_barang]'
            data-sat_grosir='$value[sat_grosir]'
            data-konversi='$value[konversi]'
            data-hna='$value[hna]'
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
