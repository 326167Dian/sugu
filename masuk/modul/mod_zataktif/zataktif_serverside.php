<?php
session_start();
include_once '../../../configurasi/koneksi.php';

if ($_GET['action'] == "table_data") {

    $columns = array(
        0 => 'id_zataktif',
        1 => 'nm_zataktif',
        2 => 'indikasi',
        3 => 'aturanpakai',
        4 => 'saran',
        5 => 'user',
        6 => 'updated_at',
        7 => 'id_zataktif'
    );

    $aksi = "modul/mod_zataktif/aksi_zataktif.php";

    $querycount = $db->query("SELECT count(id_zataktif) as jumlah FROM zataktif");
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalData = $datacount['jumlah'];
    $totalFiltered = $totalData;

    $limit = intval($_POST['length']);
    $start = intval($_POST['start']);
    $colIndex = intval($_POST['order']['0']['column']);
    $order = isset($columns[$colIndex]) ? $columns[$colIndex] : 'id_zataktif';
    $dir = (isset($_POST['order']['0']['dir']) && strtolower($_POST['order']['0']['dir']) === 'asc') ? 'ASC' : 'DESC';

    if (empty($_POST['search']['value'])) {
        $query = $db->query("SELECT * FROM zataktif ORDER BY id_zataktif DESC LIMIT $limit OFFSET $start ");
    } else {
        $search = $_POST['search']['value'];
        $query = $db->prepare("SELECT * FROM zataktif
            WHERE nm_zataktif LIKE ?
            OR indikasi LIKE ?
            OR aturanpakai LIKE ?
            OR saran LIKE ?
            OR user LIKE ?
            ORDER BY id_zataktif DESC LIMIT $limit OFFSET $start");
        $query->execute(["%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]);

        $querycount = $db->prepare("SELECT count(id_zataktif) as jumlah FROM zataktif
            WHERE nm_zataktif LIKE ?
            OR indikasi LIKE ?
            OR aturanpakai LIKE ?
            OR saran LIKE ?
            OR user LIKE ?");
        $querycount->execute(["%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]);
        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();

    if (!empty($query)) {
        $no = $start + 1;
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            $nestedData['no'] = $no;
            $nestedData['nm_zataktif'] = $value['nm_zataktif'];
            $nestedData['indikasi'] = $value['indikasi'];
            $nestedData['aturanpakai'] = $value['aturanpakai'];
            $nestedData['saran'] = $value['saran'];
            $nestedData['user'] = $value['user'];
            $nestedData['updated_at'] = $value['updated_at'];

            $nestedData['pilih'] = '<div class="dropdown">'
                . '<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action <i class="fa fa-caret-down"></i></button>'
                . '<div class="dropdown-menu">'
                . '<a href="?module=zataktif&act=edit&id=' . $value['id_zataktif'] . '" title="EDIT" class="btn btn-info btn-xs" style="width: 50%; margin:5px 0">EDIT</a> <br>'
                . '<a href="javascript:confirmdelete(\'' . $aksi . '?module=zataktif&act=hapus&id=' . $value['id_zataktif'] . '\')" title="HAPUS" class="btn btn-danger btn-xs" style="width:50%; margin:5px 0">HAPUS</a>'
                . '</div>'
                . '</div>';

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
