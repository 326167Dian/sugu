<?php
include_once '../../../configurasi/koneksi.php';

// Provide default date range when not supplied by caller
if (!isset($_GET['start']) || $_GET['start']=='') $_GET['start'] = date('Y-m-d', strtotime('-30 days'));
if (!isset($_GET['finish']) || $_GET['finish']=='') $_GET['finish'] = date('Y-m-d');

if ($_GET['action'] == "table_data") {

    $columns = array(
        0 => 'id_barang',
        1 => 'kd_barang',
        2 => 'nm_barang',
        3 => 'stok_barang',
        4 => 't30',
        5 => 't60',
        6 => 'gr',
        7 => 'q30',
        8 => 'satuan',
        9 => 'harga_beli',
        10 => 'nilai_barang',
        11 => 'kartu_stok'
    );

    // count total data
    $querycount = $db->prepare("SELECT count(id_barang) as jumlah, SUM(hrgsat_barang*stok_barang) as totalNilaiStok FROM barang");
    $querycount->execute();
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalData = $datacount['jumlah'];
    $totalFiltered = $totalData;
    $totalNilaiStok = $datacount['totalNilaiStok'];

    $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $order = $columns[$_POST['order']['0']['column']];
    $dir = $_POST['order']['0']['dir'];

    if (empty($_POST['search']['value'])) {
        $tgl60 = date('Y-m-d', strtotime('-30 days', strtotime($_GET['start'])));
        $query = $db->prepare("SELECT a.kd_barang, a.nm_barang, a.stok_barang,
            (
                SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                AND trkasir_detail.id_barang = a.id_barang
            ) AS t30,
            (
                (SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                WHERE trkasir.tgl_trkasir BETWEEN '$tgl60' AND '$_GET[finish]'
                AND trkasir_detail.id_barang = a.id_barang) -
                (SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                AND trkasir_detail.id_barang = a.id_barang)
            ) AS t60,
            (
                SELECT SUM(trkasir_detail.qty_dtrkasir) FROM trkasir_detail
                JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                AND trkasir_detail.id_barang = a.id_barang
            ) AS q30,
            (
                ROUND((
                    (
                        (SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                        JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                        WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                        AND trkasir_detail.id_barang = a.id_barang)/
                        (
                            (SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                            JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                            WHERE trkasir.tgl_trkasir BETWEEN '$tgl60' AND '$_GET[finish]'
                            AND trkasir_detail.id_barang = a.id_barang) -
                            (SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                            JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                            WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                            AND trkasir_detail.id_barang = a.id_barang)
                        )
                    )*100
                )-100)
            )AS gr,
            a.sat_barang,
            a.hrgsat_barang,
            (a.hrgsat_barang * a.stok_barang) as nilai_barang
        FROM barang a 
        ORDER BY $order $dir LIMIT $limit OFFSET $start");
    } else {
        $tgl60 = date('Y-m-d', strtotime('-30 days', strtotime($_GET['start'])));
        $search = $_POST['search']['value'];
        $query = $db->prepare("SELECT a.kd_barang, a.nm_barang, a.stok_barang,
                (
                    SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                    JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                    WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                    AND trkasir_detail.id_barang = a.id_barang
                ) AS t30,
                (
                    (SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                    JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                    WHERE trkasir.tgl_trkasir BETWEEN '$tgl60' AND '$_GET[finish]'
                    AND trkasir_detail.id_barang = a.id_barang) -
                    (SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                    JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                    WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                    AND trkasir_detail.id_barang = a.id_barang)
                ) AS t60,
                (
                    SELECT SUM(trkasir_detail.qty_dtrkasir) FROM trkasir_detail
                    JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                    WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                    AND trkasir_detail.id_barang = a.id_barang
                ) AS q30,
                (
                    ROUND((
                        (
                            (SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                            JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                            WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                            AND trkasir_detail.id_barang = a.id_barang)/
                            (
                                (SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                                JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                                WHERE trkasir.tgl_trkasir BETWEEN '$tgl60' AND '$_GET[finish]'
                                AND trkasir_detail.id_barang = a.id_barang) -
                                (SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                                JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                                WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                                AND trkasir_detail.id_barang = a.id_barang)
                            )
                        )*100
                    )-100)
                )AS gr,
                a.sat_barang,
                a.hrgsat_barang,
                (a.hrgsat_barang * a.stok_barang) as nilai_barang
            FROM barang a
            WHERE a.kd_barang LIKE '%$search%'
                OR a.nm_barang LIKE '%$search%'
                OR a.stok_barang LIKE '%$search%'
                OR a.sat_barang LIKE '%$search%'
                OR a.hrgsat_barang LIKE '%$search%'
            ORDER BY $order $dir LIMIT $limit OFFSET $start");

        $querycount = $db->prepare("SELECT count(a.id_barang) as jumlah, a.kd_barang, a.nm_barang, a.stok_barang,
                (
                    SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                    JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                    WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                    AND trkasir_detail.id_barang = a.id_barang
                ) AS t30,
                (
                    (SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                    JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                    WHERE trkasir.tgl_trkasir BETWEEN '$tgl60' AND '$_GET[finish]'
                    AND trkasir_detail.id_barang = a.id_barang) -
                    (SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                    JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                    WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                    AND trkasir_detail.id_barang = a.id_barang)
                ) AS t60,
                (
                    SELECT SUM(trkasir_detail.qty_dtrkasir) FROM trkasir_detail
                    JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                    WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                    AND trkasir_detail.id_barang = a.id_barang
                ) AS q30,
                (
                    ROUND((
                        (
                            (SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                            JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                            WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                            AND trkasir_detail.id_barang = a.id_barang)/
                            (
                                (SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                                JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                                WHERE trkasir.tgl_trkasir BETWEEN '$tgl60' AND '$_GET[finish]'
                                AND trkasir_detail.id_barang = a.id_barang) -
                                (SELECT COUNT(trkasir_detail.id_dtrkasir) FROM trkasir_detail
                                JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                                WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                                AND trkasir_detail.id_barang = a.id_barang)
                            )
                        )*100
                    )-100)
                )AS gr,
                a.sat_barang,
                a.hrgsat_barang,
                (a.hrgsat_barang * a.stok_barang) as nilai_barang
            FROM barang a
            WHERE a.kd_barang LIKE '%$search%'
                OR a.nm_barang LIKE '%$search%'
                OR a.stok_barang LIKE '%$search%'
                OR a.sat_barang LIKE '%$search%'
                OR a.hrgsat_barang LIKE '%$search%'");

        $querycount->execute();
        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();
    if (!empty($query)) {
        $no = $start + 1;
        $query->execute();
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            $nestedData['no'] = $no;
            $nestedData['kd_barang'] = (string)$value['kd_barang'];
            $nestedData['nm_barang'] = $value['nm_barang'];
            $nestedData['stok_barang'] = $value['stok_barang'];
            $nestedData['t30'] = $value['t30'];
            $nestedData['t60'] = $value['t60'];
            $nestedData['gr'] = ($value['t60'] == 0) ? 0 : $value['gr'];
            $nestedData['q30'] = ($value['q30'] <= 0) ? 0 : $value['q30'];
            $nestedData['satuan'] = $value['sat_barang'];
            $nestedData['harga_beli'] = $value['hrgsat_barang'];
            $nestedData['nilai_barang'] = $value['nilai_barang'];
            $nestedData['kartu_stok'] = "<a href='?module=kartustok&act=view&id=$value[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a>";
            $data[] = $nestedData;
            $no++;
        }
    }

    $json_data = [
        "draw"              => intval($_POST['draw']),
        "recordsTotal"      => intval($totalData),
        "recordsFiltered"   => intval($totalFiltered),
        "totalStok"         => intval($totalNilaiStok),
        "data"              => $data
    ];

    echo json_encode($json_data);
}
