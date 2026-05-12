<?php
include_once '../../../configurasi/koneksi.php';

if ($_GET['action'] == "table_data") {

    $columns = array(
        0 => 'id_barang',
        1 => 'kd_barang',
        2 => 'nm_barang',
        3 => 'stok_barang',
        4 => 'satuan',
        5 => 't30',
        6 => 'q30',
        7 => 'sf',
        8 => 'harga_beli',
        9 => 'id_barang'
    );

    $querycount = $db->query("SELECT count(id_barang) as jumlah FROM barang_supplier WHERE id_supplier='$_GET[id]'");
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalData = $datacount['jumlah'];

    $totalFiltered = $totalData;

    $limit = $_POST['length'];
    $start = $_POST['start'];
    $order = $columns[$_POST['order']['0']['column']];
    $dir = $_POST['order']['0']['dir'];

    if (empty($_POST['search']['value'])) {
        $query = $db->query("SELECT 
                barang.*,
                (
                    SELECT COUNT(trkasir_detail.id_dtrkasir) 
                    FROM trkasir_detail
                    JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                    WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                    AND trkasir_detail.id_barang = barang_supplier.id_barang
                ) AS t30,
                (
                    SELECT SUM(trkasir_detail.qty_dtrkasir) 
                    FROM trkasir_detail
                    JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                    WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                    AND trkasir_detail.id_barang = barang_supplier.id_barang
                ) AS q30,
                (
                    (
                        SELECT SUM(trkasir_detail.qty_dtrkasir) 
                        FROM trkasir_detail
                        JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                        WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                        AND trkasir_detail.id_barang = barang_supplier.id_barang
                    )-barang.stok_barang
                ) AS sf
            FROM barang_supplier
            JOIN barang ON barang_supplier.id_barang = barang.id_barang
            WHERE barang_supplier.id_supplier = " . $_GET['id'] . "
            ORDER BY $order $dir LIMIT $limit OFFSET $start");
    } else {
        $search = $_POST['search']['value'];
        $query = $db->query("SELECT 
                barang.*,
                (
                    SELECT COUNT(trkasir_detail.id_dtrkasir) 
                    FROM trkasir_detail
                    JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                    WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                    AND trkasir_detail.id_barang = barang_supplier.id_barang
                ) AS t30,
                (
                    SELECT SUM(trkasir_detail.qty_dtrkasir) 
                    FROM trkasir_detail
                    JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                    WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                    AND trkasir_detail.id_barang = barang_supplier.id_barang
                ) AS q30,
                (
                    (
                        SELECT SUM(trkasir_detail.qty_dtrkasir) 
                        FROM trkasir_detail
                        JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                        WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                        AND trkasir_detail.id_barang = barang_supplier.id_barang
                    )-barang.stok_barang
                ) AS sf
            FROM barang_supplier
            JOIN barang ON barang_supplier.id_barang = barang.id_barang
            WHERE barang_supplier.id_supplier = " . $_GET['id'] . " 
                        AND kd_barang LIKE '%$search%' 
                        OR nm_barang LIKE '%$search%'
                        OR stok_barang LIKE '%$search%'
                        OR sat_barang LIKE '%$search%'
                        OR hrgsat_barang LIKE '%$search%'
            ORDER BY $order $dir LIMIT $limit OFFSET $start");

        $querycount = $db->query("SELECT 
                        barang.*,
                        (
                            SELECT COUNT(trkasir_detail.id_dtrkasir) 
                            FROM trkasir_detail
                            JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                            WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                            AND trkasir_detail.id_barang = barang_supplier.id_barang
                        ) AS t30,
                        (
                            SELECT SUM(trkasir_detail.qty_dtrkasir) 
                            FROM trkasir_detail
                            JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                            WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                            AND trkasir_detail.id_barang = barang_supplier.id_barang
                        ) AS q30,
                        (
                            (
                                SELECT SUM(trkasir_detail.qty_dtrkasir) 
                                FROM trkasir_detail
                                JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                                WHERE trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
                                AND trkasir_detail.id_barang = barang_supplier.id_barang
                            )-barang.stok_barang
                        ) AS sf
                    FROM barang_supplier
                    JOIN barang ON barang_supplier.id_barang = barang.id_barang
                    WHERE barang_supplier.id_supplier = " . $_GET['id'] . "
                        AND kd_barang LIKE '%$search%' 
                        OR nm_barang LIKE '%$search%'
                        OR stok_barang LIKE '%$search%'
                        OR sat_barang LIKE '%$search%'
                        OR hrgsat_barang LIKE '%$search%'");

        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();
    // $totalNilaiBarang = 0;
    if (!empty($query)) {
        $no = $start + 1;
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            $hargabeli = $value['hrgsat_barang'];

            // for column
            $nestedData['no'] = $no;
            $nestedData['kd_barang'] = (string)$value['kd_barang'];
            $nestedData['nm_barang'] = $value['nm_barang'];
            $nestedData['stok_barang'] = $value['stok_barang'];
            $nestedData['satuan'] = $value['sat_barang'];
            $nestedData['t30'] = $value['t30'];
            $nestedData['q30'] = (($value['q30'] <= 0) ? 0 : $value['q30']);
            $nestedData['sf'] = $value['sf'];
            $nestedData['harga_beli'] = $hargabeli;
            $nestedData['pilih'] = "<button class='btn btn-xs btn-info' id='pilihbarang' 
            data-id_barang='$value[id_barang]'
            data-kd_barang='$value[kd_barang]'
            data-nm_barang='$value[nm_barang]'
            data-stok_barang='$value[stok_barang]'
            data-sat_barang='$value[sat_barang]'
            data-hrgsat_barang='$value[hrgsat_barang]'
            data-sf_barang='$value[sf]'
            >
            <i class='fa fa-check'></i>
            </button>";
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
