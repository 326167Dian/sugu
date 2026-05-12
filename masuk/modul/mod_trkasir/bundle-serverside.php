<?php
include_once '../../../configurasi/koneksi.php';
include_once '../../../configurasi/fungsi_rupiah.php';

if ($_GET['action'] == "table_data") {

    $columns = array(
        0 => 'id_bundle',
        1 => 'kd_bundle',
        2 => 'nm_bundle',
        3 => 'qty_bundle',
        4 => 'sat_bundle',
        5 => 'hrgjual_bundle',
        6 => 'id_bundle'
    );

    $querycount = $db->prepare("SELECT count(id_bundle) as jumlah FROM bundle");
    $querycount->execute();
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalData = $datacount['jumlah'];

    $totalFiltered = $totalData;

    $limit = $_POST['length'];
    $start = $_POST['start'];
    $order = $columns[$_POST['order']['0']['column']];
    $dir = $_POST['order']['0']['dir'];

    if (empty($_POST['search']['value'])) {
        $query = $db->prepare("SELECT id_bundle,
                                    kd_bundle,
                                    nm_bundle,
                                    qty_bundle,
                                    sat_bundle,
                                    hrgjual_bundle
            FROM bundle ORDER BY $order $dir LIMIT $limit OFFSET $start");
    } else {
        $search = $_POST['search']['value'];
        $query = $db->prepare("SELECT SELECT id_bundle,
                                    kd_bundle,
                                    nm_bundle,
                                    qty_bundle,
                                    sat_bundle,
                                    hrgjual_bundle 
            FROM bundle WHERE kd_bundle LIKE '%$search%' 
                        OR nm_bundle LIKE '%$search%'
                        OR qty_bundle LIKE '%$search%'
                        OR sat_bundle LIKE '%$search%'
                        OR hrgjual_bundle LIKE '%$search%' 
            ORDER BY $order $dir LIMIT $limit OFFSET $start");

        $querycount = $db->prepare("SELECT count(id_bundle) as jumlah 
            FROM barang WHERE kd_bundle LIKE '%$search%' 
                        OR nm_bundle LIKE '%$search%'
                        OR qty_bundle LIKE '%$search%'
                        OR sat_bundle LIKE '%$search%'
                        OR hrgjual_bundle LIKE '%$search%'");

        $querycount->execute();
        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();
    if (!empty($query)) {
        $no = $start + 1;
        $query->execute();
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            $nestedData['no']               = $no;
            $nestedData['kd_bundle']        = $value['kd_bundle'];
            $nestedData['nm_bundle']        = $value['nm_bundle'];
            $nestedData['qty_bundle']       = $value['qty_bundle'];
            $nestedData['sat_bundle']       = $value['sat_bundle'];
            $nestedData['hrgjual_bundle']   = $value['hrgjual_bundle'];
            
            $nestedData['pilih']            = "<button class='btn btn-xs btn-info' id='pilihbundle' 
                                                data-id_bundle='$value[id_bundle]'
                                                data-kd_bundle='$value[kd_bundle]'
                                                data-nm_bundle='$value[nm_bundle]'
                                                data-qty_bundle='$value[qty_bundle]'
                                                data-sat_bundle='$value[sat_bundle]'
                                                data-hrgjual_bundle='$value[hrgjual_bundle]'>
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
