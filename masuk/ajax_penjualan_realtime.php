<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['namauser']) || empty($_SESSION['passuser'])) {
    if (empty($_SESSION['username']) || empty($_SESSION['login'])) {
        http_response_code(401);
        echo json_encode([
            'status' => false,
            'message' => 'Sesi login tidak valid'
        ]);
        exit;
    }
}

if (empty($_SESSION['login']) || $_SESSION['login'] != 1) {
    http_response_code(401);
    echo json_encode([
        'status' => false,
        'message' => 'Silakan login kembali'
    ]);
    exit;
}

include "../configurasi/koneksi.php";

function realtimeTableExists($db, $tableName)
{
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
    $stmt->execute([$tableName]);
    return ((int)$stmt->fetchColumn() > 0);
}

function realtimeColumnExists($db, $tableName, $columnName)
{
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$tableName, $columnName]);
    return ((int)$stmt->fetchColumn() > 0);
}

try {
    $bulanRequest = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('n');
    $tahunRequest = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');
    $tipeRequest = isset($_GET['tipe']) ? $_GET['tipe'] : 'penjualan';
    if ($tipeRequest !== 'swamedikasi') {
        $tipeRequest = 'penjualan';
    }

    if ($bulanRequest < 1 || $bulanRequest > 12) {
        $bulanRequest = (int)date('n');
    }

    if ($tahunRequest < 2000 || $tahunRequest > 2100) {
        $tahunRequest = (int)date('Y');
    }

    $awalBulan = sprintf('%04d-%02d-01', $tahunRequest, $bulanRequest);
    $akhirBulanPenuh = date('Y-m-t', strtotime($awalBulan));

    $bulanSekarang = (int)date('n');
    $tahunSekarang = (int)date('Y');
    if ($bulanRequest === $bulanSekarang && $tahunRequest === $tahunSekarang) {
        $akhirPeriode = date('Y-m-d');
    } else {
        $akhirPeriode = $akhirBulanPenuh;
    }

    $awalBulanSebelumnya = date('Y-m-01', strtotime($awalBulan . ' -1 month'));
    $akhirBulanSebelumnya = date('Y-m-t', strtotime($awalBulanSebelumnya));

    if ($tipeRequest === 'swamedikasi') {
        $judulTipe = 'Swamedikasi';
        $formatTipe = 'angka';

        if (!realtimeTableExists($db, 'riwayat_pelanggan')) {
            throw new Exception('Tabel riwayat_pelanggan belum tersedia');
        }

        if (realtimeColumnExists($db, 'riwayat_pelanggan', 'tgl')) {
            $exprTgl = 'tgl';
        } elseif (realtimeColumnExists($db, 'riwayat_pelanggan', 'created_at')) {
            $exprTgl = 'DATE(created_at)';
        } else {
            throw new Exception('Kolom tanggal riwayat_pelanggan tidak ditemukan');
        }

        $sql = "SELECT $exprTgl AS tanggal, COUNT(*) AS total_nilai
                FROM riwayat_pelanggan
                WHERE $exprTgl BETWEEN :awal_bulan AND :akhir_periode
                GROUP BY $exprTgl
                ORDER BY $exprTgl ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':awal_bulan' => $awalBulan,
            ':akhir_periode' => $akhirPeriode
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mapNilai = [];
        foreach ($rows as $row) {
            $mapNilai[$row['tanggal']] = (float)$row['total_nilai'];
        }

        $sqlPrev = "SELECT $exprTgl AS tanggal, COUNT(*) AS total_nilai
                    FROM riwayat_pelanggan
                    WHERE $exprTgl BETWEEN :awal_prev AND :akhir_prev
                    GROUP BY $exprTgl
                    ORDER BY $exprTgl ASC";

        $stmtPrev = $db->prepare($sqlPrev);
        $stmtPrev->execute([
            ':awal_prev' => $awalBulanSebelumnya,
            ':akhir_prev' => $akhirBulanSebelumnya
        ]);
        $rowsPrev = $stmtPrev->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $judulTipe = 'Penjualan';
        $formatTipe = 'rupiah';

        $sql = "SELECT tgl_trkasir AS tanggal, SUM(ttl_trkasir) AS total_nilai
                FROM trkasir
                WHERE tgl_trkasir BETWEEN :awal_bulan AND :akhir_periode
                GROUP BY tgl_trkasir
                ORDER BY tgl_trkasir ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':awal_bulan' => $awalBulan,
            ':akhir_periode' => $akhirPeriode
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mapNilai = [];
        foreach ($rows as $row) {
            $mapNilai[$row['tanggal']] = (float)$row['total_nilai'];
        }

        $sqlPrev = "SELECT tgl_trkasir AS tanggal, SUM(ttl_trkasir) AS total_nilai
                    FROM trkasir
                    WHERE tgl_trkasir BETWEEN :awal_prev AND :akhir_prev
                    GROUP BY tgl_trkasir
                    ORDER BY tgl_trkasir ASC";

        $stmtPrev = $db->prepare($sqlPrev);
        $stmtPrev->execute([
            ':awal_prev' => $awalBulanSebelumnya,
            ':akhir_prev' => $akhirBulanSebelumnya
        ]);
        $rowsPrev = $stmtPrev->fetchAll(PDO::FETCH_ASSOC);
    }

    $mapNilaiPrevByHari = [];
    foreach ($rowsPrev as $rowPrev) {
        $hari = date('d', strtotime($rowPrev['tanggal']));
        $mapNilaiPrevByHari[$hari] = (float)$rowPrev['total_nilai'];
    }

    $dataHarian = [];
    $dataBulanLalu = [];
    $tanggalLoop = $awalBulan;
    while ($tanggalLoop <= $akhirPeriode) {
        $total = 0;
        if (isset($mapNilai[$tanggalLoop])) {
            $total = $mapNilai[$tanggalLoop];
        }

        $hariLoop = date('d', strtotime($tanggalLoop));
        $totalPrev = 0;
        if (isset($mapNilaiPrevByHari[$hariLoop])) {
            $totalPrev = $mapNilaiPrevByHari[$hariLoop];
        }

        $dataHarian[] = [
            'tanggal' => $tanggalLoop,
            'nilai' => $total
        ];

        $dataBulanLalu[] = [
            'hari' => $hariLoop,
            'nilai' => $totalPrev
        ];

        $tanggalLoop = date('Y-m-d', strtotime($tanggalLoop . ' +1 day'));
    }

    echo json_encode([
        'status' => true,
        'tipe' => $tipeRequest,
        'judul_tipe' => $judulTipe,
        'format' => $formatTipe,
        'data' => $dataHarian,
        'data_bulan_lalu' => $dataBulanLalu,
        'periode_label' => date('F Y', strtotime($awalBulan)),
        'periode_sebelumnya_label' => date('F Y', strtotime($awalBulanSebelumnya)),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Gagal memuat data'
    ]);
}
