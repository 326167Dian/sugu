<?php
session_start();
include "../../../configurasi/koneksi.php";

$module = isset($_GET['module']) ? $_GET['module'] : '';
$act = isset($_GET['act']) ? $_GET['act'] : '';

if ($module == 'homecare') {
    switch ($act) {
        case 'input':
            // Get form data
            $id_pelanggan = isset($_POST['id_pelanggan']) ? intval($_POST['id_pelanggan']) : null;
            $no_homecare = $_POST['no_homecare'] ?? '';
            $nama_pasien = $_POST['nama_pasien'] ?? '';
            $umur = $_POST['umur'] ?? '';
            $alamat = $_POST['alamat'] ?? '';
            $telp = $_POST['telp'] ?? '';
            $created_by = $_POST['created_by'] ?? '';
            
            // Detail arrays
            $tgl_kunjungan_arr = $_POST['tgl_kunjungan'] ?? [];
            $catatan_apoteker_arr = $_POST['catatan_apoteker'] ?? [];
            
            try {
                $db->beginTransaction();
                
                // Insert main HOMECARE record
                $stmt = $db->prepare("INSERT INTO homecare (
                    id_pelanggan, no_homecare, nama_pasien, umur, alamat, telp, created_by, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                
                $stmt->execute([
                    $id_pelanggan,
                    $no_homecare,
                    $nama_pasien,
                    $umur,
                    $alamat,
                    $telp,
                    $created_by
                ]);
                
                $id_homecare = $db->lastInsertId();
                
                // Insert detail records
                $stmtDetail = $db->prepare("INSERT INTO homecare_detail (
                    id_homecare, no_urut, tgl_kunjungan, catatan_apoteker
                ) VALUES (?, ?, ?, ?)");
                
                for ($i = 0; $i < count($tgl_kunjungan_arr); $i++) {
                    // Only insert if at least one field is filled
                    if (!empty($tgl_kunjungan_arr[$i]) || !empty($catatan_apoteker_arr[$i])) {
                        $stmtDetail->execute([
                            $id_homecare,
                            $i + 1,
                            $tgl_kunjungan_arr[$i] ?? '',
                            $catatan_apoteker_arr[$i] ?? ''
                        ]);
                    }
                }
                
                $db->commit();
                
                header("Location: ../../media_admin.php?module=homecare&info=success");
                exit;
                
            } catch (Exception $e) {
                $db->rollBack();
                echo "Error: " . $e->getMessage();
                exit;
            }
            break;

        case 'update':
            $id_homecare = isset($_POST['id_homecare']) ? intval($_POST['id_homecare']) : 0;
            $no_homecare = $_POST['no_homecare'] ?? '';
            $nama_pasien = $_POST['nama_pasien'] ?? '';
            $umur = $_POST['umur'] ?? '';
            $alamat = $_POST['alamat'] ?? '';
            $telp = $_POST['telp'] ?? '';
            
            // Detail arrays
            $id_detail_arr = $_POST['id_detail'] ?? [];
            $tgl_kunjungan_arr = $_POST['tgl_kunjungan'] ?? [];
            $catatan_apoteker_arr = $_POST['catatan_apoteker'] ?? [];
            
            try {
                $db->beginTransaction();
                
                // Update main HOMECARE record
                $stmt = $db->prepare("UPDATE homecare SET 
                    nama_pasien = ?, umur = ?, alamat = ?, telp = ?,
                    updated_at = NOW()
                    WHERE id_homecare = ?");
                
                $stmt->execute([
                    $nama_pasien,
                    $umur,
                    $alamat,
                    $telp,
                    $id_homecare
                ]);
                
                // Delete old details
                $stmtDelete = $db->prepare("DELETE FROM homecare_detail WHERE id_homecare = ?");
                $stmtDelete->execute([$id_homecare]);
                
                // Insert new details
                $stmtDetail = $db->prepare("INSERT INTO homecare_detail (
                    id_homecare, no_urut, tgl_kunjungan, catatan_apoteker
                ) VALUES (?, ?, ?, ?)");
                
                for ($i = 0; $i < count($tgl_kunjungan_arr); $i++) {
                    // Only insert if at least one field is filled
                    if (!empty($tgl_kunjungan_arr[$i]) || !empty($catatan_apoteker_arr[$i])) {
                        $stmtDetail->execute([
                            $id_homecare,
                            $i + 1,
                            $tgl_kunjungan_arr[$i] ?? '',
                            $catatan_apoteker_arr[$i] ?? ''
                        ]);
                    }
                }
                
                $db->commit();
                
                header("Location: ../../media_admin.php?module=homecare&info=update");
                exit;
                
            } catch (Exception $e) {
                $db->rollBack();
                echo "Error: " . $e->getMessage();
                exit;
            }
            break;

        case 'hapus':
            $id_homecare = isset($_GET['id']) ? intval($_GET['id']) : 0;
            
            try {
                // Delete will cascade to homecare_detail due to foreign key
                $stmt = $db->prepare("DELETE FROM homecare WHERE id_homecare = ?");
                $stmt->execute([$id_homecare]);
                
                header("Location: ../../media_admin.php?module=homecare&info=delete");
                exit;
                
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
            break;

        default:
            header("Location: ../../media_admin.php?module=homecare");
            exit;
    }
}
?>
