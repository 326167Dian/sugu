<?php
session_start();
include_once '../../../configurasi/koneksi.php';

if ($_GET['action'] == 'table_data') {
    $columns = array(
        0 => 'a.id_trkasir',
        1 => 'a.kd_trkasir',
        2 => 'a.petugas',
        3 => 'a.shift',
        4 => 'a.jenistx',
        5 => 'a.tgl_trkasir',
        6 => 'a.nm_pelanggan',
        7 => 'a.kodetx',
        8 => 'b.nm_carabayar',
        9 => 'a.ttl_trkasir',
        10 => 'a.id_trkasir',
    );

    $aksi = 'modul/mod_trkasir/aksi_trkasir.php';
    $tgl_awal = date('Y-m-d');

    $limit = isset($_POST['length']) ? (int) $_POST['length'] : 10;
    $start = isset($_POST['start']) ? (int) $_POST['start'] : 0;
    $orderIndex = isset($_POST['order'][0]['column']) ? (int) $_POST['order'][0]['column'] : 0;
    $order = isset($columns[$orderIndex]) ? $columns[$orderIndex] : 'a.id_trkasir';
    $dir = (isset($_POST['order'][0]['dir']) && strtolower($_POST['order'][0]['dir']) === 'asc') ? 'ASC' : 'DESC';
    $search = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';

    $baseFrom = ' FROM trkasir a JOIN carabayar b ON a.id_carabayar = b.id_carabayar ';
    $baseWhere = ' WHERE a.tgl_trkasir = ? ';
    $baseParams = array($tgl_awal);

    $searchWhere = '';
    $searchParams = array();
    if ($search !== '') {
        $searchLike = '%' . $search . '%';
        $searchWhere = " AND (
            a.kd_trkasir LIKE ?
            OR a.shift LIKE ?
            OR a.jenistx LIKE ?
            OR a.petugas LIKE ?
            OR a.tgl_trkasir LIKE ?
            OR a.ttl_trkasir LIKE ?
            OR a.nm_pelanggan LIKE ?
            OR a.kodetx LIKE ?
            OR b.nm_carabayar LIKE ?
        )";
        $searchParams = array(
            $searchLike,
            $searchLike,
            $searchLike,
            $searchLike,
            $searchLike,
            $searchLike,
            $searchLike,
            $searchLike,
            $searchLike,
        );
    }

    $whereSql = $baseWhere . $searchWhere;
    $params = array_merge($baseParams, $searchParams);

    $countStmt = $db->prepare('SELECT COUNT(a.id_trkasir) AS jumlah' . $baseFrom . $baseWhere);
    $countStmt->execute($baseParams);
    $totalData = (int) $countStmt->fetch(PDO::FETCH_ASSOC)['jumlah'];

    $filteredStmt = $db->prepare('SELECT COUNT(a.id_trkasir) AS jumlah' . $baseFrom . $whereSql);
    $filteredStmt->execute($params);
    $totalFiltered = (int) $filteredStmt->fetch(PDO::FETCH_ASSOC)['jumlah'];

    $summarySql = 'SELECT 
            COALESCE(SUM(a.ttl_trkasir), 0) AS total_kasir,
            COALESCE(SUM(CASE WHEN a.id_carabayar = 1 THEN a.ttl_trkasir ELSE 0 END), 0) AS total_tunai,
            COALESCE(SUM(CASE WHEN a.id_carabayar = 1 AND a.shift = 1 THEN a.ttl_trkasir ELSE 0 END), 0) AS total_tunai_pagi,
            COALESCE(SUM(CASE WHEN a.id_carabayar = 1 AND a.shift = 2 THEN a.ttl_trkasir ELSE 0 END), 0) AS total_tunai_sore,
            COALESCE(SUM(CASE WHEN a.id_carabayar = 2 THEN a.ttl_trkasir ELSE 0 END), 0) AS total_transfer,
            COALESCE(SUM(CASE WHEN a.id_carabayar = 2 AND a.shift = 1 THEN a.ttl_trkasir ELSE 0 END), 0) AS total_transfer_pagi,
          COALESCE(SUM(CASE WHEN a.id_carabayar = 2 AND a.shift = 2 THEN a.ttl_trkasir ELSE 0 END), 0) AS total_transfer_sore,
          COALESCE(SUM(CASE WHEN a.id_carabayar = 3 THEN a.ttl_trkasir ELSE 0 END), 0) AS total_tempo,
          COALESCE(SUM(CASE WHEN a.id_carabayar = 3 AND a.shift = 1 THEN a.ttl_trkasir ELSE 0 END), 0) AS total_tempo_pagi,
          COALESCE(SUM(CASE WHEN a.id_carabayar = 3 AND a.shift = 2 THEN a.ttl_trkasir ELSE 0 END), 0) AS total_tempo_sore'
        . $baseFrom . $whereSql;
    $summaryStmt = $db->prepare($summarySql);
    $summaryStmt->execute($params);
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

    $querySql = 'SELECT a.*, b.nm_carabayar'
        . $baseFrom . $whereSql
        . ' ORDER BY ' . $order . ' ' . $dir . ' LIMIT ' . $limit . ' OFFSET ' . $start;
    $query = $db->prepare($querySql);
    $query->execute($params);

    $data = array();
    $no = $start + 1;

    while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
        $nestedData = array();
        $nestedData['no'] = $no;
        $nestedData['kd_trkasir'] = $value['kd_trkasir'];
        $nestedData['shift'] = $value['shift'];
        $nestedData['jenistx'] = $value['jenistx'];
        $nestedData['petugas'] = $value['petugas'];
        $nestedData['tgl_trkasir'] = $value['tgl_trkasir'];
        $nestedData['nm_pelanggan'] = $value['nm_pelanggan'];
        $nestedData['kodetx'] = $value['kodetx'];
        $nestedData['nm_carabayar'] = $value['nm_carabayar'];
        $nestedData['ttl_trkasir'] = $value['ttl_trkasir'];

        if ($_SESSION['level'] == 'pemilik') {
            $nestedData['pilih'] = '<div class="dropdown">
                                          <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" >
                                            Action
                                            <i class="fa fa-caret-down"></i>
                                          </button>
                                          <div class="dropdown-menu">
                                            <a class="btn btn-primary btn-xs" onclick="window.open(\'modul/mod_laporan/member_card.php?kd_trkasir=' . $value['kd_trkasir'] . '\',\'nama window\',\'width=500,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no, scrollbars=no,resizable=yes,copyhistory=no\')" style="width:50%; margin:0 5 5 5">TES</a>
                                            <a href="?module=trkasir&act=ubah&id=' . $value['id_trkasir'] . '" title="EDIT" class="btn btn-info btn-xs" style="width:50%; margin:0 5 5 5">EDIT</a>
                                            <a href=javascript:confirmdelete("' . $aksi . '?module=trkasir&act=hapus&id=' . $value['id_trkasir'] . '") title="HAPUS" class="btn btn-danger btn-xs" style="width:50%; margin:0 3 3 3">HAPUS</a>
                                            <a class="btn btn-primary btn-xs" onclick="window.open(\'modul/mod_laporan/struk.php?kd_trkasir=' . $value['kd_trkasir'] . '\',\'nama window\',\'width=500,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no, scrollbars=no,resizable=yes,copyhistory=no\')" style="width:50%; margin:0 5 5 5">PRINT</a>
                                            <a href="modul/mod_laporan/kwitansi.php?kd_trkasir=' . $value['kd_trkasir'] . '" target="_blank" title="KWITANSI" class="btn btn-warning btn-xs" style="width:50%; margin:0 3 3 3">KWITANSI</a>
                                            <a href="modul/mod_laporan/invoice.php?kd_trkasir=' . $value['kd_trkasir'] . '" target="_blank" title="INVOICE" class="btn btn-primary btn-xs" style="width:50%; margin:0 3 3 3">INVOICE</a>
                                            <a href="modul/mod_etiket/etiket.php?module=trkasir&kd_trkasir=' . $value['kd_trkasir'] . '" target="_blank" title="ETIKET" class="btn btn-success btn-xs" style="width:50%; margin:0 3 3 3">ETIKET</a>
                                          </div>
                                        </div>';
        } else {
            $nestedData['pilih'] = '<div class="dropdown">
                                          <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" >
                                            Action
                                            <i class="fa fa-caret-down"></i>
                                          </button>
                                          <div class="dropdown-menu">
                                            <a class="btn btn-primary btn-xs" onclick="window.open(\'modul/mod_laporan/struk.php?kd_trkasir=' . $value['kd_trkasir'] . '\',\'nama window\',\'width=500,height=600,toolbar=no,location=no,directories=no,status=no,menubar=no, scrollbars=no,resizable=yes,copyhistory=no\')" style="width:50%; margin:0 5 5 5">PRINT</a>
                                            <a href="modul/mod_laporan/kwitansi.php?kd_trkasir=' . $value['kd_trkasir'] . '" target="_blank" title="KWITANSI" class="btn btn-warning btn-xs" style="width:50%; margin:0 5 5 5">KWITANSI</a>
                                            <a href="modul/mod_laporan/invoice.php?kd_trkasir=' . $value['kd_trkasir'] . '" target="_blank" title="INVOICE" class="btn btn-primary btn-xs" style="width:50%; margin:0 3 3 3">INVOICE</a>
                                            <a href="modul/mod_etiket/etiket.php?module=trkasir&kd_trkasir=' . $value['kd_trkasir'] . '" target="_blank" title="ETIKET" class="btn btn-success btn-xs" style="width:50%; margin:0 3 3 3">ETIKET</a>
                                          </div>
                                        </div>';
        }

        $data[] = $nestedData;
        $no++;
    }

    $json_data = array(
        'draw' => intval($_POST['draw']),
        'recordsTotal' => $totalData,
        'recordsFiltered' => $totalFiltered,
        'data' => $data,
        'totalKasir' => (int) $summary['total_kasir'],
        'totalTunai' => (int) $summary['total_tunai'],
        'totalTunaiPagi' => (int) $summary['total_tunai_pagi'],
        'totalTunaiSore' => (int) $summary['total_tunai_sore'],
        'totalTransfer' => (int) $summary['total_transfer'],
        'totalTransferPagi' => (int) $summary['total_transfer_pagi'],
        'totalTransferSore' => (int) $summary['total_transfer_sore'],
        'totalTempo' => (int) $summary['total_tempo'],
        'totalTempoPagi' => (int) $summary['total_tempo_pagi'],
        'totalTempoSore' => (int) $summary['total_tempo_sore'],
    );

    echo json_encode($json_data);
}