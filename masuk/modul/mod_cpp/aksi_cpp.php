<?php
session_start();
include "../../../configurasi/koneksi.php";

$module = isset($_GET['module']) ? $_GET['module'] : '';
$act = isset($_GET['act']) ? $_GET['act'] : '';

if ($module == 'cpp') {
    switch ($act) {
        case 'input':
            // Get form data
            $id_pelanggan = isset($_POST['id_pelanggan']) ? intval($_POST['id_pelanggan']) : null;
            $no_cpp = $_POST['no_cpp'] ?? '';
            $nama_pasien = $_POST['nama_pasien'] ?? '';
            $jk = $_POST['jk'] ?? '';
            $umur = $_POST['umur'] ?? '';
            $alamat = $_POST['alamat'] ?? '';
            $telp = $_POST['telp'] ?? '';
            $tgl_ttd = $_POST['tgl_ttd'] ?? '';
            $thn_ttd = $_POST['thn_ttd'] ?? '';
            $nama_apoteker = $_POST['nama_apoteker'] ?? '';
            $sipa_apoteker = $_POST['sipa_apoteker'] ?? '';
            $created_by = $_POST['created_by'] ?? '';
            
            // Detail arrays
            $tanggal_arr = $_POST['tanggal'] ?? [];
            $nama_dokter_arr = $_POST['nama_dokter'] ?? [];
            $nama_obat_dosis_arr = $_POST['nama_obat_dosis'] ?? [];
            $catatan_arr = $_POST['catatan'] ?? [];
            
            try {
                $db->beginTransaction();
                
                // Insert main CPP record
                $stmt = $db->prepare("INSERT INTO cpp (
                    id_pelanggan, no_cpp, nama_pasien, jk, umur, alamat, telp, 
                    tgl_ttd, thn_ttd, nama_apoteker, sipa_apoteker, created_by, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                
                $stmt->execute([
                    $id_pelanggan,
                    $no_cpp,
                    $nama_pasien,
                    $jk,
                    $umur,
                    $alamat,
                    $telp,
                    $tgl_ttd,
                    $thn_ttd,
                    $nama_apoteker,
                    $sipa_apoteker,
                    $created_by
                ]);
                
                $id_cpp = $db->lastInsertId();
                
                // Insert detail records
                $stmtDetail = $db->prepare("INSERT INTO cpp_detail (
                    id_cpp, no_urut, tanggal, nama_dokter, nama_obat_dosis, catatan
                ) VALUES (?, ?, ?, ?, ?, ?)");
                
                for ($i = 0; $i < count($tanggal_arr); $i++) {
                    // Only insert if at least one field is filled
                    if (!empty($tanggal_arr[$i]) || !empty($nama_dokter_arr[$i]) || 
                        !empty($nama_obat_dosis_arr[$i]) || !empty($catatan_arr[$i])) {
                        
                        $stmtDetail->execute([
                            $id_cpp,
                            $i + 1,
                            $tanggal_arr[$i] ?? '',
                            $nama_dokter_arr[$i] ?? '',
                            $nama_obat_dosis_arr[$i] ?? '',
                            $catatan_arr[$i] ?? ''
                        ]);
                    }
                }
                
                $db->commit();
                
                header("Location: ../../media_admin.php?module=cpp&info=success");
                exit;
                
            } catch (Exception $e) {
                $db->rollBack();
                echo "Error: " . $e->getMessage();
                exit;
            }
            break;

        case 'update':
            $id_cpp = isset($_POST['id_cpp']) ? intval($_POST['id_cpp']) : 0;
            $no_cpp = $_POST['no_cpp'] ?? '';
            $nama_pasien = $_POST['nama_pasien'] ?? '';
            $jk = $_POST['jk'] ?? '';
            $umur = $_POST['umur'] ?? '';
            $alamat = $_POST['alamat'] ?? '';
            $telp = $_POST['telp'] ?? '';
            $tgl_ttd = $_POST['tgl_ttd'] ?? '';
            $thn_ttd = $_POST['thn_ttd'] ?? '';
            $nama_apoteker = $_POST['nama_apoteker'] ?? '';
            $sipa_apoteker = $_POST['sipa_apoteker'] ?? '';
            
            // Detail arrays
            $id_detail_arr = $_POST['id_detail'] ?? [];
            $tanggal_arr = $_POST['tanggal'] ?? [];
            $nama_dokter_arr = $_POST['nama_dokter'] ?? [];
            $nama_obat_dosis_arr = $_POST['nama_obat_dosis'] ?? [];
            $catatan_arr = $_POST['catatan'] ?? [];
            
            try {
                $db->beginTransaction();
                
                // Update main CPP record
                $stmt = $db->prepare("UPDATE cpp SET 
                    nama_pasien = ?, jk = ?, umur = ?, alamat = ?, telp = ?,
                    tgl_ttd = ?, thn_ttd = ?, nama_apoteker = ?, sipa_apoteker = ?,
                    updated_at = NOW()
                    WHERE id_cpp = ?");
                
                $stmt->execute([
                    $nama_pasien,
                    $jk,
                    $umur,
                    $alamat,
                    $telp,
                    $tgl_ttd,
                    $thn_ttd,
                    $nama_apoteker,
                    $sipa_apoteker,
                    $id_cpp
                ]);
                
                // Delete old details
                $stmtDelete = $db->prepare("DELETE FROM cpp_detail WHERE id_cpp = ?");
                $stmtDelete->execute([$id_cpp]);
                
                // Insert new details
                $stmtDetail = $db->prepare("INSERT INTO cpp_detail (
                    id_cpp, no_urut, tanggal, nama_dokter, nama_obat_dosis, catatan
                ) VALUES (?, ?, ?, ?, ?, ?)");
                
                for ($i = 0; $i < count($tanggal_arr); $i++) {
                    // Only insert if at least one field is filled
                    if (!empty($tanggal_arr[$i]) || !empty($nama_dokter_arr[$i]) || 
                        !empty($nama_obat_dosis_arr[$i]) || !empty($catatan_arr[$i])) {
                        
                        $stmtDetail->execute([
                            $id_cpp,
                            $i + 1,
                            $tanggal_arr[$i] ?? '',
                            $nama_dokter_arr[$i] ?? '',
                            $nama_obat_dosis_arr[$i] ?? '',
                            $catatan_arr[$i] ?? ''
                        ]);
                    }
                }
                
                $db->commit();
                
                header("Location: ../../media_admin.php?module=cpp&info=update");
                exit;
                
            } catch (Exception $e) {
                $db->rollBack();
                echo "Error: " . $e->getMessage();
                exit;
            }
            break;

        case 'hapus':
            $id_cpp = isset($_GET['id']) ? intval($_GET['id']) : 0;
            
            try {
                // Delete will cascade to cpp_detail due to foreign key
                $stmt = $db->prepare("DELETE FROM cpp WHERE id_cpp = ?");
                $stmt->execute([$id_cpp]);
                
                header("Location: ../../media_admin.php?module=cpp&info=delete");
                exit;
                
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
            break;

        default:
            header("Location: ../../media_admin.php?module=cpp");
            exit;
    }
}
?>
