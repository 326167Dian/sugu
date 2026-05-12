<?php
session_start();
include_once '../../../configurasi/koneksi.php';

$aksi = "modul/mod_konseling/aksi_konseling.php";

if ($_GET['action'] == "table_data") {
    $columns = array(
        0 => 'id_konseling',
        1 => 'tgl_konseling',
        2 => 'nm_pelanggan',
        3 => 'nama_dokter',
        4 => 'diagnosa',
        5 => 'tindakan',
        6 => 'updated_at',
        7 => 'id_konseling'
    );

    $querycount = $db->query("SELECT count(id_konseling) as jumlah FROM konseling");
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalData = $datacount['jumlah'];
    $totalFiltered = $totalData;

    $limit = intval($_POST['length']);
    $start = intval($_POST['start']);
    $colIndex = isset($_POST['order']['0']['column']) ? intval($_POST['order']['0']['column']) : 0;
    $order = isset($columns[$colIndex]) ? $columns[$colIndex] : 'id_konseling';
    $dir = (isset($_POST['order']['0']['dir']) && strtolower($_POST['order']['0']['dir']) === 'asc') ? 'ASC' : 'DESC';

    if (empty($_POST['search']['value'])) {
        $query = $db->prepare("SELECT * FROM konseling ORDER BY $order $dir LIMIT $limit OFFSET $start");
        $query->execute();
    } else {
        $search = $_POST['search']['value'];
        $like = "%" . $search . "%";
        $query = $db->prepare("SELECT * FROM konseling WHERE nm_pelanggan LIKE ? OR nama_dokter LIKE ? OR diagnosa LIKE ? OR keluhan LIKE ? OR tindakan LIKE ? ORDER BY $order $dir LIMIT $limit OFFSET $start");
        $query->execute([$like, $like, $like, $like, $like]);

        $querycount = $db->prepare("SELECT count(id_konseling) as jumlah FROM konseling WHERE nm_pelanggan LIKE ? OR nama_dokter LIKE ? OR diagnosa LIKE ? OR keluhan LIKE ? OR tindakan LIKE ?");
        $querycount->execute([$like, $like, $like, $like, $like]);
        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();
    if (!empty($query)) {
        $no = $start + 1;
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            $nestedData = array();
            $nestedData['no'] = $no;
            $nestedData['tgl_konseling'] = $value['tgl_konseling'];
            $nestedData['nm_pelanggan'] = htmlspecialchars($value['nm_pelanggan']);
            $nestedData['nama_dokter'] = htmlspecialchars($value['nama_dokter']);
            $nestedData['diagnosa'] = htmlspecialchars($value['diagnosa']);
            $nestedData['tindakan'] = htmlspecialchars($value['tindakan']);
            $nestedData['updated_at'] = $value['updated_at'];

            $nestedData['aksi'] = "<a href='?module=konseling&act=edit&id={$value['id_konseling']}' title='EDIT' class='btn btn-purple btn-xs'>EDIT</a> "
                . "<a href='modul/mod_konseling/tampil_konseling.php?id={$value['id_konseling']}' target='_blank' title='TAMPIL' class='btn btn-light-blue btn-xs'>TAMPIL</a> "
                . "<a href=\"javascript:confirmdelete('{$aksi}?module=konseling&act=hapus&id={$value['id_konseling']}')\" title='HAPUS' class='btn btn-maroon btn-xs'>HAPUS</a>";

            $data[] = $nestedData;
            $no++;
        }
    }

    $json_data = array(
        "draw" => intval($_POST['draw']),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
    );

    echo json_encode($json_data);
}
