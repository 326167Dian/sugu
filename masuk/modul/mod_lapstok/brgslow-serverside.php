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
        HAVING (COUNT(trkasir_detail.kd_barang) > 0 AND COUNT(trkasir_detail.kd_barang) < 6)");
    $querycount->execute([$_GET['start'], $_GET['finish']]);
    $datacount = $querycount->rowCount();

    $totalData = $datacount;

    $totalFiltered = $totalData;

    $limit = $_POST['length'];
    $start = $_POST['start'];
    $order = $columns[$_POST['order']['0']['column']];
    $dir = $_POST['order']['0']['dir'];

    if (empty($_POST['search']['value'])) {
        $stmt = $db->prepare("SELECT *, 
                    COUNT(trkasir_detail.kd_barang) as t30,
                    SUM(trkasir_detail.qty_dtrkasir) as q30,
                    SUM(trkasir_detail.hrgttl_dtrkasir) as om30 FROM trkasir_detail
        JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
        WHERE trkasir.tgl_trkasir BETWEEN ? AND ?
        GROUP BY trkasir_detail.kd_barang
        HAVING (COUNT(trkasir_detail.kd_barang) > 0 AND COUNT(trkasir_detail.kd_barang) < 6)
        ORDER BY $order $dir LIMIT $limit OFFSET $start");
        $stmt->execute([$_GET['start'], $_GET['finish']]);
        $query = $stmt;
    } else {
        $search = $_POST['search']['value'];
        $stmt = $db->prepare("SELECT *,
                    COUNT(trkasir_detail.kd_barang) as t30,
                    SUM(trkasir_detail.qty_dtrkasir) as q30,
                    SUM(trkasir_detail.hrgttl_dtrkasir) as om30 FROM trkasir_detail
        JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
        WHERE trkasir.tgl_trkasir BETWEEN ? AND ? 
                    AND (trkasir_detail.kd_barang LIKE ? 
                    OR trkasir_detail.nmbrg_dtrkasir LIKE ?)
        GROUP BY trkasir_detail.kd_barang
        HAVING (COUNT(trkasir_detail.kd_barang) > 0 AND COUNT(trkasir_detail.kd_barang) < 6)
        ORDER BY $order $dir LIMIT $limit OFFSET $start");
        $stmt->execute([$_GET['start'], $_GET['finish'], '%'.$search.'%', '%'.$search.'%']);
        $query = $stmt;

        $stmt_count = $db->prepare("SELECT COUNT(*) as total FROM (SELECT 1 FROM trkasir_detail
        JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
        WHERE trkasir.tgl_trkasir BETWEEN ? AND ? 
                        AND (trkasir_detail.kd_barang LIKE ? 
                        OR trkasir_detail.nmbrg_dtrkasir LIKE ?)
        GROUP BY trkasir_detail.kd_barang
        HAVING (COUNT(trkasir_detail.kd_barang) > 0 AND COUNT(trkasir_detail.kd_barang) < 6)) as sub");
        $stmt_count->execute([$_GET['start'], $_GET['finish'], '%'.$search.'%', '%'.$search.'%']);
        $totalFiltered = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
    }

    $totalOm30 = 0;
    $totalL30 = 0;
    $totalStok = 0;
    $stmt_count1 = $db->prepare("SELECT trkasir_detail.id_barang,
                    SUM(trkasir_detail.qty_dtrkasir) as q30,
                    SUM(trkasir_detail.hrgttl_dtrkasir) as om30 FROM trkasir_detail
        JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
        WHERE trkasir.tgl_trkasir BETWEEN ? AND ?
        GROUP BY trkasir_detail.kd_barang
        HAVING (COUNT(trkasir_detail.kd_barang) > 0 AND COUNT(trkasir_detail.kd_barang) < 6)");
    $stmt_count1->execute([$_GET['start'], $_GET['finish']]);
    $querycount1 = $stmt_count1;

    while ($value1 = $querycount1->fetch(PDO::FETCH_ASSOC)) {
        $om301 = $value1['om30'];
        $q301 = $value1['q30'];

        $stmt_brg1 = $db->prepare("SELECT SUM(hrgsat_barang * stok_barang) AS nilaibarang, SUM(? - (?*hrgsat_barang)) as l30 FROM barang WHERE id_barang = ?");
        $stmt_brg1->execute([$om301, $q301, $value1['id_barang']]);
        $brg1 = $stmt_brg1->fetch(PDO::FETCH_ASSOC);

        $totalOm30 += $om301;
        $totalL30 += $brg1['l30'];
        $totalStok += $brg1['nilaibarang'];
    }

    $data = array();
    if (!empty($query)) {
        $no = $start + 1;
        
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            $getbrg = $db->prepare("SELECT * FROM barang WHERE id_barang = '$value[id_barang]' ORDER BY id_barang");
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
