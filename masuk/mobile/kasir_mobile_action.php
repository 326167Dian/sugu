<?php
session_start();
error_reporting(0);

include "../../configurasi/koneksi.php";

$db = isset($db) ? $db : null;

function mobileRedirectKasir($status, $message, $targetModule = 'kasir', $extraParams = array())
{
    $allowedModules = array('kasir', 'keranjang');
    $module = in_array($targetModule, $allowedModules, true) ? $targetModule : 'kasir';

    $acceptHeader = isset($_SERVER['HTTP_ACCEPT']) ? strtolower((string)$_SERVER['HTTP_ACCEPT']) : '';
    $requestedWith = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) : '';
    $isAjax = (isset($_POST['ajax']) && $_POST['ajax'] === '1') || $requestedWith === 'xmlhttprequest' || strpos($acceptHeader, 'application/json') !== false;

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');

        $response = array(
            'status' => $status,
            'message' => $message,
            'module' => $module,
        );

        if (is_array($extraParams) && count($extraParams) > 0) {
            foreach ($extraParams as $key => $value) {
                $response[$key] = $value;
            }
        }

        echo json_encode($response);
        exit;
    }

    $query = array(
        'module' => $module,
        'status' => $status,
        'msg' => $message,
    );

    if (is_array($extraParams) && count($extraParams) > 0) {
        foreach ($extraParams as $key => $value) {
            $query[$key] = $value;
        }
    }

    $url = "../media_mobile.php?" . http_build_query($query);
    header("Location: " . $url);
    exit;
}

