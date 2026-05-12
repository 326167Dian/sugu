<?php
session_start();
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

$kdorders = $_GET['id'];

$query = $db->prepare("SELECT * FROM orders WHERE kd_trbmasuk = ?");
$query->execute([$kdorders]);
$res = $query->fetch(PDO::FETCH_ASSOC);
$alamat = $db->prepare("select * from supplier where id_supplier=?");
$alamat->execute([$res['id_supplier']]);
$alt = $alamat->fetch(PDO::FETCH_ASSOC);
//ambil header
$ah = $db->query("SELECT * FROM setheader ");
$rh = $ah->fetch(PDO::FETCH_ASSOC);

$pdf = new FPDF("P", "cm", "A4");

$pdf->SetMargins(1, 0.75, 1);
$pdf->AliasNbPages();
$pdf->AddPage();

// $pdf->Image('../../images/logo_yasfi.png',3,1.45,2,2.5,'');
$myImage = "../../images/".$rh['logo'];
$pdf->Image($myImage, 3,1.45,2,2.5);
$pdf->ln(1);
$pdf->SetFont('helvetica', 'B', 24);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(3.5, 0.4,'' , 0, 0, 'C');
$pdf->Cell(14, 0.4, $rh['satu'], 0, 1, 'C');

$pdf->ln(0.3);
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(3.5, 0.4,'' , 0, 0, 'C');


