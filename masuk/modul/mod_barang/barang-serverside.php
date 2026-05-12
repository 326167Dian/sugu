<?php
include_once '../../../configurasi/koneksi.php';
include_once '../../../configurasi/fungsi_rupiah.php';

$aksi = "modul/mod_barang/aksi_barang.php";

if ($_GET['action'] == "table_data") {

    $columns = array(
        0 => 'id_barang',
        1 => 'nm_barang',
        2 => 'jenisobat',
        3 => 'stok_barang',
        4 => 'hrgjual_barang',
        5 => 'zataktif',
        6 => 'indikasi',
        7 => 'id_barang'
    );

    $querycount = $db->query("SELECT count(id_barang) as jumlah FROM barang");
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalData = $datacount['jumlah'];

    $totalFiltered = $totalData;

    $limit = $_POST['length'];
    $start = $_POST['start'];
    $order = $columns[$_POST['order']['0']['column']];
    $dir = $_POST['order']['0']['dir'];

    if (empty($_POST['search']['value'])) {
        $query = $db->query("SELECT id_barang,
                                    kd_barang,
                                    nm_barang,
                                    stok_barang,
                                    sat_barang,
                                    jenisobat,
                                    hrgsat_barang,
                                    hrgjual_barang,
                                    hrgjual_barang1,
                                    hrgjual_barang2,
                                    zataktif,
                                    indikasi,
                                    updated_by
            FROM barang ORDER BY $order $dir LIMIT $limit OFFSET $start");
    } else {
        $search = $_POST['search']['value'];
        $query = $db->query("SELECT id_barang,
                                    kd_barang,
                                    nm_barang,
                                    stok_barang,
                                    sat_barang,
                                    jenisobat,
                                    hrgsat_barang,
                                    hrgjual_barang,
                                    hrgjual_barang1,
                                    hrgjual_barang2,
                                    zataktif,
                                    indikasi,
                                    updated_by 
            FROM barang WHERE kd_barang LIKE '%$search%' 
                        OR nm_barang LIKE '%$search%'
                        OR jenisobat LIKE '%$search%'
                        OR stok_barang LIKE '%$search%'
                        OR sat_barang LIKE '%$search%'
                        OR updated_by LIKE '%$search%'
                        OR hrgsat_barang LIKE '%$search%'
                        OR hrgjual_barang LIKE '%$search%'
                        OR zataktif LIKE '%$search%'
                        OR indikasi LIKE '%$search%' 
            ORDER BY $order $dir LIMIT $limit OFFSET $start");

        $querycount = $db->query("SELECT count(id_barang) as jumlah 
            FROM barang WHERE kd_barang LIKE '%$search%' 
                        OR nm_barang LIKE '%$search%'
                        OR jenisobat LIKE '%$search%'
                        OR stok_barang LIKE '%$search%'
                        OR sat_barang LIKE '%$search%'
                        OR updated_by LIKE '%$search%'
                        OR hrgsat_barang LIKE '%$search%'
                        OR hrgjual_barang LIKE '%$search%'
                        OR zataktif LIKE '%$search%'
                        OR indikasi LIKE '%$search%'");

        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = $datacount['jumlah'];
    }

    $data = array();
    if (!empty($query)) {
        $no = $start + 1;
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            $nestedData['no']               = $no;
            $nestedData['id_barang']        = $value['id_barang'];
            $nestedData['nm_barang']        = $value['nm_barang'] . ' <span style="color: #666;">(' . $value['kd_barang'] . ')</span>';
            $nestedData['jenisobat_value']  = isset($value['jenisobat']) ? trim($value['jenisobat']) : '';
            $nestedData['jenisobat']        = $nestedData['jenisobat_value'] !== '' ? htmlspecialchars($nestedData['jenisobat_value'], ENT_QUOTES, 'UTF-8') : '-';
            $nestedData['updated_by']       = $value['updated_by'];
            $nestedData['stok_barang']      = $value['stok_barang'].' '.$value['sat_barang'];
            $nestedData['sat_barang']       = $value['sat_barang'];
            $nestedData['hrgsat_barang']    = $value['hrgsat_barang'];
            $nestedData['hrgjual_barang_reguler']   = $value['hrgjual_barang'];
            $nestedData['hrgjual_barang']   = '<table><tr>
                                                <td><b>(R)</b> </td><td>'.format_rupiah($value['hrgjual_barang']).'</td>
                                            </tr>
                                            <tr>
                                                <td><b>(Re)</b> </td><td>'.format_rupiah($value['hrgjual_barang1']).'</td>
                                            </tr>
                                            <tr>
                                                <td><b>(Mp)</b> </td><td>'.format_rupiah($value['hrgjual_barang2']).'</td>
                                            </tr></table>';
            
            // Menampilkan zataktif dengan nama admin di dalam kurung
            $zataktif_display = $value['zataktif'];
            if (!empty($value['updated_by'])) {
                $zataktif_display .= ' <span style="color: #999; font-size: 0.9em;">(' . $value['updated_by'] . ')</span>';
            }
            $nestedData['zataktif']         = $zataktif_display;
            $nestedData['indikasi']         = $value['indikasi'];
                        $nestedData['aksi']             = "<div class='dropdown' style='white-space:nowrap; display:inline-block;'>
    <button class='btn btn-default dropdown-toggle' type='button' id='dropdownMenuAksi" . $value['id_barang'] . "' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true' style='white-space:nowrap;'>
        action
        <span class='caret'></span>
    </button>
                <ul class='dropdown-menu' aria-labelledby='dropdownMenuAksi" . $value['id_barang'] . "' style='min-width:165px; padding:6px 6px; left:0; right:auto;'>
                    <li style='margin:0 0 4px 0; background:transparent; text-align:left;'><a href='?module=barang&act=edit&id=" . $value['id_barang'] . "' style='display:block; width:70%; margin:0; box-sizing:border-box; padding:4px 8px; background-color:yellow; color:#555; white-space:nowrap;'>EDIT</a></li>
                    <li style='margin:0 0 4px 0; background:transparent; text-align:left;'><a href='?module=barang&act=detail&id=" . $value['id_barang'] . "' style='display:block; width:70%; margin:0; box-sizing:border-box; padding:4px 8px; background-color:aqua; color:#555; white-space:nowrap;'>DETAIL</a></li>
                    <li style='margin:0 0 4px 0; background:transparent; text-align:left;'><a href='#' class='btn-print-barcode' data-id='" . $value['id_barang'] . "' style='display:block; width:70%; margin:0; box-sizing:border-box; padding:4px 8px; background-color:#d9edf7; color:#555; white-space:nowrap;'>PRINT BARCODE</a></li>
                    <li style='margin:0 0 4px 0; background:transparent; text-align:left;'><a href='?module=kartustok&act=view&id=" . $value['kd_barang'] . "' style='display:block; width:70%; margin:0; box-sizing:border-box; padding:4px 8px; background-color:pink; color:#555; white-space:nowrap;'>KARTU STOK</a></li>
                    <li style='margin:0; background:transparent; text-align:left;'><a href=javascript:confirmdelete('" . $aksi . "?module=barang&act=hapus&id=" . $value['id_barang'] . "') style='display:block; width:70%; margin:0; box-sizing:border-box; padding:4px 8px; background-color:red; color:#fff; white-space:nowrap;'>HAPUS</a></li>
    </ul>
</div>";
            $data[] = $nestedData;
            $no++;
        }
    }

    $json_data = [
        "draw"            => intval($_POST['draw']),
        "recordsTotal"    => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data"            => $data
    ];

    echo json_encode($json_data);
}
