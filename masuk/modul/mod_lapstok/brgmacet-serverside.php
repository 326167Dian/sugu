<?php
include_once '../../../configurasi/koneksi.php';

// Barang MACET: tidak ada transaksi penjualan dalam periode.
// Daftar barang yang terjual dihitung sekali (subquery), lalu barang yang tidak ada di daftar itu diambil.
// (Dulu memakai NOT EXISTS per baris + query per baris, sangat lambat karena
//  barang.kd_barang BIGINT vs trkasir_detail.kd_barang VARCHAR tidak bisa memakai index.)
if ($_GET['action'] == "table_data") {

    $columns = array(
        0 => 'id_barang',
        1 => 'kd_barang',
        2 => 'nm_barang',
        3 => 'stok_barang',
        4 => 'stok_buffer',
        5 => 'id_barang',
        6 => 'id_barang',
        7 => 'sat_barang',
        8 => 'hrgsat_barang',
        9 => 'nilai_barang',
        10 => 'kd_barang'
    );

    $limit = intval($_POST['length']);
    $start = intval($_POST['start']);
    $orderIdx = intval($_POST['order']['0']['column']);
    $order = isset($columns[$orderIdx]) ? $columns[$orderIdx] : 'nm_barang';
    $dir = (strtolower($_POST['order']['0']['dir']) == 'asc') ? 'ASC' : 'DESC';
    if ($limit < 1) {
        $limit = 10;
    }

    $tglStart = date('Y-m-d', strtotime($_GET['start']));
    $tglFinish = date('Y-m-d', strtotime($_GET['finish']));
    $params = array(':tgl_start' => $tglStart, ':tgl_finish' => $tglFinish);

    $fromMacet = "FROM barang b
        LEFT JOIN (
            SELECT DISTINCT CAST(d.kd_barang AS DECIMAL(20,0)) AS kd_num
            FROM trkasir_detail d
            JOIN trkasir t ON t.kd_trkasir = d.kd_trkasir
            WHERE t.tgl_trkasir BETWEEN :tgl_start AND :tgl_finish
        ) s ON s.kd_num = b.kd_barang
        WHERE s.kd_num IS NULL";

    $querycount = $db->prepare("SELECT COUNT(b.id_barang) AS jumlah,
            SUM(b.hrgsat_barang * b.stok_barang) AS totalNilaiStok
        $fromMacet");
    $querycount->execute($params);
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalStok = $datacount['totalNilaiStok'];
    $totalData = $datacount['jumlah'];
    $totalFiltered = $totalData;

    $searchWhere = "";
    if (!empty($_POST['search']['value'])) {
        $searchWhere = "AND (b.kd_barang LIKE :s1 OR b.nm_barang LIKE :s2)";
        $search = '%' . $_POST['search']['value'] . '%';
        $params[':s1'] = $search;
        $params[':s2'] = $search;

        $querycount = $db->prepare("SELECT COUNT(b.id_barang) AS jumlah $fromMacet $searchWhere");
        $querycount->execute($params);
        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = $datacount['jumlah'];
    }

    $query = $db->prepare("SELECT b.id_barang,
            b.kd_barang,
            b.nm_barang,
            b.stok_barang,
            b.stok_buffer,
            b.sat_barang,
            b.hrgsat_barang,
            (b.hrgsat_barang * b.stok_barang) AS nilai_barang
        $fromMacet $searchWhere
        ORDER BY $order $dir LIMIT $limit OFFSET $start");
    $query->execute($params);

    $data = array();
    $no = $start + 1;
    while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
        $nestedData['no'] = $no;
        $nestedData['kd_barang'] = $value['kd_barang'];
        $nestedData['nm_barang'] = $value['nm_barang'];
        $nestedData['stok_barang'] = $value['stok_barang'];
        $nestedData['stok_buffer'] = $value['stok_buffer'];
        // Barang macet pasti tidak punya penjualan dalam periode
        $nestedData['t30'] = 0;
        $nestedData['q30'] = 0;
        $nestedData['satuan'] = $value['sat_barang'];
        $nestedData['harga_beli'] = $value['hrgsat_barang'];
        $nestedData['nilai_barang'] = $value['nilai_barang'];
        $nestedData['kartu_stok'] = "<a href='?module=lapstok&act=edit&id=$value[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a>";
        $data[] = $nestedData;
        $no++;
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
