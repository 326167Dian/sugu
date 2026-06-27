<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include_once '../../../configurasi/koneksi.php';

function format_pelanggan_local_datetime_serverside($datetime)
{
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return '-';
    }

    try {
        $jakarta_timezone = new DateTimeZone('Asia/Jakarta');
        $local_datetime = new DateTime($datetime, $jakarta_timezone);

        return $local_datetime->format('d-m-Y H:i:s');
    } catch (Exception $e) {
        return $datetime;
    }
}

if ($_GET['action'] == "table_data") {

    $columns = array(
        0 => 'id_pelanggan',
        1 => 'nm_pelanggan',
        2 => 'tlp_pelanggan',
        3 => 'alamat_pelanggan',
        4 => 'ket_pelanggan',
        5 => 'followup',
        6 => 'id_pelanggan',
    );

    $aksi = "modul/mod_pelanggan/aksi_pelanggan.php";

    $querycount = $db->query("SELECT count(id_pelanggan) as jumlah FROM pelanggan");
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalData = $datacount['jumlah'];
    $totalFiltered = $totalData;

    $limit = intval($_POST['length']);
    $start = intval($_POST['start']);
    $colIndex = intval($_POST['order']['0']['column']);
    $order = isset($columns[$colIndex]) ? $columns[$colIndex] : 'id_pelanggan';
    $dir = (isset($_POST['order']['0']['dir']) && strtolower($_POST['order']['0']['dir']) === 'asc') ? 'ASC' : 'DESC';

    // Filter unit: tampilkan pelanggan sesuai unit admin yang login
    $unitFilter = (isset($_SESSION['unit']) && !empty($_SESSION['unit'])) ? intval($_SESSION['unit']) : null;
    $unitWhere = $unitFilter !== null ? " WHERE unit = $unitFilter " : " ";
    $unitWhereAnd = $unitFilter !== null ? " AND unit = $unitFilter " : " ";

    // Hitung total dengan filter unit
    $querycount = $db->query("SELECT count(id_pelanggan) as jumlah FROM pelanggan" . $unitWhere);
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
    $totalData = $datacount['jumlah'];
    $totalFiltered = $totalData;

    if (empty($_POST['search']['value'])) {
        // always sort by id_pelanggan descending (largest first)
        $query = $db->query("SELECT * FROM pelanggan" . $unitWhere . "ORDER BY id_pelanggan DESC LIMIT $limit OFFSET $start ");
    } else {
        $search = $_POST['search']['value'];
        $query = $db->prepare("SELECT * FROM pelanggan 
            WHERE (nm_pelanggan LIKE ? 
            OR tlp_pelanggan LIKE ? 
            OR alamat_pelanggan LIKE ? 
            OR ket_pelanggan LIKE ?) 
            $unitWhereAnd
            ORDER BY id_pelanggan DESC LIMIT $limit OFFSET $start");
        $query->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);

        $querycount = $db->prepare("SELECT count(id_pelanggan) as jumlah FROM pelanggan 
            WHERE (nm_pelanggan LIKE ? 
            OR tlp_pelanggan LIKE ? 
            OR alamat_pelanggan LIKE ? 
            OR ket_pelanggan LIKE ?)
            $unitWhereAnd");
        $querycount->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();

    if (!empty($query)) {
        $no = $start + 1;
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            $nestedData['no'] = $no;
            $nestedData['nm_pelanggan'] = $value['nm_pelanggan'];
            $nestedData['tlp_pelanggan'] = $value['tlp_pelanggan'];
            $nestedData['alamat_pelanggan'] = $value['alamat_pelanggan'];
            $nestedData['ket_pelanggan'] = $value['ket_pelanggan'];

            // get latest followup (most recent created_at)
            $followq = $db->prepare("SELECT followup, created_at FROM riwayat_pelanggan WHERE id_pelanggan = ? ORDER BY created_at DESC LIMIT 1");
            $followq->execute([$value['id_pelanggan']]);
            if ($followq->rowCount() > 0) {
                $fq = $followq->fetch(PDO::FETCH_ASSOC);
                $nestedData['followup'] = htmlspecialchars($fq['followup']);
                $nestedData['followup'] .= "<br><small>" . format_pelanggan_local_datetime_serverside($fq['created_at']) . "</small>";
            } else {
                $nestedData['followup'] = '';
            }

            if(
                isset($_SESSION['level']) && $_SESSION['level'] == 'pemilik'
            ){
                $nestedData['pilih'] = '<div class="dropdown">'
                    . '<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action <i class="fa fa-caret-down"></i></button>'
                    . '<div class="dropdown-menu">'
                    . '<a href="?module=pelanggan&act=edit&id=' . $value['id_pelanggan'] . '" title="EDIT" class="btn btn-info btn-xs" style="width: 50%; margin:5px 0">EDIT</a> <br>'
                    . '<a href="?module=pelanggan&act=riwayat&id=' . $value['id_pelanggan'] . '" title="RIWAYAT" class="btn btn-success btn-xs" style="width:50%; margin:5px 0">SWAMEDIKASI</a> <br>'
                    . '<a href="?module=cekdarah&act=tambah&id_pelanggan=' . $value['id_pelanggan'] . '" title="RIWAYAT" class="btn btn-primary btn-xs" style="width:50%; margin:5px 0">Cek Darah</a> <br>'
                    . '<a href="?module=konseling&act=tambah&id_pelanggan=' . $value['id_pelanggan'] . '" title="KONSELING" class="btn btn-primary btn-xs" style="width:50%; margin:5px 0">KONSELING</a> <br>'
                    . '<a href="?module=meso&act=input_meso&id_pelanggan=' . $value['id_pelanggan'] . '" title="MESO" class="btn btn-warning btn-xs" style="width:50%; margin:5px 0">MESO</a> <br>'
                    . '<a href="?module=pio&act=input_pio&id_pelanggan=' . $value['id_pelanggan'] . '" title="PIO" class="btn btn-danger btn-xs" style="width:50%; margin:5px 0">PIO</a> <br>'
                    . '<a href="?module=pto&act=riwayat&id_pelanggan=' . $value['id_pelanggan'] . '" title="PTO" class="btn btn-default btn-xs" style="width:50%; margin:5px 0">PTO</a> <br>'
                    . '<a href="?module=cpp&act=input_cpp&id_pelanggan=' . $value['id_pelanggan'] . '" title="CATATAN PENGOBATAN PASIEN" class="btn btn-success btn-xs" style="width:50%; margin:5px 0">CPP</a> <br>'
                    . '<a href="?module=homecare&act=input_homecare&id_pelanggan=' . $value['id_pelanggan'] . '" title="HOMECARE" class="btn btn-info btn-xs" style="width:50%; margin:5px 0">HOMECARE</a> <br>'
                    . '<a href="javascript:confirmdelete(\'' . $aksi . '?module=pelanggan&act=hapus&id=' . $value['id_pelanggan'] . '\')" title="HAPUS" class="btn btn-danger btn-xs" style="width:50%; margin:5px 0">HAPUS</a>'
                    . '</div>'
                    . '</div>';
            } else {
                $nestedData['pilih'] = '<div class="dropdown">'
                    . '<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action <i class="fa fa-caret-down"></i></button>'
                    . '<div class="dropdown-menu">'
                    . '<a href="?module=pelanggan&act=edit&id=' . $value['id_pelanggan'] . '" title="EDIT" class="btn btn-info btn-xs" style="width:50%; margin:5px 0">EDIT</a> <br>'
                    . '<a href="?module=pelanggan&act=riwayat&id=' . $value['id_pelanggan'] . '" title="RIWAYAT" class="btn btn-success btn-xs" style="width:50%; margin:5px 0">SWAMEDIKASI</a><br>'
                     . '<a href="?module=cekdarah&act=tambah&id_pelanggan=' . $value['id_pelanggan'] . '" title="RIWAYAT" class="btn btn-primary btn-xs" style="width:50%; margin:5px 0">Cek Darah</a> <br>'
                    . '<a href="?module=konseling&act=tambah&id_pelanggan=' . $value['id_pelanggan'] . '" title="KONSELING" class="btn btn-success btn-xs" style="width:50%; margin:5px 0">KONSELING</a><br>'
                    . '<a href="?module=meso&act=input_meso&id_pelanggan=' . $value['id_pelanggan'] . '" title="MESO" class="btn btn-warning btn-xs" style="width:50%; margin:5px 0">MESO</a> <br>'
                    . '<a href="?module=pio&act=input_pio&id_pelanggan=' . $value['id_pelanggan'] . '" title="PIO" class="btn btn-danger btn-xs" style="width:50%; margin:5px 0">PIO</a> <br>'
                    . '<a href="?module=pto&act=riwayat&id_pelanggan=' . $value['id_pelanggan'] . '" title="PTO" class="btn btn-default btn-xs" style="width:50%; margin:5px 0">PTO</a> <br>'
                    . '<a href="?module=cpp&act=input_cpp&id_pelanggan=' . $value['id_pelanggan'] . '" title="CATATAN PENGOBATAN PASIEN" class="btn btn-success btn-xs" style="width:50%; margin:5px 0">CPP</a> <br>'
                    . '<a href="?module=homecare&act=input_homecare&id_pelanggan=' . $value['id_pelanggan'] . '" title="HOMECARE" class="btn btn-info btn-xs" style="width:50%; margin:5px 0">HOMECARE</a> <br>'
                    . '</div>'
                    . '</div>';
            }

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
