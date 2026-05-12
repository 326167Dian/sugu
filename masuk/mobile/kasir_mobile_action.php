<?php
session_start();
error_reporting(0);

include "../../configurasi/koneksi.php";

$db = isset($db) ? $db : null;

function mobileRedirectKasir($status, $message, $targetModule = 'kasir', $extraParams = array())
{
    $allowedModules = array('kasir', 'keranjang');
    $module = in_array($targetModule, $allowedModules, true) ? $targetModule : 'kasir';

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

    $kdTrkasir = mobileEnsureActiveKasirCode($db, $idAdmin);

    if ($action === 'add_item') {
        $barcode = trim(isset($_POST['barcode']) ? $_POST['barcode'] : '');
        $qty = (float)(isset($_POST['qty']) ? $_POST['qty'] : 0);

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
        if ($stokTersedia < $qtyRounded) {
            throw new Exception('Stok tidak mencukupi. Stok tersedia: ' . number_format($stokTersedia, 0, ',', '.'));
        }

        $hargaJual = 0;
        if (isset($barang['hrgjual_barang']) && (float)$barang['hrgjual_barang'] > 0) {
            $hargaJual = (float)$barang['hrgjual_barang'];
        } elseif (isset($barang['hrgjual_barang1']) && (float)$barang['hrgjual_barang1'] > 0) {
            $hargaJual = (float)$barang['hrgjual_barang1'];
        } elseif (isset($barang['hrgjual_barang2']) && (float)$barang['hrgjual_barang2'] > 0) {
            $hargaJual = (float)$barang['hrgjual_barang2'];
        } elseif (isset($barang['hrgsat_barang'])) {
            $hargaJual = (float)$barang['hrgsat_barang'];
        }

        $modal = isset($barang['hrgsat_barang']) ? (float)$barang['hrgsat_barang'] : 0;

        $komisi = 0;
        if (mobileHasAccess('komisi') && isset($barang['komisi'])) {
            $komisi = (float)$barang['komisi'] * $qtyRounded;
        }

        $stmtDetail = $db->prepare("SELECT * FROM trkasir_detail WHERE kd_trkasir = ? AND id_barang = ? AND (no_batch IS NULL OR no_batch = '') ORDER BY id_dtrkasir DESC LIMIT 1");
        $stmtDetail->execute(array($kdTrkasir, $idBarang));
        $detailLama = $stmtDetail->fetch(PDO::FETCH_ASSOC);

        if ($detailLama) {
            $qtyBaru = (float)$detailLama['qty_dtrkasir'] + $qtyRounded;
            $ttlBaru = $qtyBaru * $hargaJual;
            $profitBaru = $ttlBaru - ($modal * $qtyBaru);
            $komisiBaru = (float)$detailLama['komisi'] + $komisi;

            $stmtUpdate = $db->prepare("UPDATE trkasir_detail SET qty_dtrkasir = ?, hrgjual_dtrkasir = ?, hrgttl_dtrkasir = ?, modal = ?, profit = ?, komisi = ?, waktu = ? WHERE id_dtrkasir = ?");
            $stmtUpdate->execute(array($qtyBaru, $hargaJual, $ttlBaru, $modal, $profitBaru, $komisiBaru, date('Y-m-d H:i:s'), $detailLama['id_dtrkasir']));
        } else {
            $ttlHarga = $qtyRounded * $hargaJual;
            $profit = $ttlHarga - ($modal * $qtyRounded);

            $stmtInsert = $db->prepare("INSERT INTO trkasir_detail (kd_trkasir, id_barang, kd_barang, nmbrg_dtrkasir, qty_dtrkasir, sat_dtrkasir, hrgjual_dtrkasir, disc, modal, profit, waktu, hrgttl_dtrkasir, tipe, komisi, idadmin) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, ?, 1, ?, ?)");
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
                $komisi,
                $idAdmin
            ));
        }

        $stmtStok = $db->prepare("UPDATE barang SET stok_barang = stok_barang - ? WHERE id_barang = ? AND stok_barang >= ?");
        $stmtStok->execute(array($qtyRounded, $idBarang, $qtyRounded));
        if ($stmtStok->rowCount() <= 0) {
            throw new Exception('Gagal update stok barang. Coba ulangi.');
        }

        $db->commit();
        mobileRedirectKasir('success', 'Barang berhasil ditambahkan ke keranjang aktif.', $returnModule);
    }

    if ($action === 'process_payment') {
        $jumlahBayar = mobileSanitizeNumber(isset($_POST['jumlah_bayar']) ? $_POST['jumlah_bayar'] : 0);
        $idCarabayar = isset($_POST['id_carabayar']) ? (int)$_POST['id_carabayar'] : 0;
        $idPelanggan = mobileResolveDefaultPelangganId($db);

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

        $sisaBayar = $totalBelanja - $jumlahBayar;
        if ($sisaBayar < 0) {
            $sisaBayar = 0;
        }
        $ketTrx = $sisaBayar > 0 ? 'BELUM LUNAS' : 'LUNAS';

        $jenisTx = 1;
        $stmtJenisTx = $db->prepare("SELECT tipe FROM trkasir_detail WHERE kd_trkasir = ? ORDER BY id_dtrkasir DESC LIMIT 1");
        $stmtJenisTx->execute(array($kdTrkasir));
        $jenisTxRow = $stmtJenisTx->fetch(PDO::FETCH_ASSOC);
        if ($jenisTxRow && isset($jenisTxRow['tipe']) && (int)$jenisTxRow['tipe'] > 0) {
            $jenisTx = (int)$jenisTxRow['tipe'];
        }

        $insertTrkasir = $db->prepare("INSERT INTO trkasir (kd_trkasir, id_user, petugas, shift, tgl_trkasir, id_pelanggan, nm_pelanggan, tlp_pelanggan, alamat_pelanggan, kodetx, ttl_trkasir, diskon1, diskon2, dp_bayar, sisa_bayar, ket_trkasir, id_carabayar, jenistx, waktu_trx, poin_awal, tambahan_poin, redeem_poin) VALUES (?, ?, ?, ?, ?, ?, 'UMUM', '', '', '', ?, 0, 0, ?, ?, ?, ?, ?, ?, 0, 0, 0)");
        $insertTrkasir->execute(array(
            $kdTrkasir,
            $idAdmin,
            $petugas,
            $shiftAktif,
            date('Y-m-d'),
            $idPelanggan,
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

    throw new Exception('Aksi kasir tidak dikenali.');
} catch (Exception $e) {
    if ($db instanceof PDO && $db->inTransaction()) {
        $db->rollBack();
    }
    mobileRedirectKasir('error', $e->getMessage(), $returnModule);
}
