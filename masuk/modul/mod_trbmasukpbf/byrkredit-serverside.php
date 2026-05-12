<?php
session_start();
include_once '../../../configurasi/koneksi.php';
include_once '../../../configurasi/fungsi_rupiah.php';
header('Content-Type: application/json; charset=UTF-8');

$aksi = "modul/mod_trbmasuk/aksi_trbmasuk.php";

if (!isset($_GET['action']) || $_GET['action'] !== 'table_data') {
    echo json_encode(array(
        'draw' => 0,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => array()
    ));
    exit;
}

$draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 0;
$start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
$length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
if ($start < 0) {
    $start = 0;
}
if ($length < 1) {
    $length = 10;
}
if ($length > 100) {
    $length = 100;
}

$columns = array(
    0 => 'id_trbmasuk',
    1 => 'kd_trbmasuk',
    2 => 'petugas',
    3 => 'tgl_trbmasuk',
    4 => 'nm_supplier',
    5 => 'ket_trbmasuk',
    6 => 'sisa_bayar',
    7 => 'carabayar',
    8 => 'id_trbmasuk'
);

$orderColumnIndex = isset($_POST['order'][0]['column']) ? (int)$_POST['order'][0]['column'] : 0;
$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'id_trbmasuk';
$orderDir = (isset($_POST['order'][0]['dir']) && strtolower($_POST['order'][0]['dir']) === 'asc') ? 'ASC' : 'DESC';

$search = '';
if (isset($_POST['search']['value'])) {
    $search = trim($_POST['search']['value']);
    if (strlen($search) > 100) {
        $search = substr($search, 0, 100);
    }
}

$countStmt = $db->prepare("SELECT COUNT(id_trbmasuk) as jumlah FROM trbmasuk WHERE id_resto = 'pusat'");
$countStmt->execute();
$totalData = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['jumlah'];
$totalFiltered = $totalData;

$params = array();
$whereSql = " WHERE id_resto = 'pusat' ";

if ($search !== '') {
    $whereSql .= " AND (kd_trbmasuk LIKE ? OR petugas LIKE ? OR tgl_trbmasuk LIKE ? OR nm_supplier LIKE ? OR carabayar LIKE ?) ";
    $searchValue = '%' . $search . '%';
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $countFilteredSql = "SELECT COUNT(id_trbmasuk) as jumlah FROM trbmasuk" . $whereSql;
    $countFilteredStmt = $db->prepare($countFilteredSql);
    $countFilteredStmt->execute($params);
    $totalFiltered = (int)$countFilteredStmt->fetch(PDO::FETCH_ASSOC)['jumlah'];
}

$dataSql = "SELECT id_trbmasuk, kd_trbmasuk, petugas, tgl_trbmasuk, nm_supplier, ket_trbmasuk, sisa_bayar, carabayar
            FROM trbmasuk" . $whereSql . " ORDER BY " . $orderColumn . " " . $orderDir . " LIMIT " . $length . " OFFSET " . $start;
$dataStmt = $db->prepare($dataSql);
$dataStmt->execute($params);

$data = array();
$no = $start + 1;
while ($row = $dataStmt->fetch(PDO::FETCH_ASSOC)) {
    $isLunas = ($row['carabayar'] === 'LUNAS');

    $noCell = $isLunas
        ? (string)$no
        : '<span style="display:block;background-color:#ffbf00;">' . $no . '</span>';

    $kodeCell = $isLunas
        ? (string)$row['kd_trbmasuk']
        : '<span style="display:block;background-color:#ffbf00;">' . $row['kd_trbmasuk'] . '</span>';

    $aksiHtml = "<a href='?module=byrkredit&act=ubah&id=" . $row['id_trbmasuk'] . "' title='EDIT' class='btn btn-warning btn-xs'>EDIT</a> "
        . "<a href=\"javascript:confirmdelete('" . $aksi . "?module=trbmasuk&act=hapus&id=" . $row['id_trbmasuk'] . "')\" title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>";

    $data[] = array(
        $noCell,
        $kodeCell,
        $row['petugas'],
        $row['tgl_trbmasuk'],
        $row['nm_supplier'],
        $row['ket_trbmasuk'],
        '<div style="text-align:right;">' . format_rupiah($row['sisa_bayar']) . '</div>',
        '<div style="text-align:center;">' . $row['carabayar'] . '</div>',
        $aksiHtml
    );

    $no++;
}

echo json_encode(array(
    'draw' => $draw,
    'recordsTotal' => $totalData,
    'recordsFiltered' => $totalFiltered,
    'data' => $data
));
