<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
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

function pelanggan_current_datetime()
{
    return date('Y-m-d H:i:s');
}

function ensure_riwayat_foto_column($db)
{
    static $checked = false;
    if ($checked) {
        return;
    }

    $checked = true;
    $stmt = $db->query("SHOW COLUMNS FROM riwayat_pelanggan LIKE 'foto'");
    if ($stmt && $stmt->rowCount() > 0) {
        return;
    }

    $db->exec("ALTER TABLE riwayat_pelanggan ADD COLUMN foto VARCHAR(255) NULL AFTER followup");
}

function ensure_riwayat_id_admin_column($db)
{
    static $checked = false;
    if ($checked) {
        return;
    }

    $checked = true;
    $stmt = $db->query("SHOW COLUMNS FROM riwayat_pelanggan LIKE 'id_admin'");
    if ($stmt && $stmt->rowCount() > 0) {
        return;
    }

    $db->exec("ALTER TABLE riwayat_pelanggan ADD COLUMN id_admin INT(11) NULL AFTER id_pelanggan");
}

function riwayat_image_upload_dir()
{
    return realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . 'images';
}

function upload_riwayat_foto($fileInput)
{
    if (!isset($_FILES[$fileInput])) {
        return ['ok' => true, 'filename' => ''];
    }

    $file = $_FILES[$fileInput];

    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['ok' => true, 'filename' => ''];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Upload foto gagal.'];
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return ['ok' => false, 'error' => 'Ukuran foto maksimal 2MB.'];
    }

    $originalName = isset($file['name']) ? $file['name'] : '';
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowedExt, true)) {
        return ['ok' => false, 'error' => 'Format foto tidak didukung.'];
    }

    if (function_exists('random_bytes')) {
        $suffix = bin2hex(random_bytes(4));
    } else {
        $suffix = str_replace('.', '', uniqid('', true));
    }
    $safeName = 'riwayat_' . date('Ymd_His') . '_' . $suffix . '.' . $ext;
    $targetDir = riwayat_image_upload_dir();
    if ($targetDir === false) {
        return ['ok' => false, 'error' => 'Folder images tidak ditemukan.'];
    }

    $targetPath = $targetDir . DIRECTORY_SEPARATOR . $safeName;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['ok' => false, 'error' => 'Gagal menyimpan file foto ke server.'];
    }

    return ['ok' => true, 'filename' => $safeName];
}

