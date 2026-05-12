<?php
session_start();
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

$jenisobat = $_POST['jenisobat'];

$pdf = new FPDF("L","cm","A4");

$pdf->SetMargins(1,1,1);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(25.5,0.7,"STOK OPNAME RAK ".$jenisobat,0,10,'L');
$pdf->ln(0.5);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(1, 0.7, 'NO', 1, 0, 'C');
$pdf->Cell(3, 0.7, 'Kode Barang', 1, 0, 'L');
$pdf->Cell(11, 0.7, 'Nama Barang', 1, 0, 'L');
$pdf->Cell(1.5, 0.7, 'Quantity', 1, 0, 'C');
$pdf->Cell(2, 0.7, 'Satuan', 1, 0, 'C');
$pdf->Cell(3, 0.7, 'Stok Fisik', 1, 0, 'C');
$pdf->Cell(3, 0.7, 'Waktu', 1, 0, 'C');
$pdf->Cell(3, 0.7, 'ACC Manager', 1, 1, 'C');
$pdf->SetFont('Arial','',8);
$no=1;


$query=mysqli_query($GLOBALS["___mysqli_ston"], "select * from barang where jenisobat='$jenisobat' ORDER BY barang.nm_barang");

while($lihat=mysqli_fetch_array($query)){

	$pdf->Cell(1, 0.6, $no , 1, 0, 'C');
	$pdf->Cell(3, 0.6, $lihat['kd_barang'],1, 0, 'L');
	$pdf->Cell(11, 0.6, $lihat['nm_barang'], 1, 0,'L');
	$pdf->Cell(1.5, 0.6, $lihat['stok_barang'], 1, 0,'C');
	$pdf->Cell(2, 0.6, $lihat['sat_barang'], 1, 0,'C');
	$pdf->Cell(3, 0.6,'' , 1, 0,'R');
	$pdf->Cell(3, 0.6,'' , 1, 0,'R');
	$pdf->Cell(3, 0.6, '', 1, 1,'C');

	$no++;
}
$pdf->SetFont('Arial','',9);
$pdf->Cell(2,0.7,"Tanggal Cetak : ".date('d-m-Y h:i:s')." || Dicetak Oleh : ".$_SESSION['namalengkap'],0,0,'L');


$pdf->Output("Stok_Opname_Bulanan.pdf","I");

?>

