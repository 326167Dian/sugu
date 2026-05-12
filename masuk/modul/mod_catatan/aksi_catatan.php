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

    function normalize_catatan_html($html)
    {
        $html = trim((string)$html);
        if ($html === '') {
            return '';
        }

        if (function_exists('tidy_repair_string')) {
            $config = [
                'show-body-only' => true,
                'clean' => true,
                'output-html' => true,
                'wrap' => 0,
                'char-encoding' => 'utf8',
            ];
            $html = tidy_repair_string($html, $config, 'utf8');
        }

        if (class_exists('DOMDocument')) {
            $internalErrors = libxml_use_internal_errors(true);
            $dom = new DOMDocument('1.0', 'UTF-8');

            $flags = 0;
            if (defined('LIBXML_HTML_NOIMPLIED')) {
                $flags |= LIBXML_HTML_NOIMPLIED;
            }
            if (defined('LIBXML_HTML_NODEFDTD')) {
                $flags |= LIBXML_HTML_NODEFDTD;
            }

            $loaded = $dom->loadHTML('<?xml encoding="utf-8" ?><div id="catatan-root">' . $html . '</div>', $flags);
            if ($loaded) {
                foreach (['script', 'iframe', 'object'] as $tag) {
                    $nodes = $dom->getElementsByTagName($tag);
                    while ($nodes->length > 0) {
                        $node = $nodes->item(0);
                        $node->parentNode->removeChild($node);
                    }
                }

                $root = $dom->getElementById('catatan-root');
                if ($root) {
                    $normalized = '';
                    foreach ($root->childNodes as $childNode) {
                        $normalized .= $dom->saveHTML($childNode);
                    }
                    $normalized = trim($normalized);
                    libxml_clear_errors();
                    libxml_use_internal_errors($internalErrors);

                    if ($normalized !== '') {
                        return $normalized;
                    }
                }
            }

            libxml_clear_errors();
            libxml_use_internal_errors($internalErrors);
        }

        return nl2br(htmlspecialchars($html, ENT_QUOTES, 'UTF-8'));
    }

    $module=$_GET['module'];
    $act=$_GET['act'];
    
// Input admin
    if ($module=='catatan' AND $act=='input_catatan'){
        $deskripsi = normalize_catatan_html($_POST['deskripsi'] ?? '');
        
        $cekganda = $db->prepare("SELECT deskripsi FROM catatan WHERE deskripsi = ?");
        $cekganda->execute([$deskripsi]);
        $ada = $cekganda->rowCount();
        
        if ($ada > 0){
            echo "<script type='text/javascript'>alert('catatan sudah ada!');history.go(-1);</script>";
        }else{

            $db->prepare("INSERT INTO catatan (
                            tgl,shift,petugas,deskripsi)
							VALUES(?,?,?,?)")->execute([$_POST['tgl'], $_POST['shift'], $_POST['petugas'], $deskripsi]);

            //echo "<script type='text/javascript'>alert('Data berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
            header('location:../../media_admin.php?module='.$module);

        }
    }
    //update catatan
    elseif ($module=='catatan' AND $act=='update_catatan'){
        $deskripsi = normalize_catatan_html($_POST['deskripsi'] ?? '');

        $db->prepare("UPDATE catatan SET   
                                deskripsi = ?
									WHERE id_catatan = ?")->execute([$deskripsi, $_POST['id']]);

        //echo "<script type='text/javascript'>alert('Data berhasil diubah !');window.location='../../media_admin.php?module=".$module."'</script>";
        header('location:../../media_admin.php?module='.$module);

    }
//Hapus Proyek
    elseif ($module=='catatan' AND $act=='hapus'){
        $petugas = $_SESSION['namalengkap'];
        $edit = $db->prepare("SELECT * FROM catatan WHERE id_catatan = ?");
        $edit->execute([$_GET['id']]);
        $r = $edit->fetch(PDO::FETCH_ASSOC);

        if ( $petugas !== $r['petugas'] && $_SESSION['level']!=='pemilik')
        { echo "<script type='text/javascript'>alert('catatan harus dihapus orang yang sama atau pemilik apotek!');history.go(-1);</script>";}
        else{
            $db->prepare("DELETE FROM catatan WHERE id_catatan = ?")->execute([$_GET['id']]);
            //echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module."'</script>";
            header('location:../../media_admin.php?module='.$module);

        }
    }

}
?>
