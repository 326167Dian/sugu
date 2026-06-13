<?php
session_start();
 if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
  echo "<link href='style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
  echo "<a href=../../index.php><b>LOGIN</b></a></center>";
}
else{
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_thumb.php";
include "../../../configurasi/library.php";

function build_riwayat_obat_items($db, $obatKds, $aturanPakaiList){
    $items = [];
    $summaryLines = [];

    if (!is_array($obatKds)) {
        $obatKds = [];
    }
    if (!is_array($aturanPakaiList)) {
        $aturanPakaiList = [];
    }

    $barangStmt = $db->prepare("SELECT kd_barang, nm_barang FROM barang WHERE kd_barang = ? LIMIT 1");

    foreach ($obatKds as $idx => $kdRaw) {
        $kd = trim((string) $kdRaw);
        $aturan = isset($aturanPakaiList[$idx]) ? trim((string) $aturanPakaiList[$idx]) : '';

        if ($kd === '' && $aturan === '') {
            continue;
        }

        if ($kd === '') {
            continue;
        }

        $barangStmt->execute([$kd]);
        $barang = $barangStmt->fetch(PDO::FETCH_ASSOC);
        if (!$barang) {
            continue;
        }

        $items[] = [
            'kd_barang' => (string) $barang['kd_barang'],
            'nm_barang' => (string) $barang['nm_barang'],
            'aturan_pakai' => $aturan
        ];

        $line = $barang['nm_barang'] . ' (' . $barang['kd_barang'] . ')';
        if ($aturan !== '') {
            $line .= ' - ' . $aturan;
        }
        $summaryLines[] = $line;
    }

    return [
        'items' => $items,
        'summary' => implode('; ', $summaryLines)
    ];
}

function get_riwayat_obat_table_name($db){
    $candidates = ['riwayat_pelanggan_obat', 'tabel_riwayat_pelanggan_obat'];
    foreach ($candidates as $tableName) {
        $stmt = $db->prepare("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? LIMIT 1");
        $stmt->execute([$tableName]);
        if ($stmt->fetchColumn() !== false) {
            return $tableName;
        }
    }

    return '';
}

$module=$_GET['module'];
$act=$_GET['act'];

// Input admin
if ($module=='pelanggan' AND $act=='input_pelanggan'){

$tanggal_lahir = isset($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : '';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_lahir)){
echo "<script type='text/javascript'>alert('Format tanggal lahir tidak valid.');history.go(-1);</script>";
exit;
}

$stmt = $db->prepare("SELECT COUNT(*) FROM pelanggan WHERE nm_pelanggan = ? AND tlp_pelanggan = ?");
$stmt->execute([$_POST['nm_pelanggan'], $_POST['tlp_pelanggan']]);
$ada = $stmt->fetchColumn();
if ($ada > 0){
echo "<script type='text/javascript'>alert('Nama Pelanggan dengan nomor telepon ini sudah ada!');history.go(-1);</script>";
}else{

    $stmt = $db->prepare("INSERT INTO pelanggan(nm_pelanggan, jenis_kelamin, tanggal_lahir, tlp_pelanggan, alamat_pelanggan, ket_pelanggan)
                                 VALUES(?, ?, ?, ?, ?, ?)");
    $stmt->execute([
    	$_POST['nm_pelanggan'],
    	$_POST['jenis_kelamin'],
    	$tanggal_lahir,
    	$_POST['tlp_pelanggan'],
    	$_POST['alamat_pelanggan'],
    	$_POST['ket_pelanggan']
    ]);
										
										
	//echo "<script type='text/javascript'>alert('Data berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
	header('location:../../media_admin.php?module='.$module);

}
}
 //updata pelanggan
 elseif ($module=='pelanggan' AND $act=='update_pelanggan'){

     $tanggal_lahir = isset($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : '';
     if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_lahir)){
     echo "<script type='text/javascript'>alert('Format tanggal lahir tidak valid.');history.go(-1);</script>";
     exit;
     }
 
     $stmt = $db->prepare("UPDATE pelanggan SET nm_pelanggan = ?,
                                jenis_kelamin = ?,
                                tanggal_lahir = ?,
                                tlp_pelanggan = ?,
                                alamat_pelanggan = ?,
                                ket_pelanggan = ?
                                WHERE id_pelanggan = ?");
    $stmt->execute([
		$_POST['nm_pelanggan'],
		$_POST['jenis_kelamin'],
		$tanggal_lahir,
		$_POST['tlp_pelanggan'],
		$_POST['alamat_pelanggan'],
		$_POST['ket_pelanggan'],
		$_POST['id']
	]);
									
	//echo "<script type='text/javascript'>alert('Data berhasil diubah !');window.location='../../media_admin.php?module=".$module."'</script>";
	header('location:../../media_admin.php?module='.$module);
	
}
//Hapus Proyek
elseif ($module=='pelanggan' AND $act=='hapus'){

    $stmt = $db->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ?");
    $stmt->execute([$_GET['id']]);
  //echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module."'</script>";
  header('location:../../media_admin.php?module='.$module);
}

// Input Riwayat Pelanggan
elseif ($module=='pelanggan' AND $act=='input_riwayat'){
    // CSRF check
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['csrf_pelanggan']){
        $_SESSION['flash'] = "<div class='alert alert-danger'>Token tidak valid. Coba ulangi.</div>";
        header('location:../../media_admin.php?module='.$module.'&act=riwayat&id=' . intval($_POST['id_pelanggan']));
        exit;
    }
    // basic validation
    $id_p = intval($_POST['id_pelanggan']);
    $tgl = $_POST['tgl'];
    $diagnosa = trim($_POST['diagnosa']);
    $obat_kd = isset($_POST['obat_kd']) ? $_POST['obat_kd'] : [];
    $aturan_pakai = isset($_POST['aturan_pakai']) ? $_POST['aturan_pakai'] : [];
    $followup = trim($_POST['followup']);

    $riwayatObatTable = get_riwayat_obat_table_name($db);
    if ($riwayatObatTable === '') {
        $_SESSION['flash'] = "<div class='alert alert-danger'>Tabel detail obat belum ada. Jalankan migration terbaru dulu.</div>";
        header('location:../../media_admin.php?module='.$module.'&act=riwayat&id='.$id_p);
        exit;
    }

    $obatPayload = build_riwayat_obat_items($db, $obat_kd, $aturan_pakai);
    $obatItems = $obatPayload['items'];
    $tindakan = $obatPayload['summary'];

    if (count($obatItems) < 1) {
        $_SESSION['flash'] = "<div class='alert alert-danger'>Minimal isi 1 obat pada tindakan.</div>";
        header('location:../../media_admin.php?module='.$module.'&act=riwayat&id='.$id_p);
        exit;
    }

    if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl)){
        $_SESSION['flash'] = "<div class='alert alert-danger'>Format tanggal tidak valid.</div>";
        header('location:../../media_admin.php?module='.$module.'&act=riwayat&id='.$id_p);
        exit;
    }

    // ensure pelanggan exists
    $stmt = $db->prepare("SELECT id_pelanggan FROM pelanggan WHERE id_pelanggan = ?");
    $stmt->execute([$id_p]);
    if($stmt->rowCount() < 1){
        $_SESSION['flash'] = "<div class='alert alert-danger'>Pelanggan tidak ditemukan.</div>";
        header('location:../../media_admin.php?module='.$module);
        exit;
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("INSERT INTO riwayat_pelanggan(id_pelanggan, tgl, diagnosa, tindakan, followup, created_at)
                                    VALUES(?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$id_p, $tgl, $diagnosa, $tindakan, $followup]);

        $id_riwayat = (int) $db->lastInsertId();
        $detailStmt = $db->prepare("INSERT INTO " . $riwayatObatTable . "(id_riwayat, kd_barang, nm_barang, aturan_pakai, created_at)
                                    VALUES(?, ?, ?, ?, NOW())");
        foreach ($obatItems as $item) {
            $detailStmt->execute([$id_riwayat, $item['kd_barang'], $item['nm_barang'], $item['aturan_pakai']]);
        }

        $db->commit();
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $_SESSION['flash'] = "<div class='alert alert-danger'>Gagal menyimpan riwayat: " . htmlspecialchars($e->getMessage()) . "</div>";
        header('location:../../media_admin.php?module='.$module.'&act=riwayat&id='.$id_p);
        exit;
    }

    // invalidate token so it can't be reused
    unset($_SESSION['csrf_pelanggan']);

    $_SESSION['flash'] = "<div class='alert alert-success'>Riwayat berhasil disimpan.</div>";
    header('location:../../media_admin.php?module='.$module.'&act=riwayat&id='.$id_p);
}

