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

$pdf = new FPDF("P", "cm", "A5");

$pdf->SetMargins(1, 0, 1);
$pdf->AliasNbPages();
$pdf->AddPage();

// $pdf->Image('../../images/logo.png',1,0.7,2,2.5,'');
$myImage = "../../images/".$rh['logo'];
$pdf->Image($myImage, 1, 0.7, 2,2.3);
$pdf->ln(1);
$pdf->SetFont('helvetica', 'B', 20);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(2, 0.4,'' , 0, 0, 'C');
$pdf->Cell(10, 0.4, $rh['satu'], 0, 1, 'C');

$pdf->ln(0.3);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(2.5, 0.4,'' , 0, 0, 'C');
$pdf->Cell(10, 0.5,$rh['dua'].' '. $rh['tiga'], 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2.5, 0.4,'' , 0, 0, 'C');
$pdf->Cell(10, 0.5,'SIA : '.$rh['lima'].'  '.'Telp : '.$rh['enam'] , 0, 1, 'C');
$pdf->Cell(2.5, 0.4,'' , 0, 0, 'C');
$pdf->Cell(10, 0.5,'APJ : '.$rh['empat'], 0, 1, 'C');

$pdf->SetLineWidth(0.15);
$pdf->Line(0.5, 3.3, 14.3, 3.3); //horisontal bawah

$pdf->ln(0.7);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(14, 0, 'SURAT PESANAN ALAT KESEHATAN', 0, 0, 'C');

$pdf->ln(1);
$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2.5, 0, 'Nomor SP', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(10, 0, $kdorders, 0, 0, 'L');

$pdf->ln(0.5);
$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2.5, 0, 'Tanggal', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(10, 0, tgl_indo($res['tgl_trbmasuk']), 0, 0, 'L');

$pdf->ln(0.5);
$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2.5, 0, 'Kepada', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(10, 0, $res['nm_supplier'], 0, 0, 'L');

$pdf->ln(0.5);
$pdf->SetX(1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2.5, 0, 'Alamat', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);

$text1 = substr($alt['alamat_supplier'], 0,55);
$text2 = substr($alt['alamat_supplier'], 55,108);

$pdf->Cell(10, 0,$text1 , 0, 1, 'L');
$pdf->Cell(2.8, 0.7,' ' , 0, 0, 'L');
$pdf->Cell(10, 0.7, $text2, 0, 0, 'L');

$pdf->SetLineWidth(0);
$pdf->ln(0.8);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(1, 0.7, 'No.', 1, 0, 'C');
$pdf->Cell(6.5, 0.7, 'Nama Alat Kesehatan', 1, 0, 'C');
$pdf->Cell(1.5, 0.7, 'Satuan', 1, 0, 'C');
$pdf->Cell(1.5, 0.7, 'Jumlah', 1, 0, 'C');
$pdf->Cell(2.5, 0.7, 'Ket', 1, 0, 'C');
// $pdf->ln(0.7);
// $pdf->SetFont('Arial', '', 10);

$no = 1;
$query1 = $db->prepare("SELECT *
FROM ordersdetail
WHERE kd_trbmasuk = ?");
$query1->execute([$kdorders]);

while ($lihat = $query1->fetch(PDO::FETCH_ASSOC)) {
    $qty = ($lihat['qtygrosir_dtrbmasuk'] == "") ? $lihat['qty_dtrbmasuk'] : $lihat['qtygrosir_dtrbmasuk'];
    $satuan = ($lihat['satgrosir_dtrbmasuk'] == "") ? $lihat['sat_dtrbmasuk'] : $lihat['satgrosir_dtrbmasuk'];
    
    $pdf->ln(0.7);
    $pdf->SetFont('Arial', '', 10);
    
    $pdf->Cell(1, 0.7, $no, 1, 0, 'C');
    $pdf->Cell(6.5, 0.7, $lihat['nmbrg_dtrbmasuk'], 1, 0, 'L');
    $pdf->Cell(1.5, 0.7, $satuan, 1, 0, 'C');
    $pdf->Cell(1.5, 0.7, $qty, 1, 0, 'C');
    $pdf->Cell(2.5, 0.7, terbilang($qty), 1, 0, 'C');
    $no++;
}

$pdf->ln(1.5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(5, 0, '', 0, 0, 'R');
$pdf->Cell(9, 0, $rh['tigabelas'].', ' . tgl_indo(date("Y-m-d")), 0, 1, 'C');

$pdf->ln(0.4);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(5, 0, '', 0, 0, 'R');
$pdf->Cell(9, 0, 'Apoteker Pemesan,', 0, 0, 'C');

$signaturePath = "../../images/".$rh['tandatangan'];
$signatureFile = __DIR__."/../../images/".$rh['tandatangan'];
if (($res['tandatangan']) == 'YA' && $rh['tandatangan'] != '') {
    $pdf->ln(0.3);
    $pdf->SetX(9);
    $pdf->Image($signaturePath, $pdf->GetX(), $pdf->GetY()-0.5, 4, 4);
}

$pdf->ln(2.5);
$pdf->SetFont('Arial', 'BU', 10);
$pdf->Cell(5, 0, '', 0, 0, 'R');
$pdf->Cell(9, 0,$rh['empat'],0, 0, 'C');

$pdf->ln(0.4);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(5, 0, '', 0, 0, 'R');
$pdf->Cell(9, 0,$rh['tujuh'], 0, 0, 'C');
$pdf->Output("order".$res['tgl_trbmasuk'], "I");
?>