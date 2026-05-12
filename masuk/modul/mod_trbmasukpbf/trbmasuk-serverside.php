<?php
session_start();
include_once '../../../configurasi/koneksi.php';

if ($_GET['action'] == "table_data") {

    $columns = array(
        0 => 'id_trbmasuk',
        1 => 'kd_trbmasuk',
        2 => 'kd_orders',
        3 => 'petugas',
        4 => 'tgl_trbmasuk',
        5 => 'nm_supplier',
        6 => 'ket_trbmasuk',
        7 => 'sisa_bayar',
        8 => 'jatuh_tempo',
        9 => 'carabayar',
        10 => 'id_trbmasuk'
    );

    $querycount = $db->prepare("SELECT count(id_trbmasuk) as jumlah FROM trbmasuk WHERE id_resto = ? 
                                AND jenis = ?");
    $querycount->execute(['pusat','pbf']);
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalData = $datacount['jumlah'];

    $totalFiltered = $totalData;

    $limit = $_POST['length'];
    $start = $_POST['start'];
    $order = $columns[$_POST['order']['0']['column']];
    $dir = $_POST['order']['0']['dir'];

    if (empty($_POST['search']['value'])) {
        $query = $db->prepare("SELECT *
            FROM trbmasuk
            WHERE trbmasuk.id_resto = 'pusat' AND trbmasuk.jenis = 'pbf'
            ORDER BY $order DESC LIMIT $limit OFFSET $start");
    } else {
        $search = $_POST['search']['value'];
        $query = $db->prepare("SELECT * 
            FROM trbmasuk
            WHERE trbmasuk.id_resto = 'pusat' AND trbmasuk.jenis = 'pbf'
                        AND kd_trbmasuk LIKE '%$search%'
                        OR kd_orders LIKE '%$search%'
                        OR petugas LIKE '%$search%'
                        OR tgl_trbmasuk LIKE '%$search%'
                        OR nm_supplier LIKE '%$search%'
                        OR ket_trbmasuk LIKE '%$search%'
                        OR sisa_bayar LIKE '%$search%'
                        OR carabayar LIKE '%$search%'
            ORDER BY $order DESC LIMIT $limit OFFSET $start");

        $querycount = $db->prepare("SELECT count(id_trbmasuk) as jumlah 
            FROM trbmasuk
            WHERE trbmasuk.id_resto = 'pusat' AND trbmasuk.jenis = 'pbf'
                        AND kd_trbmasuk LIKE '%$search%'
                        OR kd_orders LIKE '%$search%'
                        OR petugas LIKE '%$search%'
                        OR tgl_trbmasuk LIKE '%$search%'
                        OR nm_supplier LIKE '%$search%'
                        OR ket_trbmasuk LIKE '%$search%'
                        OR sisa_bayar LIKE '%$search%'
                        OR carabayar LIKE '%$search%'");

        $querycount->execute();
        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();
    // $totalNilaiBarang = 0;
    if (!empty($query)) {
      
        $no = $start + 1;
        $query->execute();
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            // for column
            $nestedData['no']               = '<input type="checkbox" name="check[]" value="' . $value['kd_trbmasuk'] . '"> '.$no;           
            $nestedData['kd_trbmasuk']      = $value['kd_trbmasuk'];
            $nestedData['kd_orders']        = $value['kd_orders'];
            $nestedData['petugas']          = $value['petugas'];
            $nestedData['tgl_trbmasuk']     = $value['tgl_trbmasuk'];
            $nestedData['nm_supplier']      = $value['nm_supplier'];
            $nestedData['ket_trbmasuk']     = $value['ket_trbmasuk'];
            $nestedData['jatuh_tempo']      = $value['jatuhtempo'];
            $nestedData['sisa_bayar']       = $value['sisa_bayar'];
            $nestedData['carabayar']        = $value['carabayar'];
            $nestedData['tgl_lunas']        = $value['tgl_lunas'];
            $nestedData['petugas_lunas']    = $value['petugas_lunas'];
            
            if($_SESSION['level'] == 'pemilik'){
                $nestedData['aksi']             = "<a href='?module=trbmasukpbf&act=tampil&id=$value[id_trbmasuk]' title='EDIT' class='btn btn-warning btn-xs'>TAMPIL</a>
                                                <a href='?module=trbmasukpbf&act=ubah&id=$value[id_trbmasuk]' title='EDIT' class='btn btn-primary btn-xs'>EDIT</a>";
            } else {
                $nestedData['aksi']             = "<a href='?module=trbmasukpbf&act=tampil&id=$value[id_trbmasuk]' title='EDIT' class='btn btn-warning btn-xs'>TAMPIL</a>";
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