$pdf->Cell(14, 0.5,'No. SIPA '.$rh['tujuh'], 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(3.5, 0.4,'' , 0, 0, 'C');
$pdf->Cell(14, 0.5, $rh['dua'].' '.$rh['tiga'], 0, 1, 'C');
$pdf->Cell(3.5, 0.4,'' , 0, 0, 'C');
$pdf->Cell(14, 0.5,'SIA : '.$rh['lima'] .' Telp : '. $rh['enam'], 0, 1, 'C'); 

$pdf->SetLineWidth(0.15);
$pdf->Line(0.5, 4.15, 20.5, 4.15); //horisontal bawah

$pdf->ln(0.7);
$pdf->SetX(1);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(20.5, 0, 'SURAT PESANAN OBAT OBAT TERTENTU (OOT)', 0, 0, 'C');

$pre = substr($kdorders,4,12);
$pdf->ln(0.4);
$pdf->SetFont('Arial', '', 10);
$pdf->SetX(1);
$pdf->Cell(20.5, 0, 'Nomor SP : OOT-'.$pre, 0, 0, 'C');



$pdf->ln(0.7);
$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(14, 0, 'Yang bertandatangan di bawah ini :', 0, 0, 'L');


$pdf->ln(0.5);
$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2.5, 0, 'Nama', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(10, 0, $rh['empat'], 0, 0, 'L');

$pdf->ln(0.4);
$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2.5, 0, 'Jabatan', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(10, 0,'Apoteker Penanggung Jawab' , 0, 0, 'L');

$pdf->ln(0.4);
$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2.5, 0, 'No. SIPA', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(10, 0,$rh['tujuh'] , 0, 0, 'L');

$pdf->ln(0.4);
$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2.5, 0, 'Alamat', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(10, 0,$rh['duabelas'] , 0, 1, 'L');
$pdf->Cell(3, 0.8,'' , 0, 0, 'L');
$pdf->Cell(10, 0.8,'' , 0, 1, 'L');

$pdf->ln(0.2);
$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(14, 0, 'Mengajukan pesanan Obat Jadi OOT Farmasi kepada :', 0, 0, 'L');

$pdf->ln(0.4);
$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(3.5, 0, 'Nama Perusahaan', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(10, 0, $res['nm_supplier'], 0, 0, 'L');

$text1 = substr($alt['alamat_supplier'], 0,80);
$text2 = substr($alt['alamat_supplier'], 80,160);
$text3 = strlen($alt['alamat_supplier']);

if($text3 > 80) {
    $pdf->ln(0.4);
    $pdf->SetX(1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(3.5, 0, 'Alamat', 0, 0, 'L');
    $pdf->Cell(0.5, 0, ':', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(10, 0, $text1, 0, 0, 'L');

    $pdf->ln(0.4);
    $pdf->SetX(1);
    $pdf->Cell(3.5, 0, '', 0, 0, 'L');
    $pdf->Cell(0.5, 0, '', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(10, 0, $text2, 0, 1, 'L');
}
else {
    $pdf->ln(0.4);
    $pdf->SetX(1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(3.5, 0, 'Alamat', 0, 0, 'L');
    $pdf->Cell(0.5, 0, ':', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(10, 0, $text1, 0, 1, 'L');
}

$pdf->ln(0.4);
    $pdf->SetX(1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(3.5, 0, 'No Telp', 0, 0, 'L');
    $pdf->Cell(0.5, 0, ':', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(10, 0, $alt['tlp_supplier'], 0, 1, 'L');

$pdf->SetLineWidth(0);
$pdf->ln(0.8);
$current_y = $pdf->GetY(10);
$current_x = $pdf->GetX(1);
$cell_width=1;
$cell_height=1;
$pdf->SetX(1);
$pdf->SetFont('Arial', 'B', 9);
$pdf->MultiCell($cell_width,1.2,'No',1,'C');
$current_x =$cell_width + $current_x;
$pdf-> SetXY($current_x,$current_y);
$pdf->MultiCell(6.5, 1.2, 'Nama Obat Mengandung Obat Jadi OOT', 1, 'C');
$current_x = (6.5 + $current_x);
$pdf-> SetXY($current_x,$current_y);
$pdf->MultiCell(4, 0.6, 'Zat Aktif OOT / isi Kemasan', 1, 'C');
$current_x = (4 + $current_x);
$pdf-> SetXY($current_x,$current_y);
$pdf->MultiCell(3, 0.6, 'Bentuk dan Kekuatan', 1, 'C');
$current_x = (3 + $current_x);
$pdf-> SetXY($current_x,$current_y);
$pdf->MultiCell(1.5, 1.2, 'Satuan', 1, 'C');
$current_x = (1.5 + $current_x);
$pdf-> SetXY($current_x,$current_y);
$pdf->MultiCell(1.4, 1.2, 'Jumlah', 1, 'C');
$current_x = (1.4 + $current_x);
$pdf-> SetXY($current_x,$current_y);
$pdf->MultiCell(2.2, 1.2, 'Keterangan', 1, 'C');


$no = 1;
$query1 = $db->prepare("SELECT *
FROM ordersdetail join barang
on (ordersdetail.kd_barang=barang.kd_barang)
WHERE kd_trbmasuk = ?");
$query1->execute([$kdorders]);



while ($lihat = $query1->fetch(PDO::FETCH_ASSOC)) {
    $qty = ($lihat['qtygrosir_dtrbmasuk'] == "") ? $lihat['qty_dtrbmasuk'] : $lihat['qtygrosir_dtrbmasuk'];
    $satuan = ($lihat['satgrosir_dtrbmasuk'] == "") ? $lihat['sat_dtrbmasuk'] : $lihat['satgrosir_dtrbmasuk'];
    $zat = substr($lihat['ket_barang'],0,30);
    $kekuatan = substr($lihat['ket_barang'],-11,10);
    //atur pindah baris nama barang
    $text4 = substr($lihat['nmbrg_dtrbmasuk'],0,46);
    $text5 = substr($lihat['nmbrg_dtrbmasuk'],46,92);
    $text6 = strlen($lihat['nmbrg_dtrbmasuk']);
    //atur pindah baris zat aktif
    $text7 = substr($lihat['ket_barang'],0,25);
    $text8 = substr($lihat['ket_barang'],25,50);
    $text9 = strlen($lihat['ket_barang']);
    //atur pindah baris bentuk dan kekuatan
    $text10 = substr($lihat['dosis'],0,23);
    $text11 = substr($lihat['dosis'],23,46);
    $text12 = strlen($lihat['dosis']);
    //atur pindah baris pindah keterangan
    $ket = terbilang($qty);
    $text13 = substr($ket,0,10);
    $text14 = substr($ket,10,28);
    $text15 = strlen($ket);
    

    if($text6<47 || $text9<26) {
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(1, 0.7, $no, 1, 0, 'C');
        $pdf->Cell(6.5, 0.7, $text4, 1, 0, 'L');
        $pdf->Cell(4, 0.7, $text7, 1, 0, 'C');
        $pdf->Cell(3, 0.7, $text10, 1, 0, 'C');
        $pdf->Cell(1.5, 0.7, $satuan, 1, 0, 'C');
        $pdf->Cell(1.4, 0.7, $qty, 1, 0, 'C');
        $pdf->Cell(2.2, 0.7, $text13, 1, 1, 'C');
    }
    else {
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(1, 0.7, $no, 'LTR', 0, 'C');
        $pdf->Cell(6.5, 0.7, $text4, 'LTR', 0, 'L');
        $pdf->Cell(4, 0.7, $text7, 'LTR', 0, 'C');
        $pdf->Cell(3, 0.7, $text10, 'LTR', 0, 'C');
        $pdf->Cell(1.5, 0.7, $satuan, 'LTR', 0, 'C');
        $pdf->Cell(1.4, 0.7, $qty, 'LTR', 0, 'C');
        $pdf->Cell(2.2, 0.7, $text13, 'LTR', 1, 'C');

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(1, 0.5,'', 'LBR', 0, 'C');
        $pdf->Cell(6.5, 0.5, $text5, 'LBR', 0, 'L');
        $pdf->Cell(4, 0.5, $text8, 'LBR', 0, 'C');
        $pdf->Cell(3, 0.5, $text11, 'LBR', 0, 'C');
        $pdf->Cell(1.5, 0.5, '', 'LBR', 0, 'C');
        $pdf->Cell(1.4, 0.5, '', 'LBR', 0, 'C');
        $pdf->Cell(2.2, 0.5, $text14, 'LBR', 1, 'C');
    }
    $no++;
}

$pdf->ln(0.7);
$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(14, 0, 'Obat Jadi OOT Farmasi tersebut akan digunakan untuk :', 0, 0, 'L');

$pdf->ln(0.5);
$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2.5, 0, 'Nama', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(10, 0, $rh['satu'], 0, 0, 'L');

$pdf->ln(0.4);
$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2.5, 0, 'Alamat', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(10, 0, $rh['dua'].'  ' .$rh['tiga'] , 0, 0, 'L');

$pdf->ln(0.4);
$pdf->SetX(1);
$pdf->SetFont('', '', 10);
$pdf->Cell(2.5, 0, '', 0, 0, 'L');
$pdf->Cell(0.5, 0, '', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);


$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2.5, 0, 'Telp', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(10, 0,$rh['enam'] , 0, 0, 'L');

$pdf->ln(0.4);
$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2.5, 0, 'Surat Izin', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(10, 0, $rh['lima'] , 0, 1, 'L');

$pdf->ln(1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 0, '', 0, 0, 'R');
$pdf->Cell(9, 0, $rh['tigabelas'].', ' . tgl_indo($res['tgl_trbmasuk']), 0, 1, 'C');

$pdf->ln(0.4);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(10, 0, '', 0, 0, 'R');
$pdf->Cell(9, 0, 'Apoteker Pemesan,', 0, 0, 'C');

$signaturePath = "../../images/".$rh['tandatangan'];
$signatureFile = __DIR__."/../../images/".$rh['tandatangan'];
if (($res['tandatangan']) == 'YA' && $rh['tandatangan'] != '') {
    $pdf->ln(0.3);
    $pdf->SetX(14);
    $pdf->Image($signaturePath, $pdf->GetX(), $pdf->GetY()-0.5, 4, 4);
}

$pdf->ln(3);
$pdf->SetFont('Arial', 'BU', 10);
$pdf->Cell(10, 0, '', 0, 0, 'R');
$pdf->Cell(9, 0, $rh['empat'],0, 0, 'C');

$pdf->ln(0.4);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(10, 0, '', 0, 0, 'R');
$pdf->Cell(9, 0, $rh['tujuh'], 0, 0, 'C');
$pdf->Output("order-prekursor_".$res['tgl_trbmasuk'], "I");
?>