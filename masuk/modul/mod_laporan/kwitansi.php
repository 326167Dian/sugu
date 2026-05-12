<?php
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/rpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

$kd=$_GET['kd_trkasir'];
$tampil = $db->prepare("SELECT * FROM trkasir WHERE kd_trkasir='$kd'");
$tampil->execute();
$lihat = $tampil->fetch(PDO::FETCH_ASSOC);
$panjang = terbilang($lihat['ttl_trkasir']);
$text1 = substr($panjang, 0,50);
$text2 = substr($panjang, 50,100);
$text3 = strlen($panjang);

//ambil header
$ah = $db->prepare("SELECT * FROM setheader");
$ah->execute();
$rh = $ah->fetch(PDO::FETCH_ASSOC);


$pdf=new RPDF("P", "cm", "A4");
$pdf->AddPage();

//$pdf->TextWithRotation(10,10,$kd,45,0);
//$pdf->SetFontSize(30);
//$pdf->TextWithDirection(10,5,'world-1!','L');
//$pdf->TextWithDirection(10,5,'world-2!','U');
//$pdf->TextWithDirection(10,5,'world-3!','R');
//$pdf->TextWithDirection(10,5,'world-4!','D');

$text = substr($rh['satu'], 7);

$pdf->SetFont('Arial','B', 10);
$pdf->TextWithDirection(1.5,5.7,'APOTEK','U');
$pdf->TextWithDirection(2,5.7,$text,'U');
$pdf->SetFont('Arial','', 7);
$pdf->TextWithDirection(2.5,5.7,$rh['dua'],'U');
$pdf->TextWithDirection(2.8,5.7,$rh['tiga'],'U');
$pdf->TextWithDirection(3.1,5.7,$rh['tujuh'],'U');
// $pdf->Image('../../images/logo90.png',1.2,5.8,2.5,2.0,'');
$myImage = "../../images/".$rh['logo'];
//$pdf->Image($myImage,1.2,5.8,2.5,2.0,'');
$pdf->RotatedImage($myImage, 1.2, 7.8, 2.0, 2.0, -270);



$pdf->SetLineWidth(0.1);
$pdf->Line(0.8, 0.7, 0.8, 8); //vertikal atas
$pdf->Line(4, 0.7, 4, 8); //vertikal tengah
$pdf->Line(20, 0.7, 20, 8); //vertikal akhir
$pdf->Line(0.8, 0.7, 20, 0.7); //Horizontal Atas
$pdf->Line(0.8, 8, 20, 8); //Horizontal Bawah

$pdf->ln(0.5);
$pdf->SetFont('Arial','', 12);
$pdf->Cell(3.5, 0.5,'', 0, 0, 'R');
$pdf->Cell(4.2, 0.5,'No Transaksi', 0, 0, 'L');
$pdf->Cell(0.1, 0.5,':', 0, 0, 'C');
$pdf->Cell(3, 0.5,$kd, 0, 1, 'L');
$pdf->Cell(3.5, 0.5,'', 0, 0, 'R');
$pdf->Cell(4.2, 0.5,'Telah Terima dari', 0, 0, 'L');
$pdf->Cell(0.1, 0.5,':', 0, 0, 'C');
$pdf->Cell(3, 0.5,$lihat['nm_pelanggan'], 0, 1, 'L');
if ($text3<51)
  {
    $pdf->Cell(3.5, 0.5,'', 0, 0, 'R');
    $pdf->Cell(4.2, 0.5,'Uang Sejumlah', 0, 0, 'L');
    $pdf->Cell(0.1, 0.5,':', 0, 0, 'C');
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(11, 0.5,$text1.' Rupiah', 0, 1, 'L',1);
  }
else{
    $pdf->Cell(3.5, 0.5,'', 0, 0, 'R');
    $pdf->Cell(4.2, 0.5,'Uang Sejumlah', 0, 0, 'L');
    $pdf->Cell(0.1, 0.5,':', 0, 0, 'C');
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(11, 0.5,$text1, 0, 1, 'L',1);
    $pdf->Cell(3.5, 0.5,'', 0, 0, 'R');
    $pdf->Cell(4.2, 0.5,'', 0, 0, 'L');
    $pdf->Cell(0.1, 0.5,'', 0, 0, 'C');
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(11, 0.5,$text2.' Rupiah', 0, 1, 'L',1);
  }
$pdf->ln(0.5);
$pdf->SetFont('Arial','', 12);
$pdf->Cell(3.5, 0.5,'', 0, 0, 'R');
$pdf->Cell(4.2, 0.5,'Untuk Pembayaran', 0, 0, 'L');
$pdf->Cell(0.1,0.5,':',0,0,'C');
$pdf->Cell(11,0.5,'Pembelian obat - obatan',0,'L');


$pdf->ln(0.5);
$pdf->SetFont('Arial','', 12);
$pdf->Cell(3.5, 0.5,'', 0, 0, 'R');
$pdf->Cell(7, 0.5,'', 0, 0, 'L');
$pdf->Cell(7, 0.5,$rh['tigabelas'].', '.tgl_indo($lihat['tgl_trkasir']), 0, 1, 'C');

$pdf->ln(2);
$pdf->SetFont('Arial','', 12);
$pdf->Cell(3.5, 0.5,'', 0, 0, 'R');
$pdf->SetFillColor(220, 220, 220);
$pdf->SetFont('Arial','', 20);
$pdf->Cell(7, 0.5,'Rp. '.format_rupiah($lihat['ttl_trkasir']).',-', 0, 0, 'L',1);
$pdf->SetFont('Arial','', 12);
$pdf->Cell(7, 0.5,$lihat['petugas'], 0, 1, 'C');

$pdf->Output();
?>