// Update Riwayat Pelanggan
elseif ($module=='pelanggan' AND $act=='update_riwayat'){
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['csrf_pelanggan']){
        $_SESSION['flash'] = "<div class='alert alert-danger'>Token tidak valid. Coba ulangi.</div>";
        header('location:../../media_admin.php?module='.$module.'&act=riwayat&id=' . intval($_POST['id_pelanggan']));
        exit;
    }
    $id_p = intval($_POST['id_pelanggan']);
    $id_r = intval($_POST['id_riwayat']);
    $tgl = $_POST['tgl'];
    $diagnosa = trim($_POST['diagnosa']);
    $obat_kd = isset($_POST['obat_kd']) ? $_POST['obat_kd'] : [];
    $aturan_pakai = isset($_POST['aturan_pakai']) ? $_POST['aturan_pakai'] : [];
    $followup = trim($_POST['followup']);

    $riwayatObatTable = get_riwayat_obat_table_name($db);
    if ($riwayatObatTable === '') {
        $_SESSION['flash'] = "<div class='alert alert-danger'>Tabel detail obat belum ada. Jalankan migration terbaru dulu.</div>";
        header('location:../../media_admin.php?module='.$module.'&act=edit_riwayat&id='.$id_p.'&idr='.$id_r);
        exit;
    }

    $obatPayload = build_riwayat_obat_items($db, $obat_kd, $aturan_pakai);
    $obatItems = $obatPayload['items'];
    $tindakan = $obatPayload['summary'];

    if (count($obatItems) < 1) {
        $_SESSION['flash'] = "<div class='alert alert-danger'>Minimal isi 1 obat pada tindakan.</div>";
        header('location:../../media_admin.php?module='.$module.'&act=edit_riwayat&id='.$id_p.'&idr='.$id_r);
        exit;
    }

    if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl)){
        $_SESSION['flash'] = "<div class='alert alert-danger'>Format tanggal tidak valid.</div>";
        header('location:../../media_admin.php?module='.$module.'&act=edit_riwayat&id='.$id_p.'&idr='.$id_r);
        exit;
    }

    $cek = $db->prepare("SELECT id FROM riwayat_pelanggan WHERE id = ? AND id_pelanggan = ?");
    $cek->execute([$id_r, $id_p]);
    if($cek->rowCount() < 1){
        $_SESSION['flash'] = "<div class='alert alert-danger'>Riwayat tidak ditemukan.</div>";
        header('location:../../media_admin.php?module='.$module.'&act=riwayat&id='.$id_p);
        exit;
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("UPDATE riwayat_pelanggan SET tgl = ?, diagnosa = ?, tindakan = ?, followup = ? WHERE id = ?");
        $stmt->execute([$tgl, $diagnosa, $tindakan, $followup, $id_r]);

        $db->prepare("DELETE FROM " . $riwayatObatTable . " WHERE id_riwayat = ?")->execute([$id_r]);

        $detailStmt = $db->prepare("INSERT INTO " . $riwayatObatTable . "(id_riwayat, kd_barang, nm_barang, aturan_pakai, created_at)
                                    VALUES(?, ?, ?, ?, NOW())");
        foreach ($obatItems as $item) {
            $detailStmt->execute([$id_r, $item['kd_barang'], $item['nm_barang'], $item['aturan_pakai']]);
        }

        $db->commit();
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $_SESSION['flash'] = "<div class='alert alert-danger'>Gagal memperbarui riwayat: " . htmlspecialchars($e->getMessage()) . "</div>";
        header('location:../../media_admin.php?module='.$module.'&act=edit_riwayat&id='.$id_p.'&idr='.$id_r);
        exit;
    }

    unset($_SESSION['csrf_pelanggan']);
    $_SESSION['flash'] = "<div class='alert alert-success'>Riwayat berhasil diperbarui.</div>";
    header('location:../../media_admin.php?module='.$module.'&act=riwayat&id='.$id_p);
}

