<?php
// Dipakai bersama oleh brglaku / brglancar / brgslow -serverside.php.
// Penjualan dalam rentang tanggal dihitung sekali per kd_barang (subquery),
// kd_num: barang.kd_barang BIGINT sedangkan trkasir_detail.kd_barang VARCHAR, disamakan ke angka agar join cepat.
// lalu di-join ke barang, supaya semua kolom bisa diurutkan dan tidak ada query per baris.

function kategori_barang_serverside($db, $minT30, $maxT30)
{
    $columns = array(
        0 => 'kd_barang',
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
        12 => 'kd_barang'
    );

    $limit = intval($_POST['length']);
    $start = intval($_POST['start']);
    $orderIdx = intval($_POST['order']['0']['column']);
    $order = isset($columns[$orderIdx]) ? $columns[$orderIdx] : 't30';
    $dir = (strtolower($_POST['order']['0']['dir']) == 'asc') ? 'ASC' : 'DESC';
    if ($limit < 1) {
        $limit = 10;
    }

    $tglStart = date('Y-m-d', strtotime($_GET['start']));
    $tglFinish = date('Y-m-d', strtotime($_GET['finish']));

    $having = "COUNT(d.kd_barang) >= " . intval($minT30);
    if ($maxT30 !== null) {
        $having .= " AND COUNT(d.kd_barang) <= " . intval($maxT30);
    }

    $penjualan = function ($searchWhere) use ($having) {
        return "SELECT d.kd_barang,
                MAX(CAST(d.kd_barang AS DECIMAL(20,0))) AS kd_num,
                MAX(d.nmbrg_dtrkasir) AS nmbrg,
                COUNT(d.kd_barang) AS t30,
                SUM(d.qty_dtrkasir) AS q30,
                SUM(d.hrgttl_dtrkasir) AS om30
            FROM trkasir_detail d
            JOIN trkasir t ON t.kd_trkasir = d.kd_trkasir
            WHERE t.tgl_trkasir BETWEEN :tgl_start AND :tgl_finish $searchWhere
            GROUP BY d.kd_barang
            HAVING $having";
    };

    $select = function ($searchWhere) use ($penjualan) {
        return "SELECT s.kd_barang,
                COALESCE(b.nm_barang, s.nmbrg) AS nm_barang,
                b.stok_barang,
                b.stok_buffer,
                s.t30,
                s.q30,
                s.om30,
                (s.om30 - (s.q30 * b.hrgsat_barang)) AS l30,
                b.sat_barang,
                b.hrgsat_barang,
                (b.hrgsat_barang * b.stok_barang) AS nilai_barang
            FROM (" . $penjualan($searchWhere) . ") s
            LEFT JOIN barang b ON b.kd_barang = s.kd_num";
    };

    $tglParams = array(':tgl_start' => $tglStart, ':tgl_finish' => $tglFinish);

    // Total seluruh kategori (tanpa filter pencarian)
    $qTotal = $db->prepare("SELECT COUNT(*) AS jumlah,
            SUM(x.om30) AS totalOm30,
            SUM(x.l30) AS totalL30,
            SUM(x.nilai_barang) AS totalStok
        FROM (" . $select("") . ") x");
    $qTotal->execute($tglParams);
    $total = $qTotal->fetch(PDO::FETCH_ASSOC);

    $totalData = $total['jumlah'];
    $totalFiltered = $totalData;

    $searchWhere = "";
    $params = $tglParams;
    if (!empty($_POST['search']['value'])) {
        $searchWhere = "AND (d.kd_barang LIKE :s1 OR d.nmbrg_dtrkasir LIKE :s2)";
        $search = '%' . $_POST['search']['value'] . '%';
        $params[':s1'] = $search;
        $params[':s2'] = $search;

        $qCount = $db->prepare("SELECT COUNT(*) FROM (" . $penjualan($searchWhere) . ") x");
        $qCount->execute($params);
        $totalFiltered = $qCount->fetchColumn();
    }

    $query = $db->prepare($select($searchWhere) . " ORDER BY $order $dir, s.kd_barang LIMIT $limit OFFSET $start");
    $query->execute($params);

    $data = array();
    $no = $start + 1;
    while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
        $nestedData['no'] = $no;
        $nestedData['kd_barang'] = $value['kd_barang'];
        $nestedData['nm_barang'] = $value['nm_barang'];
        $nestedData['stok_barang'] = $value['stok_barang'];
        $nestedData['stok_buffer'] = $value['stok_buffer'];
        $nestedData['t30'] = intval($value['t30']);
        $nestedData['q30'] = $value['q30'];
        $nestedData['om30'] = $value['om30'];
        $nestedData['l30'] = round($value['l30']);
        $nestedData['satuan'] = $value['sat_barang'];
        $nestedData['harga_beli'] = $value['hrgsat_barang'];
        $nestedData['nilai_barang'] = $value['nilai_barang'];
        $nestedData['kartu_stok'] = "<a href='?module=lapstok&act=edit&id=$value[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a>";
        $data[] = $nestedData;
        $no++;
    }

    return [
        "draw"              => intval($_POST['draw']),
        "recordsTotal"      => intval($totalData),
        "recordsFiltered"   => intval($totalFiltered),
        "totalOm30"         => floatval($total['totalOm30']),
        "totalL30"          => floatval($total['totalL30']),
        "totalStok"         => floatval($total['totalStok']),
        "data"              => $data
    ];
}
