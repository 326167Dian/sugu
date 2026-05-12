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
$pdf->Cell(25.5,0.7,"LAPORAN DATA BARANG",0,10,'L');
$pdf->ln(0.5);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(1, 0.7, 'NO', 1, 0, 'C');
$pdf->Cell(3, 0.7, 'Kode Barang', 1, 0, 'L');
$pdf->Cell(11, 0.7, 'Nama Barang', 1, 0, 'L');
$pdf->Cell(1.5, 0.7, 'Qty', 1, 0, 'R');
$pdf->Cell(2, 0.7, 'Satuan', 1, 0, 'C');
$pdf->Cell(3, 0.7, 'Harga Beli', 1, 0, 'R');
$pdf->Cell(3, 0.7, 'Harga Jual', 1, 0, 'R');
$pdf->Cell(3, 0.7, 'Indikasi', 1, 1, 'C');
$pdf->SetFont('Arial','',8);
$no=1;


$query = $db->query("select * from barang ORDER BY barang.nm_barang");

while($lihat = $query->fetch(PDO::FETCH_ASSOC)){

	$pdf->Cell(1, 0.6, $no , 1, 0, 'C');
	$pdf->Cell(3, 0.6, $lihat['kd_barang'],1, 0, 'L');
	$pdf->Cell(11, 0.6, $lihat['nm_barang'], 1, 0,'L');
	$pdf->Cell(1.5, 0.6, $lihat['stok_barang'], 1, 0,'R');
	$pdf->Cell(2, 0.6, $lihat['sat_barang'], 1, 0,'C');
	$pdf->Cell(3, 0.6, format_rupiah($lihat['hrgsat_barang']), 1, 0,'R');
	$pdf->Cell(3, 0.6, format_rupiah($lihat['hrgjual_barang']), 1, 0,'R');
	$pdf->Cell(3, 0.6, 'lihat aplikasi', 1, 1,'C');

	$no++;
}
$pdf->SetFont('Arial','',9);
$pdf->Cell(2,0.7,"Tanggal Cetak : ".date('d-m-Y h:i:s')." || Dicetak Oleh : ".$_SESSION['namalengkap'],0,0,'L');


$pdf->Output("Laporan_data_barang.pdf","I");
