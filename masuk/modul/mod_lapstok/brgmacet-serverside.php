<?php
include_once '../../../configurasi/koneksi.php';

if ($_GET['action'] == "table_data") {

    $columns = array(
        0 => 'id_barang',
        1 => 'kd_barang',
        2 => 'nm_barang',
        3 => 'stok_barang',
        4 => 'stok_buffer',
        5 => 't30',
        6 => 'q30',
        7 => 'sat_barang',
        8 => 'hrgsat_barang',
        9 => 'nilai_barang',
        10 => 'kartu_stok'
    );

    $querycount = $db->prepare("SELECT
                    COUNT(id_barang) AS jumlah,
                    SUM(hrgsat_barang * stok_barang) AS totalNilaiStok
                FROM barang 
                WHERE NOT EXISTS (
                    SELECT trkasir_detail.kd_barang FROM trkasir_detail
                    JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
                    WHERE trkasir_detail.id_barang = barang.id_barang
                    AND trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                )");

    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalStok = $datacount['totalNilaiStok'];
    $totalData = $datacount['jumlah'];

    $totalFiltered = $totalData;

    $limit = $_POST['length'];
    $start = $_POST['start'];
    $order = $columns[$_POST['order']['0']['column']];
    $dir = $_POST['order']['0']['dir'];

    if (empty($_POST['search']['value'])) {
        $query = $db->prepare("SELECT
                id_barang, 
                kd_barang, 
                nm_barang, 
                stok_barang, 
                stok_buffer, 
                sat_barang, 
                hrgsat_barang
            FROM barang 
            WHERE NOT EXISTS (
                SELECT trkasir_detail.kd_barang FROM trkasir_detail
                JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
                WHERE trkasir_detail.id_barang = barang.id_barang
                AND trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
            )
            ORDER BY $order $dir LIMIT $limit OFFSET $start");
    } else {
        $search = $_POST['search']['value'];
        $query = $db->prepare("SELECT
                    id_barang, 
                    kd_barang, 
                    nm_barang, 
                    stok_barang, 
                    stok_buffer, 
                    sat_barang, 
                    hrgsat_barang
                FROM barang 
                WHERE NOT EXISTS (
                    SELECT trkasir_detail.kd_barang FROM trkasir_detail
                    JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
                    WHERE trkasir_detail.id_barang = barang.id_barang
                    AND trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                )
                    AND kd_barang LIKE '%$search%' 
                    OR nm_barang LIKE '%$search%'
        ORDER BY $order $dir LIMIT $limit OFFSET $start");

        $querycount = $db->prepare("SELECT
                            COUNT(id_barang) AS jumlah
                        FROM barang 
                        WHERE NOT EXISTS (
                            SELECT trkasir_detail.kd_barang FROM trkasir_detail
                            JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
                            WHERE trkasir_detail.id_barang = barang.id_barang
                            AND trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                        ) 
                        AND kd_barang LIKE '%$search%' 
                        OR nm_barang LIKE '%$search%'
        ORDER BY $order $dir LIMIT $limit OFFSET $start");

        $querycount->execute();
        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = $datacount['jumlah'];
    }


    $data = array();
    if (!empty($query)) {
        $no = $start + 1;
        $query->execute();
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            $pass = $db->prepare("SELECT count(id_dtrkasir) AS jumlah,
                    SUM(trkasir_detail.qty_dtrkasir) as pw
                FROM trkasir_detail JOIN trkasir
                ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir 
                WHERE trkasir_detail.id_barang = '$value[id_barang]'
                AND trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'");
            $pass->execute();
            $pass1 = $pass->fetch(PDO::FETCH_ASSOC);

            $nilaibarang = $value['hrgsat_barang'] * $value['stok_barang'];

            $nestedData['no'] = $no;
            $nestedData['kd_barang'] = $value['kd_barang'];
            $nestedData['nm_barang'] = $value['nm_barang'];
            $nestedData['stok_barang'] = $value['stok_barang'];
            $nestedData['stok_buffer'] = $value['stok_buffer'];
            $nestedData['t30'] = $pass1['jumlah'];
            $nestedData['q30'] = (($pass1['pw'] <= 0) ? 0 : $pass1['pw']);
            $nestedData['satuan'] = $value['sat_barang'];
            $nestedData['harga_beli'] = $value['hrgsat_barang'];
            $nestedData['nilai_barang'] = $nilaibarang;
            $nestedData['kartu_stok'] = "<a href='?module=lapstok&act=edit&id=$value[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a>";
            $data[] = $nestedData;
            $no++;
        }
    }

    $json_data = [
        "draw"              => intval($_POST['draw']),
        "recordsTotal"      => intval($totalData),
        "recordsFiltered"   => intval($totalFiltered),
        "totalStok"         => $totalStok,
        "data"              => $data
    ];

    echo json_encode($json_data);
}