function mobileBatchFifo($db, $qty, $kdBarang)
{
    $stmt = $db->prepare("SELECT 
                          no_batch, 
                          exp_date, 
                          MIN(tgl_transaksi) AS tgl_awal, 
                          SUM(CASE WHEN status = 'masuk' THEN qty ELSE 0 END) AS total_masuk, 
                          SUM(CASE WHEN status = 'keluar' THEN qty ELSE 0 END) AS total_keluar, 
                          SUM(CASE WHEN status = 'masuk' THEN qty ELSE 0 END) - SUM(CASE WHEN status = 'keluar' THEN qty ELSE 0 END) AS sisa_qty 
                        FROM batch 
                        WHERE kd_barang = ? 
                        GROUP BY no_batch 
                        HAVING sisa_qty > 0 
                        ORDER BY CASE WHEN exp_date = '0000-00-00' THEN '9999-12-31' ELSE exp_date END ASC, exp_date ASC");
    $stmt->execute(array($kdBarang));
    
    $data = array();
    if ($stmt->rowCount() > 0) {
        $kebutuhan = $qty;
        while ($r1 = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($kebutuhan <= 0) {
                break;
            }

            if ($r1['sisa_qty'] <= $kebutuhan) {
                $ambil = $r1['sisa_qty'];
            } else {
                $ambil = $kebutuhan;
            }

            $data[] = array(
                'no_batch'   => $r1['no_batch'],
                'exp_date'   => $r1['exp_date'],
                'qty_ambil'  => $ambil
            );

            $kebutuhan -= $ambil;
        }

        if ($kebutuhan > 0) {
            $data[] = array(
                'no_batch'   => '',
                'exp_date'   => '0000-00-00',
                'qty_ambil'  => $kebutuhan
            );
        }
    } else {
        $data[] = array(
            'no_batch'   => '',
            'exp_date'   => '0000-00-00',
            'qty_ambil'  => $qty
        );
    }
    
    return $data;
}

function mobileHasAccess($sessionKey)
{
    return isset($_SESSION[$sessionKey]) && $_SESSION[$sessionKey] === 'Y';
}

function mobileColumnExists($db, $tableName, $columnName)
{
    try {
        $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
        $stmt->execute(array($tableName, $columnName));
        return (int)$stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        return false;
    }
}

function mobileTableExists($db, $tableName)
{
    try {
        $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
        $stmt->execute(array($tableName));
        return (int)$stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        return false;
    }
}

function mobileSanitizeNumber($value)
{
    $sanitized = preg_replace('/[^0-9.,-]/', '', (string)$value);
    $sanitized = str_replace('.', '', $sanitized);
    $sanitized = str_replace(',', '.', $sanitized);
    return (float)$sanitized;
}

function mobileEnsureActiveKasirCode($db, $idAdmin)
{
    $stmt = $db->prepare("SELECT kd_trkasir FROM kdtk WHERE id_admin = ? AND stt_kdtk = 'ON' ORDER BY id_kdtk DESC LIMIT 1");
    $stmt->execute(array($idAdmin));
    $kdAktif = $stmt->fetchColumn();
    if (!empty($kdAktif)) {
        return $kdAktif;
    }

    $newCode = null;
    for ($i = 0; $i < 10; $i++) {
        $candidate = 'TKP-' . date('dmyHis') . sprintf('%02d', $i);
        $cek = $db->prepare("SELECT COUNT(*) FROM kdtk WHERE kd_trkasir = ?");
        $cek->execute(array($candidate));
        if ((int)$cek->fetchColumn() === 0) {
            $newCode = $candidate;
            break;
        }
        usleep(150000);
    }

    if (empty($newCode)) {
        throw new Exception('Gagal membuat kode transaksi baru.');
    }

    $insert = $db->prepare("INSERT INTO kdtk(kd_trkasir, id_admin, stt_kdtk) VALUES(?, ?, 'ON')");
    $insert->execute(array($newCode, $idAdmin));

    return $newCode;
}

function mobileResolveDefaultPelangganId($db)
{
    if (!mobileTableExists($db, 'pelanggan')) {
        return 0;
    }

    try {
        if (mobileColumnExists($db, 'pelanggan', 'nm_pelanggan')) {
            $stmtUmum = $db->prepare("SELECT id_pelanggan FROM pelanggan WHERE LOWER(nm_pelanggan) LIKE '%umum%' ORDER BY id_pelanggan ASC LIMIT 1");
            $stmtUmum->execute();
            $idUmum = $stmtUmum->fetchColumn();
            if ($idUmum !== false) {
                return (int)$idUmum;
            }
        }

        $stmtAny = $db->prepare("SELECT id_pelanggan FROM pelanggan ORDER BY id_pelanggan ASC LIMIT 1");
        $stmtAny->execute();
        $idAny = $stmtAny->fetchColumn();
        if ($idAny !== false) {
            return (int)$idAny;
        }
    } catch (Exception $e) {
        return 0;
    }

    return 0;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    mobileRedirectKasir('error', 'Metode request tidak valid.');
}

if (empty($_SESSION['login']) || empty($_SESSION['idadmin'])) {
    mobileRedirectKasir('error', 'Anda tidak memiliki akses ke kasir mobile.');
}

if (!$db instanceof PDO) {
    mobileRedirectKasir('error', 'Koneksi database tidak tersedia.');
}

$action = isset($_POST['mobile_action']) ? $_POST['mobile_action'] : '';
$returnModule = isset($_POST['return_module']) ? $_POST['return_module'] : 'kasir';
$idAdmin = (int)$_SESSION['idadmin'];
$petugas = isset($_SESSION['namalengkap']) ? $_SESSION['namalengkap'] : '';

try {
    $db->beginTransaction();

    if ($action === 'open_shift') {
        if (!mobileHasAccess('shiftkerja')) {
            throw new Exception('Anda tidak memiliki hak akses shift kasir.');
        }

        if (!mobileTableExists($db, 'waktukerja')) {
            throw new Exception('Tabel waktukerja tidak ditemukan.');
        }

        $today = date('Y-m-d');
        $cekShiftAktif = $db->prepare("SELECT COUNT(*) FROM waktukerja WHERE tanggal = ? AND status = 'ON'");
        $cekShiftAktif->execute(array($today));
        if ((int)$cekShiftAktif->fetchColumn() > 0) {
            throw new Exception('Kasir sudah dibuka untuk hari ini.');
        }

        $shiftInput = isset($_POST['shift']) ? trim((string)$_POST['shift']) : '1';
        if ($shiftInput !== '1' && $shiftInput !== '2') {
            throw new Exception('Shift tidak valid.');
        }

        $saldoAwal = mobileSanitizeNumber(isset($_POST['saldoawal']) ? $_POST['saldoawal'] : 0);
        if ($saldoAwal < 0) {
            throw new Exception('Saldo awal tidak boleh negatif.');
        }

        $stmtInsertShift = $db->prepare("INSERT INTO waktukerja (petugasbuka, petugastutup, tanggal, waktubuka, waktututup, shift, saldoawal, saldoakhir, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtInsertShift->execute(array(
            $petugas,
            '',
            $today,
            date('H:i:s'),
            '00:00:00',
            $shiftInput,
            $saldoAwal,
            0,
            'ON'
        ));

        $db->commit();
        mobileRedirectKasir('success', 'Open kasir berhasil disimpan.', $returnModule);
    }

    if ($action === 'close_shift') {
        if (!mobileHasAccess('shiftkerja')) {
            throw new Exception('Anda tidak memiliki hak akses shift kasir.');
        }

        if (!mobileTableExists($db, 'waktukerja')) {
            throw new Exception('Tabel waktukerja tidak ditemukan.');
        }

        $today = date('Y-m-d');
        $stmtShiftAktif = $db->prepare("SELECT * FROM waktukerja WHERE tanggal = ? AND status = 'ON' LIMIT 1");
        $stmtShiftAktif->execute(array($today));
        $shiftAktifRow = $stmtShiftAktif->fetch(PDO::FETCH_ASSOC);
        if (!$shiftAktifRow) {
            throw new Exception('Tidak ada shift aktif untuk ditutup.');
        }

        $saldoAkhir = mobileSanitizeNumber(isset($_POST['saldoakhir']) ? $_POST['saldoakhir'] : 0);
        if ($saldoAkhir < 0) {
            throw new Exception('Saldo akhir tidak boleh negatif.');
        }

        $stmtCloseShift = $db->prepare("UPDATE waktukerja SET petugastutup = ?, waktututup = ?, status = ?, saldoakhir = ? WHERE id_shift = ?");
        $stmtCloseShift->execute(array(
            $petugas,
            date('H:i:s'),
            'OFF',
            $saldoAkhir,
            $shiftAktifRow['id_shift']
        ));

        $db->commit();
        mobileRedirectKasir('success', 'Tutup kasir berhasil disimpan.', $returnModule);
    }

    if (!mobileHasAccess('tpk')) {
        throw new Exception('Anda tidak memiliki akses ke kasir mobile.');
    }

    if ($action === 'delete_transaction') {
        if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'pemilik') {
            throw new Exception('Hanya pemilik yang bisa menghapus transaksi.');
        }

        $idTrkasir = isset($_POST['id_trkasir']) ? (int)$_POST['id_trkasir'] : 0;
        if ($idTrkasir <= 0) {
            throw new Exception('ID transaksi tidak valid.');
        }

        $stmtInduk = $db->prepare("SELECT * FROM trkasir WHERE id_trkasir = ? LIMIT 1");
        $stmtInduk->execute(array($idTrkasir));
        $trxInduk = $stmtInduk->fetch(PDO::FETCH_ASSOC);
        if (!$trxInduk) {
            throw new Exception('Data transaksi tidak ditemukan.');
        }

        $kdTrx = isset($trxInduk['kd_trkasir']) ? (string)$trxInduk['kd_trkasir'] : '';
        if ($kdTrx === '') {
            throw new Exception('Kode transaksi tidak valid.');
        }

        $stmtDetail = $db->prepare("SELECT * FROM trkasir_detail WHERE kd_trkasir = ?");
        $stmtDetail->execute(array($kdTrx));
        $detailRows = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);

        foreach ($detailRows as $detail) {
            $idBarang = isset($detail['id_barang']) ? (int)$detail['id_barang'] : 0;
            $qtyDetail = isset($detail['qty_dtrkasir']) ? (float)$detail['qty_dtrkasir'] : 0;
            $idDetail = isset($detail['id_dtrkasir']) ? (int)$detail['id_dtrkasir'] : 0;

            if ($idBarang > 0 && $qtyDetail > 0) {
                $stmtRestoreStok = $db->prepare("UPDATE barang SET stok_barang = stok_barang + ? WHERE id_barang = ?");
                $stmtRestoreStok->execute(array($qtyDetail, $idBarang));
            }

            if (mobileTableExists($db, 'komisi_pegawai') && $idDetail > 0) {
                $stmtDelKomisi = $db->prepare("DELETE FROM komisi_pegawai WHERE id_dtrkasir = ?");
                $stmtDelKomisi->execute(array($idDetail));
            }
        }

        if (mobileTableExists($db, 'batch')) {
            $stmtDelBatch = $db->prepare("DELETE FROM batch WHERE kd_transaksi = ? AND status = 'keluar'");
            $stmtDelBatch->execute(array($kdTrx));
        }

        $stmtDelDetail = $db->prepare("DELETE FROM trkasir_detail WHERE kd_trkasir = ?");
        $stmtDelDetail->execute(array($kdTrx));

        if (mobileTableExists($db, 'pelanggan') && isset($trxInduk['id_pelanggan']) && (int)$trxInduk['id_pelanggan'] > 0) {
            $tambahanPoin = isset($trxInduk['tambahan_poin']) ? (float)$trxInduk['tambahan_poin'] : 0;
            $redeemPoin = isset($trxInduk['redeem_poin']) ? (float)$trxInduk['redeem_poin'] : 0;
            $idPelanggan = (int)$trxInduk['id_pelanggan'];

            $stmtUpdatePoin = $db->prepare("UPDATE pelanggan SET total_poin = (total_poin - :tambahan_poin) + :redeem_poin WHERE id_pelanggan = :id_pelanggan");
            $stmtUpdatePoin->execute(array(
                ':tambahan_poin' => $tambahanPoin,
                ':redeem_poin' => $redeemPoin,
                ':id_pelanggan' => $idPelanggan,
            ));
        }

        if (mobileTableExists($db, 'kartu_stok')) {
            $stmtDelKartu = $db->prepare("DELETE FROM kartu_stok WHERE kode_transaksi = ?");
            $stmtDelKartu->execute(array($kdTrx));
        }

        $stmtDelTrx = $db->prepare("DELETE FROM trkasir WHERE id_trkasir = ?");
        $stmtDelTrx->execute(array($idTrkasir));

        $db->commit();
        mobileRedirectKasir('success', 'Transaksi berhasil dihapus.', $returnModule);
    }

    $shiftAktif = '0';
    if (mobileTableExists($db, 'waktukerja')) {
        $shiftStmt = $db->prepare("SELECT shift FROM waktukerja WHERE tanggal = CURDATE() AND status = 'ON' LIMIT 1");
        $shiftStmt->execute();
        $shiftRow = $shiftStmt->fetch(PDO::FETCH_ASSOC);
        if (!$shiftRow) {
            throw new Exception('Shift kasir belum dibuka. Silakan open kasir terlebih dahulu.');
        }
        $shiftAktif = isset($shiftRow['shift']) ? $shiftRow['shift'] : '0';
    }

    if ($action === 'delete_cart_item') {
        $idDetail = isset($_POST['id_dtrkasir']) ? (int)$_POST['id_dtrkasir'] : 0;
        if ($idDetail <= 0) {
            throw new Exception('ID detail item tidak valid.');
        }

        $stmtDetail = $db->prepare("SELECT id_dtrkasir, id_barang, kd_barang, qty_dtrkasir, kd_trkasir, idadmin FROM trkasir_detail WHERE id_dtrkasir = ? LIMIT 1");
        $stmtDetail->execute(array($idDetail));
        $detailRow = $stmtDetail->fetch(PDO::FETCH_ASSOC);
        if (!$detailRow) {
            throw new Exception('Item keranjang tidak ditemukan atau sudah terhapus.');
        }

        $idAdminBaris = isset($detailRow['idadmin']) ? (int)$detailRow['idadmin'] : 0;
        if ($idAdminBaris > 0 && $idAdminBaris !== $idAdmin) {
            throw new Exception('Item keranjang bukan milik user login saat ini.');
        }

        $idBarang = isset($detailRow['id_barang']) ? (int)$detailRow['id_barang'] : 0;
        $kdBarang = isset($detailRow['kd_barang']) ? (string)$detailRow['kd_barang'] : '';
        $qtyDetail = isset($detailRow['qty_dtrkasir']) ? (float)$detailRow['qty_dtrkasir'] : 0;
        $kdTrkasir = isset($detailRow['kd_trkasir']) ? (string)$detailRow['kd_trkasir'] : '';
        if ($kdTrkasir === '') {
            throw new Exception('Kode transaksi item tidak valid.');
        }

        if ($idBarang > 0 && $qtyDetail > 0) {
            $stmtRestoreStok = $db->prepare("UPDATE barang SET stok_barang = stok_barang + ? WHERE id_barang = ?");
            $stmtRestoreStok->execute(array($qtyDetail, $idBarang));
        }

        if (mobileTableExists($db, 'komisi_pegawai')) {
            $stmtDelKomisi = $db->prepare("DELETE FROM komisi_pegawai WHERE id_dtrkasir = ?");
            $stmtDelKomisi->execute(array($idDetail));
        }

        if (mobileTableExists($db, 'batch') && $kdBarang !== '' && $qtyDetail > 0) {
            try {
                $qtySisaRollback = (float)$qtyDetail;
                $stmtBatchKeluar = $db->prepare("SELECT id_batch, qty FROM batch WHERE kd_transaksi = ? AND kd_barang = ? AND status = 'keluar' ORDER BY id_batch DESC");
                $stmtBatchKeluar->execute(array($kdTrkasir, $kdBarang));

                while (($rowBatch = $stmtBatchKeluar->fetch(PDO::FETCH_ASSOC)) && $qtySisaRollback > 0) {
                    $idBatch = isset($rowBatch['id_batch']) ? (int)$rowBatch['id_batch'] : 0;
                    $qtyBatch = isset($rowBatch['qty']) ? (float)$rowBatch['qty'] : 0;
                    if ($idBatch <= 0 || $qtyBatch <= 0) {
                        continue;
                    }

                    if ($qtyBatch <= $qtySisaRollback + 0.00001) {
                        $stmtDelBatchRow = $db->prepare("DELETE FROM batch WHERE id_batch = ?");
                        $stmtDelBatchRow->execute(array($idBatch));
                        $qtySisaRollback -= $qtyBatch;
                    } else {
                        $qtyBaru = $qtyBatch - $qtySisaRollback;
                        $stmtUpdBatchRow = $db->prepare("UPDATE batch SET qty = ? WHERE id_batch = ?");
                        $stmtUpdBatchRow->execute(array($qtyBaru, $idBatch));
                        $qtySisaRollback = 0;
                    }
                }
            } catch (Exception $e) {
                // Abaikan kegagalan rollback batch agar hapus item keranjang tetap berjalan.
            }
        }

        $stmtDeleteDetail = $db->prepare("DELETE FROM trkasir_detail WHERE id_dtrkasir = ? AND kd_trkasir = ?");
        $stmtDeleteDetail->execute(array($idDetail, $kdTrkasir));
        if ($stmtDeleteDetail->rowCount() <= 0) {
            throw new Exception('Gagal menghapus item keranjang.');
        }

        $stmtCartSummary = $db->prepare("SELECT COUNT(*) AS total_baris, COALESCE(SUM(qty_dtrkasir), 0) AS total_qty, COALESCE(SUM(hrgttl_dtrkasir), 0) AS total_harga FROM trkasir_detail WHERE kd_trkasir = ?");
        $stmtCartSummary->execute(array($kdTrkasir));
        $cartSummary = $stmtCartSummary->fetch(PDO::FETCH_ASSOC);

        $stmtCartDetails = $db->prepare("SELECT d.id_dtrkasir,
                                                d.kd_barang,
                                                d.tipe,
                                                COALESCE(d.resep, 'TIDAK') AS resep,
                                                d.sat_dtrkasir,
                                                d.qty_dtrkasir,
                                                d.hrgjual_dtrkasir,
                                                d.hrgttl_dtrkasir,
                                                COALESCE(b.nm_barang, d.nmbrg_dtrkasir) AS nm_barang_tampil
                                         FROM trkasir_detail d
                                         LEFT JOIN barang b ON b.id_barang = d.id_barang
                                         WHERE d.kd_trkasir = ?
                                         ORDER BY d.id_dtrkasir DESC
                                         LIMIT 500");
        $stmtCartDetails->execute(array($kdTrkasir));
        $cartDetailsRows = $stmtCartDetails->fetchAll(PDO::FETCH_ASSOC);
        $cartDetails = array();
        foreach ($cartDetailsRows as $row) {
            $cartDetails[] = array(
                'id_dtrkasir' => isset($row['id_dtrkasir']) ? (int)$row['id_dtrkasir'] : 0,
                'kd_barang' => isset($row['kd_barang']) ? (string)$row['kd_barang'] : '',
                'tipe' => isset($row['tipe']) ? (int)$row['tipe'] : 1,
                'resep' => isset($row['resep']) ? (string)$row['resep'] : 'TIDAK',
                'nm_barang_tampil' => isset($row['nm_barang_tampil']) ? (string)$row['nm_barang_tampil'] : '',
                'sat_dtrkasir' => isset($row['sat_dtrkasir']) ? (string)$row['sat_dtrkasir'] : '',
                'qty_dtrkasir' => isset($row['qty_dtrkasir']) ? (float)$row['qty_dtrkasir'] : 0,
                'hrgjual_dtrkasir' => isset($row['hrgjual_dtrkasir']) ? (float)$row['hrgjual_dtrkasir'] : 0,
                'hrgttl_dtrkasir' => isset($row['hrgttl_dtrkasir']) ? (float)$row['hrgttl_dtrkasir'] : 0,
            );
        }

        $extraAjaxData = array(
            'cart' => array(
                'kd_trkasir' => $kdTrkasir,
                'total_baris' => isset($cartSummary['total_baris']) ? (int)$cartSummary['total_baris'] : 0,
                'total_qty' => isset($cartSummary['total_qty']) ? (float)$cartSummary['total_qty'] : 0,
                'total_harga' => isset($cartSummary['total_harga']) ? (float)$cartSummary['total_harga'] : 0,
                'details' => $cartDetails,
            ),
        );

        $db->commit();
        mobileRedirectKasir('success', 'Item berhasil dihapus dari keranjang.', $returnModule, $extraAjaxData);
    }

    $kdTrkasir = mobileEnsureActiveKasirCode($db, $idAdmin);

    if ($action === 'add_item') {
        $barcode = trim(isset($_POST['barcode']) ? $_POST['barcode'] : '');
        $qty = (float)(isset($_POST['qty']) ? $_POST['qty'] : 0);
        $hargaJualManualRaw = trim(isset($_POST['harga_jual']) ? (string)$_POST['harga_jual'] : '');
        $hargaJualManual = 0;
        if ($hargaJualManualRaw !== '') {
            $hargaJualManualClean = preg_replace('/[^0-9.,-]/', '', $hargaJualManualRaw);
            if (strpos($hargaJualManualClean, ',') !== false && strpos($hargaJualManualClean, '.') !== false) {
                $hargaJualManualClean = str_replace('.', '', $hargaJualManualClean);
                $hargaJualManualClean = str_replace(',', '.', $hargaJualManualClean);
            } elseif (strpos($hargaJualManualClean, ',') !== false) {
                $hargaJualManualClean = str_replace(',', '.', $hargaJualManualClean);
            } else {
                $hargaJualManualClean = str_replace(',', '', $hargaJualManualClean);
            }
            $hargaJualManual = (float)round((float)$hargaJualManualClean, 0);
        }
        $jenisTransaksi = isset($_POST['jns_transaksi']) ? (int)$_POST['jns_transaksi'] : 1;
        if ($jenisTransaksi < 1 || $jenisTransaksi > 6) {
            $jenisTransaksi = 1;
        }

        $resep = isset($_POST['resep']) ? strtoupper(trim((string)$_POST['resep'])) : 'TIDAK';
        if ($resep !== 'YA' && $resep !== 'TIDAK') {
            $resep = 'TIDAK';
        }

        if ($barcode === '') {
            throw new Exception('Barcode wajib diisi.');
        }

        if ($qty <= 0) {
            throw new Exception('Qty harus lebih besar dari 0.');
        }

        $qtyRounded = floor($qty);
        if ($qtyRounded <= 0) {
            throw new Exception('Qty tidak valid.');
        }

        if ($hargaJualManualRaw !== '' && $hargaJualManual <= 0) {
            throw new Exception('Harga jual manual harus lebih besar dari 0.');
        }

        $sqlBarang = "SELECT * FROM barang WHERE kd_barang = ? LIMIT 1";
        $stmtBarang = $db->prepare($sqlBarang);
        $stmtBarang->execute(array($barcode));
        $barang = $stmtBarang->fetch(PDO::FETCH_ASSOC);

        if (!$barang) {
            if (mobileColumnExists($db, 'barang', 'barcode_barang')) {
                $stmtBarang2 = $db->prepare("SELECT * FROM barang WHERE barcode_barang = ? LIMIT 1");
                $stmtBarang2->execute(array($barcode));
                $barang = $stmtBarang2->fetch(PDO::FETCH_ASSOC);
            }
        }

        if (!$barang) {
            throw new Exception('Barang dengan barcode tersebut tidak ditemukan.');
        }

        $idBarang = (int)$barang['id_barang'];
        $kdBarang = $barang['kd_barang'];
        $nmBarang = $barang['nm_barang'];
        $satBarang = isset($barang['sat_barang']) ? $barang['sat_barang'] : 'PCS';

        $stokTersedia = isset($barang['stok_barang']) ? (float)$barang['stok_barang'] : 0;

        $hargaReguler = (isset($barang['hrgjual_barang']) && (float)$barang['hrgjual_barang'] > 0) ? (float)$barang['hrgjual_barang'] : 0;
        $hargaResep = (isset($barang['hrgjual_barang1']) && (float)$barang['hrgjual_barang1'] > 0) ? (float)$barang['hrgjual_barang1'] : 0;
        $hargaMarketplace = (isset($barang['hrgjual_barang2']) && (float)$barang['hrgjual_barang2'] > 0) ? (float)$barang['hrgjual_barang2'] : 0;
        $hargaModal = isset($barang['hrgsat_barang']) ? (float)$barang['hrgsat_barang'] : 0;

        if ($hargaReguler <= 0) {
            $hargaReguler = $hargaResep > 0 ? $hargaResep : ($hargaMarketplace > 0 ? $hargaMarketplace : $hargaModal);
        }
        if ($hargaResep <= 0) {
            $hargaResep = $hargaReguler > 0 ? $hargaReguler : ($hargaMarketplace > 0 ? $hargaMarketplace : $hargaModal);
        }
        if ($hargaMarketplace <= 0) {
            $hargaMarketplace = $hargaResep > 0 ? $hargaResep : ($hargaReguler > 0 ? $hargaReguler : $hargaModal);
        }

        $hargaJual = $hargaReguler;
        if ($jenisTransaksi === 2) {
            $hargaJual = $hargaResep;
        } elseif ($jenisTransaksi === 3) {
            $hargaJual = $hargaMarketplace;
        }

        if ($hargaJualManual > 0) {
            $hargaJual = $hargaJualManual;
        }
        $hargaJual = (float)round($hargaJual, 0);

        $modal = $hargaModal;

        $komisi = 0;
        if (mobileHasAccess('komisi') && isset($barang['komisi'])) {
            $komisi = (float)$barang['komisi'] * $qtyRounded;
        }

        $ttlHarga = (float)round($qtyRounded * $hargaJual, 0);
        $profit = $ttlHarga - ($modal * $qtyRounded);
        $lineQty = (float)$qtyRounded;
        $lineSubtotal = (float)$ttlHarga;

        $hasResepColumn = mobileColumnExists($db, 'trkasir_detail', 'resep');
        if ($hasResepColumn) {
            $stmtInsert = $db->prepare("INSERT INTO trkasir_detail (kd_trkasir, id_barang, kd_barang, nmbrg_dtrkasir, qty_dtrkasir, sat_dtrkasir, hrgjual_dtrkasir, disc, resep, modal, profit, waktu, hrgttl_dtrkasir, tipe, komisi, idadmin) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtInsert->execute(array(
                $kdTrkasir,
                $idBarang,
                $kdBarang,
                $nmBarang,
                $qtyRounded,
                $satBarang,
                $hargaJual,
                $resep,
                $modal,
                $profit,
                date('Y-m-d H:i:s'),
                $ttlHarga,
                $jenisTransaksi,
                $komisi,
                $idAdmin
            ));
        } else {
            $stmtInsert = $db->prepare("INSERT INTO trkasir_detail (kd_trkasir, id_barang, kd_barang, nmbrg_dtrkasir, qty_dtrkasir, sat_dtrkasir, hrgjual_dtrkasir, disc, modal, profit, waktu, hrgttl_dtrkasir, tipe, komisi, idadmin) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, ?, ?, ?, ?)");
            $stmtInsert->execute(array(
                $kdTrkasir,
                $idBarang,
                $kdBarang,
                $nmBarang,
                $qtyRounded,
                $satBarang,
                $hargaJual,
                $modal,
                $profit,
                date('Y-m-d H:i:s'),
                $ttlHarga,
                $jenisTransaksi,
                $komisi,
                $idAdmin
            ));
        }

        $stmtStok = $db->prepare("UPDATE barang SET stok_barang = stok_barang - ? WHERE id_barang = ?");
        $stmtStok->execute(array($qtyRounded, $idBarang));
        if ($stmtStok->rowCount() <= 0) {
            throw new Exception('Gagal update stok barang. Coba ulangi.');
        }

        if ($hargaJualManual > 0 && mobileColumnExists($db, 'barang', 'hrgsat_barang')) {
            $stmtUpdateHargaBarang = $db->prepare("UPDATE barang SET hrgsat_barang = ? WHERE id_barang = ?");
            $stmtUpdateHargaBarang->execute(array((float)round($hargaJualManual, 0), $idBarang));
        }

        if (mobileTableExists($db, 'batch')) {
            $batchData = mobileBatchFifo($db, $qtyRounded, $kdBarang);
            $datetime = date('Y-m-d H:i:s');
            
            foreach ($batchData as $batchItem) {
                $noBatch = (string)$batchItem['no_batch'];
                $expDate = (string)$batchItem['exp_date'];
                $qtyAmbil = (float)$batchItem['qty_ambil'];
                
                if ($noBatch !== '' && $noBatch !== '0') {
                    $stmtInsertBatch = $db->prepare("INSERT INTO batch (tgl_transaksi, no_batch, exp_date, qty, satuan, kd_transaksi, kd_barang, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'keluar')");
                    $stmtInsertBatch->execute(array($datetime, $noBatch, $expDate, $qtyAmbil, $satBarang, $kdTrkasir, $kdBarang));
                }
            }
        }

        $stmtCartSummary = $db->prepare("SELECT COUNT(*) AS total_baris, COALESCE(SUM(qty_dtrkasir), 0) AS total_qty, COALESCE(SUM(hrgttl_dtrkasir), 0) AS total_harga FROM trkasir_detail WHERE kd_trkasir = ?");
        $stmtCartSummary->execute(array($kdTrkasir));
        $cartSummary = $stmtCartSummary->fetch(PDO::FETCH_ASSOC);

        $stmtCartDetails = $db->prepare("SELECT d.id_dtrkasir,
                                                d.kd_barang,
                                                d.tipe,
                                                COALESCE(d.resep, 'TIDAK') AS resep,
                                                d.sat_dtrkasir,
                                                d.qty_dtrkasir,
                                                d.hrgjual_dtrkasir,
                                                d.hrgttl_dtrkasir,
                                                COALESCE(b.nm_barang, d.nmbrg_dtrkasir) AS nm_barang_tampil
                                         FROM trkasir_detail d
                                         LEFT JOIN barang b ON b.id_barang = d.id_barang
                                         WHERE d.kd_trkasir = ?
                                         ORDER BY d.id_dtrkasir DESC
                                         LIMIT 500");
        $stmtCartDetails->execute(array($kdTrkasir));
        $cartDetailsRows = $stmtCartDetails->fetchAll(PDO::FETCH_ASSOC);
        $cartDetails = array();
        foreach ($cartDetailsRows as $row) {
            $cartDetails[] = array(
                'id_dtrkasir' => isset($row['id_dtrkasir']) ? (int)$row['id_dtrkasir'] : 0,
                'kd_barang' => isset($row['kd_barang']) ? (string)$row['kd_barang'] : '',
                'tipe' => isset($row['tipe']) ? (int)$row['tipe'] : 1,
                'resep' => isset($row['resep']) ? (string)$row['resep'] : 'TIDAK',
                'nm_barang_tampil' => isset($row['nm_barang_tampil']) ? (string)$row['nm_barang_tampil'] : '',
                'sat_dtrkasir' => isset($row['sat_dtrkasir']) ? (string)$row['sat_dtrkasir'] : '',
                'qty_dtrkasir' => isset($row['qty_dtrkasir']) ? (float)$row['qty_dtrkasir'] : 0,
                'hrgjual_dtrkasir' => isset($row['hrgjual_dtrkasir']) ? (float)$row['hrgjual_dtrkasir'] : 0,
                'hrgttl_dtrkasir' => isset($row['hrgttl_dtrkasir']) ? (float)$row['hrgttl_dtrkasir'] : 0,
            );
        }

        $extraAjaxData = array(
            'cart' => array(
                'kd_trkasir' => $kdTrkasir,
                'total_baris' => isset($cartSummary['total_baris']) ? (int)$cartSummary['total_baris'] : 0,
                'total_qty' => isset($cartSummary['total_qty']) ? (float)$cartSummary['total_qty'] : 0,
                'total_harga' => isset($cartSummary['total_harga']) ? (float)$cartSummary['total_harga'] : 0,
                'details' => $cartDetails,
            ),
            'item' => array(
                'kd_barang' => $kdBarang,
                'nm_barang' => $nmBarang,
                'sat_barang' => $satBarang,
                'qty' => $lineQty,
                'harga' => (float)$hargaJual,
                'subtotal' => $lineSubtotal,
                'tipe' => (int)$jenisTransaksi,
                'resep' => $resep,
            ),
        );

        $db->commit();
        mobileRedirectKasir('success', 'Barang berhasil ditambahkan ke keranjang aktif.', $returnModule, $extraAjaxData);
    }

    if ($action === 'process_payment') {
        $jumlahBayar = mobileSanitizeNumber(isset($_POST['jumlah_bayar']) ? $_POST['jumlah_bayar'] : 0);
        $idCarabayar = isset($_POST['id_carabayar']) ? (int)$_POST['id_carabayar'] : 0;
        $idPelangganInput = isset($_POST['id_pelanggan']) ? (int)$_POST['id_pelanggan'] : 0;
        $idPelanggan = $idPelangganInput > 0 ? $idPelangganInput : mobileResolveDefaultPelangganId($db);
        $nmPelanggan = 'UMUM';
        $tlpPelanggan = '';
        $alamatPelanggan = '';

        if ($idPelanggan > 0 && mobileTableExists($db, 'pelanggan')) {
            $stmtPelanggan = $db->prepare("SELECT id_pelanggan, nm_pelanggan, tlp_pelanggan, alamat_pelanggan FROM pelanggan WHERE id_pelanggan = ? LIMIT 1");
            $stmtPelanggan->execute(array($idPelanggan));
            $rowPelanggan = $stmtPelanggan->fetch(PDO::FETCH_ASSOC);

            if ($rowPelanggan) {
                $nmPelanggan = isset($rowPelanggan['nm_pelanggan']) ? trim((string)$rowPelanggan['nm_pelanggan']) : 'UMUM';
                $tlpPelanggan = isset($rowPelanggan['tlp_pelanggan']) ? (string)$rowPelanggan['tlp_pelanggan'] : '';
                $alamatPelanggan = isset($rowPelanggan['alamat_pelanggan']) ? (string)$rowPelanggan['alamat_pelanggan'] : '';
            } else {
                $idPelanggan = mobileResolveDefaultPelangganId($db);
                if ($idPelanggan > 0) {
                    $stmtPelangganFallback = $db->prepare("SELECT id_pelanggan, nm_pelanggan, tlp_pelanggan, alamat_pelanggan FROM pelanggan WHERE id_pelanggan = ? LIMIT 1");
                    $stmtPelangganFallback->execute(array($idPelanggan));
                    $rowPelangganFallback = $stmtPelangganFallback->fetch(PDO::FETCH_ASSOC);
                    if ($rowPelangganFallback) {
                        $nmPelanggan = isset($rowPelangganFallback['nm_pelanggan']) ? trim((string)$rowPelangganFallback['nm_pelanggan']) : 'UMUM';
                        $tlpPelanggan = isset($rowPelangganFallback['tlp_pelanggan']) ? (string)$rowPelangganFallback['tlp_pelanggan'] : '';
                        $alamatPelanggan = isset($rowPelangganFallback['alamat_pelanggan']) ? (string)$rowPelangganFallback['alamat_pelanggan'] : '';
                    }
                }
            }
        }

        if ($nmPelanggan === '') {
            $nmPelanggan = 'UMUM';
        }

        $stmtTotal = $db->prepare("SELECT COALESCE(SUM(hrgttl_dtrkasir), 0) AS total_belanja FROM trkasir_detail WHERE kd_trkasir = ?");
        $stmtTotal->execute(array($kdTrkasir));
        $totalBelanja = (float)$stmtTotal->fetchColumn();

        if ($totalBelanja <= 0) {
            throw new Exception('Keranjang aktif masih kosong.');
        }

        $stmtCekPaid = $db->prepare("SELECT COUNT(*) FROM trkasir WHERE kd_trkasir = ?");
        $stmtCekPaid->execute(array($kdTrkasir));
        if ((int)$stmtCekPaid->fetchColumn() > 0) {
            $db->prepare("UPDATE kdtk SET stt_kdtk = 'OFF' WHERE id_admin = ? AND kd_trkasir = ?")->execute(array($idAdmin, $kdTrkasir));
            throw new Exception('Transaksi ini sudah diproses sebelumnya.');
        }

        if ($jumlahBayar <= 0) {
            throw new Exception('Jumlah bayar wajib diisi.');
        }

        if ($idCarabayar <= 0) {
            throw new Exception('Metode pembayaran wajib dipilih.');
        }

        $stmtCarabayar = $db->prepare("SELECT id_carabayar FROM carabayar WHERE id_carabayar = ? LIMIT 1");
        $stmtCarabayar->execute(array($idCarabayar));
        if (!$stmtCarabayar->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception('Metode pembayaran tidak valid.');
        }

        $uangKembalian = $jumlahBayar - $totalBelanja;
        if ($uangKembalian < 0) {
            $uangKembalian = 0;
        }

        $kurangBayar = $totalBelanja - $jumlahBayar;
        if ($kurangBayar < 0) {
            $kurangBayar = 0;
        }
        $sisaBayar = $uangKembalian;
        $ketTrx = 'LUNAS';
        if ($kurangBayar > 0) {
            $ketTrx = 'BELUM LUNAS | KURANG: Rp ' . number_format((float)$kurangBayar, 0, ',', '.');
        }

        $jenisTx = 1;
        $stmtJenisTx = $db->prepare("SELECT tipe FROM trkasir_detail WHERE kd_trkasir = ? ORDER BY id_dtrkasir DESC LIMIT 1");
        $stmtJenisTx->execute(array($kdTrkasir));
        $jenisTxRow = $stmtJenisTx->fetch(PDO::FETCH_ASSOC);
        if ($jenisTxRow && isset($jenisTxRow['tipe']) && (int)$jenisTxRow['tipe'] > 0) {
            $jenisTx = (int)$jenisTxRow['tipe'];
        }

        $insertTrkasir = $db->prepare("INSERT INTO trkasir (kd_trkasir, id_user, petugas, shift, tgl_trkasir, id_pelanggan, nm_pelanggan, tlp_pelanggan, alamat_pelanggan, kodetx, ttl_trkasir, diskon1, diskon2, dp_bayar, sisa_bayar, ket_trkasir, id_carabayar, jenistx, waktu_trx, poin_awal, tambahan_poin, redeem_poin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, '', ?, 0, 0, ?, ?, ?, ?, ?, ?, 0, 0, 0)");
        $insertTrkasir->execute(array(
            $kdTrkasir,
            $idAdmin,
            $petugas,
            $shiftAktif,
            date('Y-m-d'),
            $idPelanggan,
            $nmPelanggan,
            $tlpPelanggan,
            $alamatPelanggan,
            $totalBelanja,
            $jumlahBayar,
            $sisaBayar,
            $ketTrx,
            $idCarabayar,
            $jenisTx,
            date('Y-m-d H:i:s')
        ));

        $db->prepare("UPDATE trkasir_detail SET idadmin = ? WHERE kd_trkasir = ?")->execute(array($idAdmin, $kdTrkasir));

        if (mobileTableExists($db, 'kartu_stok')) {
            $db->prepare("INSERT INTO kartu_stok(kode_transaksi, tgl_sekarang) VALUES(?, ?)")->execute(array($kdTrkasir, date('Y-m-d H:i:s')));
        }

        $db->prepare("UPDATE kdtk SET stt_kdtk = 'OFF' WHERE id_admin = ? AND kd_trkasir = ?")->execute(array($idAdmin, $kdTrkasir));

        $db->commit();
        mobileRedirectKasir(
            'success',
            'Pembayaran berhasil diproses. Kode: ' . $kdTrkasir,
            $returnModule,
            array(
                'trx' => $kdTrkasir,
                'paid' => '1',
            )
        );
    }

    if ($action === 'get_sales_summary') {
        $idAdmin = (int)$_SESSION['idadmin'];
        $tglHari = isset($_POST['tgl_hari']) ? (string)$_POST['tgl_hari'] : date('Y-m-d');
        
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $stmtSummary = $db->prepare("SELECT 
                COALESCE(SUM(ttl_trkasir), 0) AS total_semua,
                COALESCE(SUM(CASE WHEN id_carabayar = 1 THEN ttl_trkasir ELSE 0 END), 0) AS total_tunai,
                COALESCE(SUM(CASE WHEN id_carabayar = 1 AND shift = 1 THEN ttl_trkasir ELSE 0 END), 0) AS tunai_pagi,
                COALESCE(SUM(CASE WHEN id_carabayar = 1 AND shift = 2 THEN ttl_trkasir ELSE 0 END), 0) AS tunai_sore,
                COALESCE(SUM(CASE WHEN id_carabayar = 2 THEN ttl_trkasir ELSE 0 END), 0) AS total_transfer,
                COALESCE(SUM(CASE WHEN id_carabayar = 2 AND shift = 1 THEN ttl_trkasir ELSE 0 END), 0) AS transfer_pagi,
                COALESCE(SUM(CASE WHEN id_carabayar = 2 AND shift = 2 THEN ttl_trkasir ELSE 0 END), 0) AS transfer_sore,
                COALESCE(SUM(CASE WHEN id_carabayar = 3 THEN ttl_trkasir ELSE 0 END), 0) AS total_tempo,
                COALESCE(SUM(CASE WHEN id_carabayar = 3 AND shift = 1 THEN ttl_trkasir ELSE 0 END), 0) AS tempo_pagi,
                COALESCE(SUM(CASE WHEN id_carabayar = 3 AND shift = 2 THEN ttl_trkasir ELSE 0 END), 0) AS tempo_sore,
                COUNT(*) AS jumlah_transaksi
            FROM trkasir 
            WHERE DATE(tgl_trkasir) = ? AND id_user = ?");
            
            $stmtSummary->execute(array($tglHari, $idAdmin));
            $summary = $stmtSummary->fetch(PDO::FETCH_ASSOC);
            
            if (!$summary) {
                $summary = array(
                    'total_semua' => 0,
                    'total_tunai' => 0,
                    'tunai_pagi' => 0,
                    'tunai_sore' => 0,
                    'total_transfer' => 0,
                    'transfer_pagi' => 0,
                    'transfer_sore' => 0,
                    'total_tempo' => 0,
                    'tempo_pagi' => 0,
                    'tempo_sore' => 0,
                    'jumlah_transaksi' => 0,
                );
            }

            echo json_encode(array(
                'status' => 'success',
                'summary' => $summary
            ));
        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 'error',
                'message' => $e->getMessage()
            ));
        }
        exit;
    }

    throw new Exception('Aksi kasir tidak dikenali.');
} catch (Exception $e) {
    if ($db instanceof PDO && $db->inTransaction()) {
        $db->rollBack();
    }
    mobileRedirectKasir('error', $e->getMessage(), $returnModule);
}
