<?php
include_once '../../../configurasi/koneksi.php';
session_start();
$aksi = "modul/mod_shiftkerja/aksi_shiftkerja.php";
if ($_GET['action'] == "table_data") {
    $columns = array(
        0 => 'id_shift',
        1 => 'petugasbuka',
        2 => 'petugastutup',
        3 => 'shift',
        4 => 'tanggal',
        5 => 'waktubuka',
        6 => 'waktututup',
        7 => 'saldoawal',
        8 => 'saldoakhir',
        9 => 'status',
        10 => 'id_shift'
    );

    $querycount = $db->prepare("SELECT count(id_shift) as jumlah FROM waktukerja");
    $querycount->execute();
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalData = $datacount['jumlah'];

    $totalFiltered = $totalData;

    $limit = $_POST['length'];
    $start = $_POST['start'];
    // Default order: show most recent entries first
    $order = 'id_shift';
    $dir = 'DESC';
    if (isset($_POST['order']['0']['column']) && $_POST['order']['0']['column'] !== '') {
        $colIndex = intval($_POST['order']['0']['column']);
        if (isset($columns[$colIndex])) {
            $order = $columns[$colIndex];
        }
        $dir = (isset($_POST['order']['0']['dir']) && strtoupper($_POST['order']['0']['dir']) === 'ASC') ? 'ASC' : 'DESC';
    }

    if (empty($_POST['search']['value'])) {
        $query = $db->prepare("SELECT w.*, n.nama_shift FROM waktukerja w LEFT JOIN namashift n ON n.shift = w.shift ORDER BY $order DESC LIMIT $limit OFFSET $start");
    } else {
        $search = $db->real_escape_string($_POST['search']['value']);
        $query = $db->prepare("SELECT w.*, n.nama_shift FROM waktukerja w LEFT JOIN namashift n ON n.shift = w.shift WHERE w.petugasbuka LIKE '%$search%' OR w.petugastutup LIKE '%$search%' OR w.tanggal LIKE '%$search%' OR n.nama_shift LIKE '%$search%' OR w.status LIKE '%$search%' ORDER BY $order $dir LIMIT $limit OFFSET $start");

        $querycount = $db->prepare("SELECT count(w.id_shift) as jumlah FROM waktukerja w LEFT JOIN namashift n ON n.shift = w.shift WHERE w.petugasbuka LIKE '%$search%' OR w.petugastutup LIKE '%$search%' OR w.tanggal LIKE '%$search%' OR n.nama_shift LIKE '%$search%' OR w.status LIKE '%$search%'");
        
        $querycount->execute();
        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();
    if (!empty($query)) {
        $no = $start + 1;
        $query->execute();
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            $nestedData = array();
            $nestedData['no'] = $no;
            $nestedData['petugasbuka'] = $value['petugasbuka'];
            $nestedData['petugastutup'] = $value['petugastutup'];
            $nestedData['nama_shift'] = $value['nama_shift'];
            $nestedData['tanggal'] = $value['tanggal'];
            $nestedData['waktubuka'] = $value['waktubuka'];
            $nestedData['waktututup'] = $value['waktututup'];
            $nestedData['saldoawal'] = $value['saldoawal'];
            $nestedData['saldoakhir'] = $value['saldoakhir'];
            $nestedData['status'] = $value['status'];

            $lupa = isset($_SESSION['level']) ? $_SESSION['level'] : '';
            if ($lupa == 'pemilik') {
                $nestedData['aksi'] = "<div class='dropdown'>
  <button class='btn btn-default dropdown-toggle' type='button' id='dropdownMenu{$value['id_shift']}' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>
    action
    <span class='caret'></span>
  </button>
  <ul class='dropdown-menu' aria-labelledby='dropdownMenu{$value['id_shift']}'>
    <li style='background-color:yellow;'><a href='?module=shiftkerja&act=editkoreksi&id={$value['id_shift']}'>EDIT</a></li>
    <li style='background-color:red;'><a href=javascript:confirmdelete('{$aksi}?module=shiftkerja&act=hapus&id={$value['id_shift']}')>HAPUS</a></li>
    <li style='background-color:aqua;'><a href='modul/mod_shiftkerja/laporanshiftday.php?idshift={$value['id_shift']}' target='_blanks'>LAPORAN</a></li>
  </ul>
</div>";
            } else {
                $nestedData['aksi'] = "<div class='dropdown'>
  <button class='btn btn-default dropdown-toggle' type='button' id='dropdownMenu{$value['id_shift']}' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>
    action
    <span class='caret'></span>
  </button>
  <ul class='dropdown-menu' aria-labelledby='dropdownMenu{$value['id_shift']}'>
    <li style='background-color:aqua;'><a href='modul/mod_shiftkerja/laporanshiftday.php?idshift={$value['id_shift']}' target='_blanks'>LAPORAN</a></li>
  </ul>
</div>";
            }

            $data[] = $nestedData;
            $no++;
        }
    }

    $json_data = [
        "draw" => intval($_POST['draw']),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
    ];

    echo json_encode($json_data);
}