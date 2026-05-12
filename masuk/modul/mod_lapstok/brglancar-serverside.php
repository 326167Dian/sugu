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
        7 => 'om30',
        8 => 'l30',
        9 => 'sat_barang',
        10 => 'hrgsat_barang',
        11 => 'nilai_barang',
        12 => 'kartu_stok'
    );

    $querycount = $db->prepare("SELECT * FROM trkasir_detail
        JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
        WHERE trkasir.tgl_trkasir BETWEEN ? AND ?
        GROUP BY trkasir_detail.kd_barang
        HAVING (COUNT(trkasir_detail.kd_barang) > 5 AND COUNT(trkasir_detail.kd_barang) < 11)");
    $querycount->execute([$_GET['start'], $_GET['finish']]);
    $datacount = $querycount->rowCount();

    $totalData = $datacount;

    $totalFiltered = $totalData;

    $limit = $_POST['length'];
    $start = $_POST['start'];
    $order = $columns[$_POST['order']['0']['column']];
    $dir = $_POST['order']['0']['dir'];

    if (empty($_POST['search']['value'])) {
        // $l30 = round($om30 - ($q30 * $hargabeli));
        $query = $db->prepare("SELECT *, 
                    COUNT(trkasir_detail.kd_barang) as t30,
                    SUM(trkasir_detail.qty_dtrkasir) as q30,
                    SUM(trkasir_detail.hrgttl_dtrkasir) as om30 FROM trkasir_detail
        JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
        WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
        GROUP BY trkasir_detail.kd_barang
        HAVING (COUNT(trkasir_detail.kd_barang) > 5 AND COUNT(trkasir_detail.kd_barang) < 11)
        ORDER BY $order $dir LIMIT $limit OFFSET $start");
    } else {
        $search = $_POST['search']['value'];
        $query = $db->prepare("SELECT *,
                    COUNT(trkasir_detail.kd_barang) as t30,
                    SUM(trkasir_detail.qty_dtrkasir) as q30,
                    SUM(trkasir_detail.hrgttl_dtrkasir) as om30 FROM trkasir_detail
        JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
        WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]' 
                    AND trkasir_detail.kd_barang LIKE '%$search%' 
                    OR trkasir_detail.nmbrg_dtrkasir LIKE '%$search%'
        GROUP BY trkasir_detail.kd_barang
        HAVING (COUNT(trkasir_detail.kd_barang) > 5 AND COUNT(trkasir_detail.kd_barang) < 11)
        ORDER BY $order $dir LIMIT $limit OFFSET $start");

        $querycount = $db->prepare("SELECT * FROM trkasir_detail
        JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
        WHERE trkasir.tgl_trkasir BETWEEN ? AND ? 
                        AND (trkasir_detail.kd_barang LIKE ? 
                        OR trkasir_detail.nmbrg_dtrkasir LIKE ?)
        GROUP BY trkasir_detail.kd_barang
        HAVING (COUNT(trkasir_detail.kd_barang) > 5 AND COUNT(trkasir_detail.kd_barang) < 11)
        ORDER BY $order $dir LIMIT $limit OFFSET $start");
        $querycount->execute([$_GET['start'], $_GET['finish'], "%$search%", "%$search%"]);

        $datacount = $querycount->rowCount();
        $totalFiltered = $datacount;
    }

    $totalOm30 = 0;
    $totalL30 = 0;
    $totalStok = 0;
    $querycount1 = $db->query("SELECT *,
        COUNT(trkasir_detail.kd_barang) as t30,
                    SUM(trkasir_detail.qty_dtrkasir) as q30,
                    SUM(trkasir_detail.hrgttl_dtrkasir) as om30 FROM trkasir_detail
        JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
        WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
        GROUP BY trkasir_detail.kd_barang
        HAVING (COUNT(trkasir_detail.kd_barang) > 5 AND COUNT(trkasir_detail.kd_barang) < 11)");
    $querycount1->execute();
    while ($value1 = $querycount1->fetch(PDO::FETCH_ASSOC)) {
        $om301 = $value1['om30'];
        $q301 = $value1['q30'];

        $getbrg1 = $db->prepare("SELECT SUM(hrgsat_barang * stok_barang) AS nilaibarang, SUM($om301 - ($q301*hrgsat_barang)) as l30 FROM barang WHERE id_barang = '$value1[id_barang]'");
        $getbrg1->execute();
        $brg1 = $getbrg1->fetch(PDO::FETCH_ASSOC);

        $totalOm30 += $om301;
        $totalL30 += $brg1['l30'];
        $totalStok += $brg1['nilaibarang'];
    }

    $data = array();
    if (!empty($query)) {
        $no = $start + 1;
        $query->execute();
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            $getbrg = $db->prepare("SELECT * FROM barang WHERE kd_barang = '$value[kd_barang]' ORDER BY id_barang");
            $getbrg->execute();
            $brg = $getbrg->fetch(PDO::FETCH_ASSOC);
            $hargabeli = $brg['hrgsat_barang'];
            $nilaibarang = $brg['hrgsat_barang'] * $brg['stok_barang'];

            $t30 = $value['t30'];
            $q30 = $value['q30'];
            $om30 = $value['om30'];
            $l30 = round($om30 - ($q30 * $hargabeli));

            $nestedData['no'] = $no;
            $nestedData['kd_barang'] = $value['kd_barang'];
            $nestedData['nm_barang'] = $value['nmbrg_dtrkasir'];
            $nestedData['stok_barang'] = $brg['stok_barang'];
            $nestedData['stok_buffer'] = $brg['stok_buffer'];
            $nestedData['t30'] = $t30;
            $nestedData['q30'] = $q30;
            $nestedData['om30'] = $om30;
            $nestedData['l30'] = $l30;
            $nestedData['satuan'] = $brg['sat_barang'];
            $nestedData['harga_beli'] = $hargabeli;
            $nestedData['nilai_barang'] = $nilaibarang;
            $nestedData['kartu_stok'] = "<a href='?module=lapstok&act=edit&id=$brg[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a>";
            $data[] = $nestedData;
            $no++;
        }
    }

    $json_data = [
        "draw"              => intval($_POST['draw']),
        "recordsTotal"      => intval($totalData),
        "recordsFiltered"   => intval($totalFiltered),
        "totalOm30"         => $totalOm30,
        "totalL30"          => $totalL30,
        "totalStok"         => $totalStok,
        "data"              => $data
    ];

    echo json_encode($json_data);
}
