<?php
session_start();
include_once '../../../configurasi/koneksi.php';

$aksi = "modul/mod_swamedikasi/aksi_swamedikasi.php";

if (isset($_GET['action']) && $_GET['action'] == "table_data") {
    $columns = array(
        0 => 'id_swamedikasi',
        1 => 'tgl_swamedikasi',
        2 => 'nm_pelanggan',
        3 => 'keluhan',
        4 => 'obat_direkomendasikan',
        5 => 'saran',
        6 => 'updated_at',
        7 => 'id_swamedikasi'
    );

    $querycount = $db->query("SELECT count(id_swamedikasi) as jumlah FROM swamedikasi");
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalData = $datacount['jumlah'];
    $totalFiltered = $totalData;

    $limit = intval($_POST['length']);
    $start = intval($_POST['start']);
    $colIndex = isset($_POST['order']['0']['column']) ? intval($_POST['order']['0']['column']) : 0;
    $order = isset($columns[$colIndex]) ? $columns[$colIndex] : 'id_swamedikasi';
    $dir = (isset($_POST['order']['0']['dir']) && strtolower($_POST['order']['0']['dir']) === 'asc') ? 'ASC' : 'DESC';

    if (empty($_POST['search']['value'])) {
        $query = $db->prepare("SELECT * FROM swamedikasi ORDER BY $order $dir LIMIT $limit OFFSET $start");
        $query->execute();
    } else {
        $search = $_POST['search']['value'];
        $like = "%" . $search . "%";
        $query = $db->prepare("SELECT * FROM swamedikasi WHERE nm_pelanggan LIKE ? OR keluhan LIKE ? OR obat_direkomendasikan LIKE ? OR saran LIKE ? ORDER BY $order $dir LIMIT $limit OFFSET $start");
        $query->execute([$like, $like, $like, $like]);

        $querycount = $db->prepare("SELECT count(id_swamedikasi) as jumlah FROM swamedikasi WHERE nm_pelanggan LIKE ? OR keluhan LIKE ? OR obat_direkomendasikan LIKE ? OR saran LIKE ?");
        $querycount->execute([$like, $like, $like, $like]);
        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();
    if (!empty($query)) {
        $no = $start + 1;
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            $nestedData = array();
            $nestedData['no'] = $no;
            $nestedData['tgl_swamedikasi'] = $value['tgl_swamedikasi'];
            $nestedData['nm_pelanggan'] = htmlspecialchars($value['nm_pelanggan']);
            $nestedData['keluhan'] = htmlspecialchars($value['keluhan']);
            $nestedData['obat_direkomendasikan'] = htmlspecialchars($value['obat_direkomendasikan']);
            $nestedData['saran'] = htmlspecialchars($value['saran']);
            $nestedData['updated_at'] = $value['updated_at'];

            $nestedData['aksi'] = "<a href='?module=swamedikasi&act=edit&id={$value['id_swamedikasi']}' title='EDIT' class='btn btn-purple btn-xs'>EDIT</a> "
                . "<a href=\"javascript:confirmdelete('{$aksi}?module=swamedikasi&act=hapus&id={$value['id_swamedikasi']}')\" title='HAPUS' class='btn btn-maroon btn-xs'>HAPUS</a>";

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
?>