// Hapus Riwayat Pelanggan
elseif ($module=='pelanggan' AND $act=='hapus_riwayat'){
    if (!isset($_GET['token']) || $_GET['token'] !== $_SESSION['csrf_pelanggan']){
        $_SESSION['flash'] = "<div class='alert alert-danger'>Token tidak valid. Coba ulangi.</div>";
        header('location:../../media_admin.php?module='.$module);
        exit;
    }
    $id = intval($_GET['id']);
    $q = $db->prepare("SELECT id_pelanggan FROM riwayat_pelanggan WHERE id = ?");
    $q->execute([$id]);
    if ($q->rowCount() < 1){
        $_SESSION['flash'] = "<div class='alert alert-danger'>Riwayat tidak ditemukan.</div>";
        header('location:../../media_admin.php?module='.$module);
        exit;
    }
    $row = $q->fetch(PDO::FETCH_ASSOC);
    $id_p = $row['id_pelanggan'];
    $riwayatObatTable = get_riwayat_obat_table_name($db);
    if ($riwayatObatTable !== '') {
        $db->prepare("DELETE FROM " . $riwayatObatTable . " WHERE id_riwayat = ?")->execute([$id]);
    }
    $stmt = $db->prepare("DELETE FROM riwayat_pelanggan WHERE id = ?");
    $stmt->execute([$id]);
    unset($_SESSION['csrf_pelanggan']);
    $_SESSION['flash'] = "<div class='alert alert-success'>Riwayat berhasil dihapus.</div>";
    header('location:../../media_admin.php?module='.$module.'&act=riwayat&id='.$id_p);
}
// Input Poin
elseif ($module=='pelanggan' AND $act=='input_poin'){
    $idpoin         = $_POST['id_poin'];
    $nm_outlet      = $_POST['nm_outlet'];
    $is_outlet      = isset($_POST['is_outlet']) ? 'ya' : 'no';
    $min_penjualan  = str_replace('.','',$_POST['min_penjualan']);
    $is_kelipatan   = isset($_POST['is_kelipatan']) ? 'ya' : 'no';
    $poin_member    = str_replace('.','',$_POST['poin_member']);
    
    $stmt_poin  = $db->prepare("SELECT * FROM poin_pelanggan WHERE id_poin = :id_poin");
    $stmt_poin->execute([
            ':id_poin'  => $idpoin
        ]);
        
    if ($stmt_poin->rowCount() > 0) {
        $update_poin = $db->prepare("UPDATE poin_pelanggan SET
                                        nm_outlet       = :nm_outlet,
                                        is_outlet       = :is_outlet,
                                        min_penjualan   = :min_penjualan,
                                        is_kelipatan    = :is_kelipatan,
                                        poin_pelanggan  = :poin_pelanggan
                                    WHERE id_poin = :id_poin");
        $update_poin->execute([
            ':nm_outlet'        => $nm_outlet,
            ':is_outlet'        => $is_outlet,
            ':min_penjualan'    => $min_penjualan,
            ':is_kelipatan'     => $is_kelipatan,
            ':poin_pelanggan'   => $poin_member,
            ':id_poin'          => $idpoin
        ]);
    } else {
        $insert_poin = $db->prepare("INSERT INTO poin_pelanggan(nm_outlet, is_outlet, min_penjualan, is_kelipatan, poin_pelanggan)
                        VALUES(?,?,?,?,?)");
        $insert_poin->execute([$nm_outlet, $is_outlet, $min_penjualan, $is_kelipatan, $poin_member]);
        
    }
    header('location:../../media_admin.php?module='.$module);
}
}
?>
