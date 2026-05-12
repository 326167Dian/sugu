<?php
include_once '../../../configurasi/koneksi.php';

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

    $querycount = $db->prepare("SELECT count(id_barang) as jumlah, SUM(hrgsat_barang*stok_barang) as totalNilaiStok FROM barang");
    $querycount->execute();
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalData = $datacount['jumlah'];

    $totalFiltered = $totalData;
    $totalNilaiStok = $datacount['totalNilaiStok'];

    $limit = $_POST['length'];
    $start = $_POST['start'];
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
        // $query = $db->query("SELECT id_barang,
        //                             kd_barang,
        //                             nm_barang,
        //                             stok_barang,
        //                             sat_barang,
        //                             jenisobat,
        //                             hrgsat_barang,
        //                             hrgjual_barang,
        //                             indikasi
        //     FROM barang ORDER BY $order $dir LIMIT $limit OFFSET $start");
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
        // $query = $db->query("SELECT id_barang,
        //                             kd_barang,
        //                             nm_barang,
        //                             stok_barang,
        //                             sat_barang,
        //                             jenisobat,
        //                             hrgsat_barang,
        //                             hrgjual_barang,
        //                             indikasi 
        //     FROM barang WHERE kd_barang LIKE '%$search%' 
        //                 OR nm_barang LIKE '%$search%'
        //                 OR stok_barang LIKE '%$search%'
        //                 OR sat_barang LIKE '%$search%'
        //                 OR jenisobat LIKE '%$search%'
        //                 OR hrgsat_barang LIKE '%$search%'
        //                 OR hrgjual_barang LIKE '%$search%'
        //                 OR indikasi LIKE '%$search%' 
        //     ORDER BY $order $dir LIMIT $limit OFFSET $start");

        // $querycount = $db->query("SELECT count(id_barang) as jumlah 
        //     FROM barang WHERE kd_barang LIKE '%$search%' 
        //                 OR nm_barang LIKE '%$search%'
        //                 OR stok_barang LIKE '%$search%'
        //                 OR sat_barang LIKE '%$search%'
        //                 OR jenisobat LIKE '%$search%'
        //                 OR hrgsat_barang LIKE '%$search%'
        //                 OR hrgjual_barang LIKE '%$search%'
        //                 OR indikasi LIKE '%$search%'");
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
    // $totalNilaiBarang = 0;
    if (!empty($query)) {
        $no = $start + 1;
        $query->execute();
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            // $tgl60 = date('Y-m-d', strtotime('-30 days', strtotime($_GET['start'])));

            // $pass = $db->query("SELECT count(id_dtrkasir) AS jumlah,
            //         SUM(trkasir_detail.qty_dtrkasir) as pw
            //     FROM trkasir_detail JOIN trkasir
            //     ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir 
            //     WHERE trkasir_detail.id_barang = '$value[id_barang]'
            //     AND trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'");
            // $pass1 = $pass->fetch_array();

            // $pass60 = $db->query("SELECT count(id_dtrkasir) AS jumlah FROM trkasir_detail JOIN trkasir
            //     ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir 
            //     WHERE trkasir_detail.id_barang = '$value[id_barang]'
            //     AND trkasir.tgl_trkasir BETWEEN '$tgl60' AND '$_GET[finish]'");
            // $pass4 = $pass60->fetch_array();

            // $pass5 = $pass4['jumlah'] - $pass1['jumlah'];
            // $pass6 = ($pass5 == 0) ? 0 : intval(round(($pass1['jumlah'] / $pass5 * 100) - 100));

            // $hargabeli = $value['hrgsat_barang'];
            // $nilaibarang = $value['hrgsat_barang'] * $value['stok_barang'];

            // for column
            // $nestedData['no'] = $no;
            // $nestedData['kd_barang'] = $value['kd_barang'];
            // $nestedData['nm_barang'] = $value['nm_barang'];
            // $nestedData['stok_barang'] = $value['stok_barang'];
            // $nestedData['t30'] = $pass1['jumlah'];
            // $nestedData['t60'] = $pass5;
            // $nestedData['gr'] = $pass6;
            // $nestedData['q30'] = (($pass1['pw'] <= 0) ? 0 : $pass1['pw']);
            // $nestedData['satuan'] = $value['sat_barang'];
            // $nestedData['harga_beli'] = $hargabeli;
            // $nestedData['nilai_barang'] = $nilaibarang;
            // $nestedData['kartu_stok'] = "<a href='?module=lapstok&act=edit&id=$value[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a>";
            // $data[] = $nestedData;
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
            $nestedData['kartu_stok'] = "<a href='?module=lapstok&act=edit&id=$value[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a>
            <a href='?module=lapstok&act=order&id=$value[kd_barang]' title='Riwayat' class='btn btn-info btn-xs'>Riwayat Pesanan</a>";
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