function delete_riwayat_foto_file($filename)
{
    $filename = trim((string) $filename);
    if ($filename === '') {
        return;
    }

    $path = riwayat_image_upload_dir() . DIRECTORY_SEPARATOR . $filename;
    if (is_file($path)) {
        @unlink($path);
    }
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

if (empty($_SESSION['unit'])) {
	echo "<script type='text/javascript'>alert('Akun Anda belum memiliki Unit Bisnis. Hubungi administrator untuk mengatur unit terlebih dahulu.');history.go(-1);</script>";
	exit;
}

$unit_admin = intval($_SESSION['unit']);

$stmt = $db->prepare("SELECT COUNT(*) FROM pelanggan WHERE nm_pelanggan = ? AND tlp_pelanggan = ?");
$stmt->execute([$_POST['nm_pelanggan'], $_POST['tlp_pelanggan']]);
$ada = $stmt->fetchColumn();
if ($ada > 0){
echo "<script type='text/javascript'>alert('Nama Pelanggan dengan nomor telepon ini sudah ada!');history.go(-1);</script>";
}else{

    $stmt = $db->prepare("INSERT INTO pelanggan(nm_pelanggan, jenis_kelamin, tanggal_lahir, tlp_pelanggan, alamat_pelanggan, ket_pelanggan, unit)
                                 VALUES(?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
    	$_POST['nm_pelanggan'],
    	$_POST['jenis_kelamin'],
    	$tanggal_lahir,
    	$_POST['tlp_pelanggan'],
    	$_POST['alamat_pelanggan'],
    	$_POST['ket_pelanggan'],
    	$unit_admin
    ]);
										
										
	//echo "<script type='text/javascript'>alert('Data berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
	header('location:../../media_admin.php?module='.$module);

}
}
 //updata pelanggan
 elseif ($module=='pelanggan' AND $act=='update_pelanggan'){

     if (empty($_SESSION['unit'])) {
     	echo "<script type='text/javascript'>alert('Akun Anda belum memiliki Unit Bisnis. Hubungi administrator untuk mengatur unit terlebih dahulu.');history.go(-1);</script>";
     	exit;
     }

     $unit_admin = intval($_SESSION['unit']);

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
                                ket_pelanggan = ?,
                                unit = ?
                                WHERE id_pelanggan = ?");
    $stmt->execute([
		$_POST['nm_pelanggan'],
		$_POST['jenis_kelamin'],
		$tanggal_lahir,
		$_POST['tlp_pelanggan'],
		$_POST['alamat_pelanggan'],
		$_POST['ket_pelanggan'],
		$unit_admin,
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
    try {
        ensure_riwayat_foto_column($db);
        ensure_riwayat_id_admin_column($db);
    } catch (Exception $e) {
        $_SESSION['flash'] = "<div class='alert alert-danger'>Gagal menyiapkan kolom foto: " . htmlspecialchars($e->getMessage()) . "</div>";
        header('location:../../media_admin.php?module='.$module.'&act=riwayat&id=' . intval($_POST['id_pelanggan']));
        exit;
    }

    // CSRF check
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['csrf_pelanggan']){
        $_SESSION['flash'] = "<div class='alert alert-danger'>Token tidak valid. Coba ulangi.</div>";
        header('location:../../media_admin.php?module='.$module.'&act=riwayat&id=' . intval($_POST['id_pelanggan']));
        exit;
    }
    // basic validation
    $id_p = intval($_POST['id_pelanggan']);
    $id_admin = isset($_SESSION['id_admin']) ? intval($_SESSION['id_admin']) : 0;
    $tgl = $_POST['tgl'];
    $diagnosa = trim($_POST['diagnosa']);
    $obat_kd = isset($_POST['obat_kd']) ? $_POST['obat_kd'] : [];
    $aturan_pakai = isset($_POST['aturan_pakai']) ? $_POST['aturan_pakai'] : [];
    $followup = trim($_POST['followup']);

    if ($id_admin < 1) {
        $_SESSION['flash'] = "<div class='alert alert-danger'>Session admin tidak valid. Silakan login ulang.</div>";
        header('location:../../media_admin.php?module='.$module.'&act=riwayat&id='.$id_p);
        exit;
    }

    $uploadFoto = upload_riwayat_foto('foto');
    if (!$uploadFoto['ok']) {
        $_SESSION['flash'] = "<div class='alert alert-danger'>" . htmlspecialchars($uploadFoto['error']) . "</div>";
        header('location:../../media_admin.php?module='.$module.'&act=riwayat&id='.$id_p);
        exit;
    }
    $foto = $uploadFoto['filename'];

    $tableCheck = $db->query("SHOW TABLES LIKE 'riwayat_pelanggan_obat'");
    if ($tableCheck->rowCount() < 1) {
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
        $created_at = pelanggan_current_datetime();

        $stmt = $db->prepare("INSERT INTO riwayat_pelanggan(id_pelanggan, id_admin, tgl, diagnosa, tindakan, followup, foto, created_at)
                VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_p, $id_admin, $tgl, $diagnosa, $tindakan, $followup, $foto, $created_at]);

        $id_riwayat = (int) $db->lastInsertId();
        $detailStmt = $db->prepare("INSERT INTO riwayat_pelanggan_obat(id_riwayat, kd_barang, nm_barang, aturan_pakai, created_at)
                                    VALUES(?, ?, ?, ?, ?)");
        foreach ($obatItems as $item) {
            $detailStmt->execute([$id_riwayat, $item['kd_barang'], $item['nm_barang'], $item['aturan_pakai'], $created_at]);
        }

        $db->commit();
    } catch (Exception $e) {
        if (!empty($foto)) {
            delete_riwayat_foto_file($foto);
        }
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
    try {
        ensure_riwayat_foto_column($db);
    } catch (Exception $e) {
        $_SESSION['flash'] = "<div class='alert alert-danger'>Gagal menyiapkan kolom foto: " . htmlspecialchars($e->getMessage()) . "</div>";
        header('location:../../media_admin.php?module='.$module.'&act=riwayat&id=' . intval($_POST['id_pelanggan']));
        exit;
    }

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

    $tableCheck = $db->query("SHOW TABLES LIKE 'riwayat_pelanggan_obat'");
    if ($tableCheck->rowCount() < 1) {
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

    $cek = $db->prepare("SELECT id, foto FROM riwayat_pelanggan WHERE id = ? AND id_pelanggan = ?");
    $cek->execute([$id_r, $id_p]);
    if($cek->rowCount() < 1){
        $_SESSION['flash'] = "<div class='alert alert-danger'>Riwayat tidak ditemukan.</div>";
        header('location:../../media_admin.php?module='.$module.'&act=riwayat&id='.$id_p);
        exit;
    }
    $cekRow = $cek->fetch(PDO::FETCH_ASSOC);
    $fotoLama = isset($cekRow['foto']) ? $cekRow['foto'] : '';

    $uploadFoto = upload_riwayat_foto('foto');
    if (!$uploadFoto['ok']) {
        $_SESSION['flash'] = "<div class='alert alert-danger'>" . htmlspecialchars($uploadFoto['error']) . "</div>";
        header('location:../../media_admin.php?module='.$module.'&act=edit_riwayat&id='.$id_p.'&idr='.$id_r);
        exit;
    }

    $fotoBaru = $uploadFoto['filename'];
    $fotoFinal = $fotoLama;
    if (!empty($fotoBaru)) {
        $fotoFinal = $fotoBaru;
    }

    try {
        $db->beginTransaction();
        $created_at = pelanggan_current_datetime();

        $stmt = $db->prepare("UPDATE riwayat_pelanggan SET tgl = ?, diagnosa = ?, tindakan = ?, followup = ?, foto = ? WHERE id = ?");
        $stmt->execute([$tgl, $diagnosa, $tindakan, $followup, $fotoFinal, $id_r]);

        $db->prepare("DELETE FROM riwayat_pelanggan_obat WHERE id_riwayat = ?")->execute([$id_r]);

        $detailStmt = $db->prepare("INSERT INTO riwayat_pelanggan_obat(id_riwayat, kd_barang, nm_barang, aturan_pakai, created_at)
                                    VALUES(?, ?, ?, ?, ?)");
        foreach ($obatItems as $item) {
            $detailStmt->execute([$id_r, $item['kd_barang'], $item['nm_barang'], $item['aturan_pakai'], $created_at]);
        }

        $db->commit();
        if (!empty($fotoBaru) && !empty($fotoLama) && $fotoLama !== $fotoBaru) {
            delete_riwayat_foto_file($fotoLama);
        }
    } catch (Exception $e) {
        if (!empty($fotoBaru)) {
            delete_riwayat_foto_file($fotoBaru);
        }
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
    try {
        ensure_riwayat_foto_column($db);
    } catch (Exception $e) {
        $_SESSION['flash'] = "<div class='alert alert-danger'>Gagal menyiapkan kolom foto: " . htmlspecialchars($e->getMessage()) . "</div>";
        header('location:../../media_admin.php?module='.$module);
        exit;
    }

    if (!isset($_GET['token']) || $_GET['token'] !== $_SESSION['csrf_pelanggan']){
        $_SESSION['flash'] = "<div class='alert alert-danger'>Token tidak valid. Coba ulangi.</div>";
        header('location:../../media_admin.php?module='.$module);
        exit;
    }
    $id = intval($_GET['id']);
    $q = $db->prepare("SELECT id_pelanggan, foto FROM riwayat_pelanggan WHERE id = ?");
    $q->execute([$id]);
    if ($q->rowCount() < 1){
        $_SESSION['flash'] = "<div class='alert alert-danger'>Riwayat tidak ditemukan.</div>";
        header('location:../../media_admin.php?module='.$module);
        exit;
    }
    $row = $q->fetch(PDO::FETCH_ASSOC);
    $id_p = $row['id_pelanggan'];
    $foto = isset($row['foto']) ? $row['foto'] : '';
    $db->prepare("DELETE FROM riwayat_pelanggan_obat WHERE id_riwayat = ?")->execute([$id]);
    $stmt = $db->prepare("DELETE FROM riwayat_pelanggan WHERE id = ?");
    $stmt->execute([$id]);
    delete_riwayat_foto_file($foto);
    unset($_SESSION['csrf_pelanggan']);
    $_SESSION['flash'] = "<div class='alert alert-success'>Riwayat berhasil dihapus.</div>";
    header('location:../../media_admin.php?module='.$module.'&act=riwayat&id='.$id_p);
}

}
?>
