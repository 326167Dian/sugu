<?php
session_start();
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['login']) || empty($_SESSION['idadmin'])) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Akses ditolak.'
    ));
    exit;
}

include "../../configurasi/koneksi.php";

if (!isset($db) || !($db instanceof PDO)) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Koneksi database tidak tersedia.'
    ));
    exit;
}

function mobileTableExistsAjax($db, $tableName)
{
    static $cache = array();
    $key = strtolower($tableName);
    if (isset($cache[$key])) {
        return $cache[$key];
    }

    try {
        $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
        $stmt->execute(array($tableName));
        $cache[$key] = ((int)$stmt->fetchColumn() > 0);
        return $cache[$key];
    } catch (Exception $e) {
        $cache[$key] = false;
        return false;
    }
}

function mobileColumnExistsAjax($db, $tableName, $columnName)
{
    static $cache = array();
    $key = strtolower($tableName . '.' . $columnName);
    if (isset($cache[$key])) {
        return $cache[$key];
    }

    try {
        $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
        $stmt->execute(array($tableName, $columnName));
        $cache[$key] = ((int)$stmt->fetchColumn() > 0);
        return $cache[$key];
    } catch (Exception $e) {
        $cache[$key] = false;
        return false;
    }
}

function mobileApproxCount($db, $tableName, $fallbackSql)
{
    if (mobileTableExistsAjax($db, $tableName)) {
        try {
            $stmt = $db->prepare("SELECT COALESCE(table_rows, 0) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1");
            $stmt->execute(array($tableName));
            $approx = (int)$stmt->fetchColumn();
            if ($approx > 0) {
                return $approx;
            }
        } catch (Exception $e) {
        }

        try {
            $stmt = $db->query($fallbackSql);
            return (int)$stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }

    return 0;
}

$totalSwamedikasiHariIni = 0;
$totalPelanggan = mobileApproxCount($db, 'pelanggan', "SELECT COUNT(*) FROM pelanggan");

$totalTransaksiHariIni = 0;
$totalOmzetHariIni = 0;
$referensiTanggalTransaksi = date('Y-m-d');
$totalBarangMacet = 0;
$topProduk = array();

if (mobileTableExistsAjax($db, 'riwayat_pelanggan')) {
    try {
        $fieldTanggalRiwayat = null;
        if (mobileColumnExistsAjax($db, 'riwayat_pelanggan', 'tgl')) {
            $fieldTanggalRiwayat = 'tgl';
        } elseif (mobileColumnExistsAjax($db, 'riwayat_pelanggan', 'created_at')) {
            $fieldTanggalRiwayat = 'created_at';
        }

        if ($fieldTanggalRiwayat !== null) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM riwayat_pelanggan WHERE " . $fieldTanggalRiwayat . " >= CURDATE() AND " . $fieldTanggalRiwayat . " < (CURDATE() + INTERVAL 1 DAY)");
            $stmt->execute();
            $totalSwamedikasiHariIni = (int)$stmt->fetchColumn();
        }
    } catch (Exception $e) {
        $totalSwamedikasiHariIni = 0;
    }
}

if (mobileTableExistsAjax($db, 'trkasir')) {
    try {
        $todayDate = date('Y-m-d');
        $hasTanggalTrkasir = mobileColumnExistsAjax($db, 'trkasir', 'tgl_trkasir');
        $hasWaktuTrx = mobileColumnExistsAjax($db, 'trkasir', 'waktu_trx');

        $dateExpression = null;
        if ($hasTanggalTrkasir && $hasWaktuTrx) {
            $dateExpression = "COALESCE(NULLIF(DATE(tgl_trkasir), '0000-00-00'), NULLIF(DATE(waktu_trx), '0000-00-00'))";
        } elseif ($hasTanggalTrkasir) {
            $dateExpression = "NULLIF(DATE(tgl_trkasir), '0000-00-00')";
        } elseif ($hasWaktuTrx) {
            $dateExpression = "NULLIF(DATE(waktu_trx), '0000-00-00')";
        }

        if ($dateExpression !== null) {
            $fieldOmzet = null;
            if (mobileColumnExistsAjax($db, 'trkasir', 'ttl_trkasir')) {
                $fieldOmzet = 'ttl_trkasir';
            } elseif (mobileColumnExistsAjax($db, 'trkasir', 'total')) {
                $fieldOmzet = 'total';
            }

            $sumExpression = $fieldOmzet !== null ? "COALESCE(SUM(" . $fieldOmzet . "), 0)" : "0";

            $sqlAgg = "SELECT COUNT(*) AS total_transaksi, " . $sumExpression . " AS total_omzet
                       FROM trkasir
                       WHERE " . $dateExpression . " = ?";
            $stmtAgg = $db->prepare($sqlAgg);
            $stmtAgg->execute(array($todayDate));
            $rowAgg = $stmtAgg->fetch(PDO::FETCH_ASSOC);

            if ($rowAgg) {
                $totalTransaksiHariIni = (int)$rowAgg['total_transaksi'];
                $totalOmzetHariIni = (float)round((float)$rowAgg['total_omzet']);
            }
        }
    } catch (Exception $e) {
        $totalTransaksiHariIni = 0;
        $totalOmzetHariIni = 0;
        $referensiTanggalTransaksi = date('Y-m-d');
    }
}

if (mobileTableExistsAjax($db, 'barang') && mobileTableExistsAjax($db, 'trkasir_detail') && mobileTableExistsAjax($db, 'trkasir')) {
    try {
        $finishDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-30 days', strtotime($finishDate)));

        $stmtMacet = $db->prepare("SELECT COUNT(b.id_barang) AS jumlah
                                   FROM barang b
                                   WHERE NOT EXISTS (
                                       SELECT d.kd_barang
                                       FROM trkasir_detail d
                                       JOIN trkasir t ON d.kd_trkasir = t.kd_trkasir
                                       WHERE d.id_barang = b.id_barang
                                         AND t.tgl_trkasir BETWEEN ? AND ?
                                   )");
        $stmtMacet->execute(array($startDate, $finishDate));
        $totalBarangMacet = (int)$stmtMacet->fetchColumn();
    } catch (Exception $e) {
        $totalBarangMacet = 0;
    }
}

if (mobileTableExistsAjax($db, 'trkasir_detail') && mobileTableExistsAjax($db, 'trkasir') && mobileTableExistsAjax($db, 'barang')) {
    try {
        $sqlTopProduk = "SELECT b.nm_barang, SUM(d.qty_dtrkasir) AS qty_total
                         FROM trkasir_detail d
                         JOIN trkasir t ON t.kd_trkasir = d.kd_trkasir
                         JOIN barang b ON b.id_barang = d.id_barang
                         WHERE t.tgl_trkasir >= CURDATE()
                           AND t.tgl_trkasir < (CURDATE() + INTERVAL 1 DAY)
                         GROUP BY b.id_barang, b.nm_barang
                         ORDER BY qty_total DESC
                         LIMIT 5";
        $stmtTopProduk = $db->query($sqlTopProduk);
        $topProduk = $stmtTopProduk->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $topProduk = array();
    }
}

echo json_encode(array(
    'status' => 'ok',
    'data' => array(
        'total_swamedikasi_hari_ini' => $totalSwamedikasiHariIni,
        'total_pelanggan' => $totalPelanggan,
        'total_transaksi_hari_ini' => $totalTransaksiHariIni,
        'total_omzet_hari_ini' => $totalOmzetHariIni,
        'referensi_tanggal_transaksi' => $referensiTanggalTransaksi,
        'total_barang_macet' => $totalBarangMacet,
        'top_produk' => $topProduk,
    )
));
