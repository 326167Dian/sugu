<?php
session_start();
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";


$pdf = new FPDF("L","cm","A4");

$pdf->SetMargins(1,1,1);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(25.5,0.7,"LAPORAN DATA SUPPLIER",0,10,'L');
$pdf->ln(0.5);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(1, 0.7, 'NO', 1, 0, 'C');
$pdf->Cell(8, 0.7, 'Nama Supplier', 1, 0, 'L');
$pdf->Cell(4, 0.7, 'Telepon', 1, 0, 'L');
$pdf->Cell(14, 0.7, 'Alamat', 1, 1, 'L');
$pdf->SetFont('Arial','',8);
$no=1;


$query = $db->query("select * from supplier");

while($lihat = $query->fetch(PDO::FETCH_ASSOC)){

	$pdf->Cell(1, 0.6, $no , 1, 0, 'C');
	$pdf->Cell(8, 0.6, $lihat['nm_supplier'],1, 0, 'L');
	$pdf->Cell(4, 0.6, $lihat['tlp_supplier'], 1, 0,'L');
	$pdf->Cell(14, 0.6, $lihat['alamat_supplier'], 1, 1,'L');

	$no++;
}
$pdf->SetFont('Arial','',9);
$pdf->Cell(2,0.7,"Tanggal Cetak : ".date('d-m-Y h:i:s')." || Dicetak Oleh : ".$_SESSION['namalengkap'],0,0,'L');


$pdf->Output("Laporan_data_supplier.pdf","I");

?>

