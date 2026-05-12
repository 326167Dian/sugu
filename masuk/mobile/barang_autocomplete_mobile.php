<?php
session_start();
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');

include "../../configurasi/koneksi.php";

if (empty($_SESSION['login']) || empty($_SESSION['idadmin'])) {
    echo json_encode(array('status' => 'error', 'items' => array(), 'message' => 'Unauthorized'));
    exit;
}

$db = isset($db) ? $db : null;
if (!$db instanceof PDO) {
    echo json_encode(array('status' => 'error', 'items' => array(), 'message' => 'Koneksi database tidak tersedia'));
    exit;
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$limit = 15;

if ($q === '' || mb_strlen($q) < 2) {
    echo json_encode(array('status' => 'ok', 'items' => array()));
    exit;
}

try {
    $stmt = $db->prepare("SELECT id_barang, kd_barang, nm_barang, sat_barang, stok_barang, hrgjual_barang, hrgjual_barang1, hrgjual_barang2, hrgsat_barang
                          FROM barang
                          WHERE nm_barang LIKE ?
                          ORDER BY nm_barang ASC
                          LIMIT " . (int)$limit);
    $stmt->execute(array('%' . $q . '%'));

    $items = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $harga = 0;
        if (isset($row['hrgjual_barang']) && (float)$row['hrgjual_barang'] > 0) {
            $harga = (float)$row['hrgjual_barang'];
        } elseif (isset($row['hrgjual_barang1']) && (float)$row['hrgjual_barang1'] > 0) {
            $harga = (float)$row['hrgjual_barang1'];
        } elseif (isset($row['hrgjual_barang2']) && (float)$row['hrgjual_barang2'] > 0) {
            $harga = (float)$row['hrgjual_barang2'];
        } elseif (isset($row['hrgsat_barang'])) {
            $harga = (float)$row['hrgsat_barang'];
        }

        $items[] = array(
            'id_barang' => (int)$row['id_barang'],
            'kd_barang' => (string)$row['kd_barang'],
            'nm_barang' => (string)$row['nm_barang'],
            'sat_barang' => isset($row['sat_barang']) ? (string)$row['sat_barang'] : '',
            'stok_barang' => isset($row['stok_barang']) ? (float)$row['stok_barang'] : 0,
            'harga_jual' => $harga,
        );
    }

    echo json_encode(array('status' => 'ok', 'items' => $items));
} catch (Exception $e) {
    echo json_encode(array('status' => 'error', 'items' => array(), 'message' => $e->getMessage()));
}
