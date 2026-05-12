<?php
include_once '../../../configurasi/koneksi.php';

if (!isset($_GET['action']) || $_GET['action'] !== 'table_data') {
    exit;
}

if (ob_get_length()) {
    ob_clean();
}
ob_start();

header('Content-Type: application/json; charset=utf-8');

try {
    $columns = [
        0 => 'id_pelanggan',
        1 => 'nm_pelanggan',
        2 => 'tlp_pelanggan',
        3 => 'id_pelanggan'
    ];

    $draw = isset($_POST['draw']) ? (int) $_POST['draw'] : 0;
    $limit = isset($_POST['length']) ? (int) $_POST['length'] : 10;
    $start = isset($_POST['start']) ? (int) $_POST['start'] : 0;

    if ($limit < 1) {
        $limit = 10;
    }

    $orderIndex = isset($_POST['order'][0]['column']) ? (int) $_POST['order'][0]['column'] : 0;
    $order = isset($columns[$orderIndex]) ? $columns[$orderIndex] : 'id_pelanggan';

    $dir = isset($_POST['order'][0]['dir']) ? strtolower($_POST['order'][0]['dir']) : 'asc';
    if ($dir !== 'asc' && $dir !== 'desc') {
        $dir = 'asc';
    }

    $search = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';

    $querycount = $db->prepare('SELECT COUNT(id_pelanggan) AS jumlah FROM pelanggan');
    $querycount->execute();
    $datacount = $querycount->fetch(PDO::FETCH_ASSOC);

    $totalData = isset($datacount['jumlah']) ? (int) $datacount['jumlah'] : 0;
    $totalFiltered = $totalData;

    if ($search === '') {
        $query = $db->prepare("SELECT * FROM pelanggan ORDER BY {$order} {$dir} LIMIT :limit OFFSET :start");
        $query->bindValue(':limit', $limit, PDO::PARAM_INT);
        $query->bindValue(':start', $start, PDO::PARAM_INT);
    } else {
        $query = $db->prepare("SELECT *
            FROM pelanggan
            WHERE nm_pelanggan LIKE :search OR tlp_pelanggan LIKE :search
            ORDER BY {$order} {$dir}
            LIMIT :limit OFFSET :start");
        $query->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        $query->bindValue(':limit', $limit, PDO::PARAM_INT);
        $query->bindValue(':start', $start, PDO::PARAM_INT);

        $querycount = $db->prepare('SELECT COUNT(id_pelanggan) AS jumlah
            FROM pelanggan
            WHERE nm_pelanggan LIKE :search OR tlp_pelanggan LIKE :search');
        $querycount->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        $querycount->execute();
        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
        $totalFiltered = isset($datacount['jumlah']) ? (int) $datacount['jumlah'] : 0;
    }

    $data = [];
    $no = $start + 1;
    $query->execute();

    while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
        $idPelanggan = htmlspecialchars((string) ($value['id_pelanggan'] ?? ''), ENT_QUOTES, 'UTF-8');
        $namaPelanggan = htmlspecialchars((string) ($value['nm_pelanggan'] ?? ''), ENT_QUOTES, 'UTF-8');
        $telpPelanggan = htmlspecialchars((string) ($value['tlp_pelanggan'] ?? ''), ENT_QUOTES, 'UTF-8');
        $alamatPelanggan = htmlspecialchars((string) ($value['alamat_pelanggan'] ?? ''), ENT_QUOTES, 'UTF-8');
        $ttlPoin = htmlspecialchars((string) (array_key_exists('ttl_poin', $value) ? $value['ttl_poin'] : 0), ENT_QUOTES, 'UTF-8');

        $nestedData = [];
        $nestedData['no'] = $no;
        $nestedData['nm_pelanggan'] = $value['nm_pelanggan'] ?? '';
        $nestedData['tlp_pelanggan'] = $value['tlp_pelanggan'] ?? '';
        $nestedData['pilih'] = "<button class='btn btn-xs btn-info' id='pilihpelanggan'"
            . " data-id_pelanggan='" . $idPelanggan . "'"
            . " data-nm_pelanggan='" . $namaPelanggan . "'"
            . " data-tlp_pelanggan='" . $telpPelanggan . "'"
            . " data-alamat_pelanggan='" . $alamatPelanggan . "'"
            . " data-ttl_pelanggan='" . $ttlPoin . "'"
            . "><i class='fa fa-check'></i></button>";

        $data[] = $nestedData;
        $no++;
    }

    $json_data = [
        'draw' => $draw,
        'recordsTotal' => $totalData,
        'recordsFiltered' => $totalFiltered,
        'data' => $data
    ];

    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode($json_data);
} catch (Exception $e) {
    http_response_code(500);
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode([
        'draw' => isset($_POST['draw']) ? (int) $_POST['draw'] : 0,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Gagal memuat data member.'
    ]);
}
