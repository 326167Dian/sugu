<?php
session_start();
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";


$tgl_awal = $_POST['tgl_awal'];
$tgl_akhir = $_POST['tgl_akhir'];
$jenisobat = $_POST['jenisobat'];

$pdf = new FPDF("L","cm","A4");

$pdf->SetMargins(1,1,1);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(25.5,0.7,"LAPORAN LABA JENIS OBAT TERTENTU",0,10,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(5.5,0.5,"Tanggal Cetak : ".date('d-m-Y h:i:s'),0,0,'L');
$pdf->Cell(5,0.5,"Dicetak Oleh : ".$_SESSION['namalengkap'],0,1,'L');
$pdf->Cell(5.5,0.5,"Periode : ".tgl_indo($tgl_awal)." - ".tgl_indo($tgl_akhir),0,0,'L');
$pdf->Line(1,2.7,28.5,2.7); //horisontal bawah
$pdf->ln(0.5);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(1, 0.7, 'NO', 1, 0, 'C');
$pdf->Cell(2.5, 0.7, 'Kode Barang', 1, 0, 'L');
$pdf->Cell(11, 0.7, 'Nama Barang', 1, 0, 'L');
$pdf->Cell(1.5, 0.7, 'Qty', 1, 0, 'R');
$pdf->Cell(2, 0.7, 'Satuan', 1, 0, 'C');
$pdf->Cell(2, 0.7, 'Harga Beli', 1, 0, 'C');
$pdf->Cell(2, 0.7, 'Harga Jual', 1, 0, 'C');
$pdf->Cell(3, 0.7, 'Laba', 1, 1, 'R');
$pdf->SetFont('Arial','',8);
$no=1;


$query=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT 
trkasir_detail.kd_barang,
trkasir_detail.id_dtrkasir,
trkasir_detail.kd_trkasir,
trkasir.kd_trkasir,
trkasir.tgl_trkasir
FROM trkasir_detail 
JOIN trkasir ON (trkasir_detail.kd_trkasir = trkasir.kd_trkasir)
JOIN barang ON (trkasir_detail.kd_barang = barang.kd_barang)
WHERE barang.jenisobat ='$jenisobat' AND trkasir.tgl_trkasir BETWEEN '$tgl_awal' AND '$tgl_akhir'
GROUP BY trkasir_detail.kd_barang");



while($lihat=mysqli_fetch_array($query)){

$query2=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT 
trkasir_detail.kd_barang,
trkasir_detail.id_dtrkasir,
trkasir_detail.kd_trkasir,
trkasir_detail.kd_barang,
barang.id_barang,
barang.kd_barang,
barang.hrgsat_barang,
barang.hrgjual_barang,
SUM(trkasir_detail.qty_dtrkasir) as ttlqty,
trkasir.kd_trkasir,
trkasir.tgl_trkasir
FROM trkasir_detail 
JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
JOIN barang on trkasir_detail.kd_barang=barang.kd_barang
WHERE trkasir_detail.kd_barang='$lihat[kd_barang]'
AND trkasir.tgl_trkasir BETWEEN '$tgl_awal' AND '$tgl_akhir'
ORDER BY trkasir_detail.id_dtrkasir ASC");

$r2=mysqli_fetch_array($query2);
$ttlqty = $r2['ttlqty'];



$hrgsat_barang = $r2['hrgsat_barang'];
 $hrgsat_barang1 = format_rupiah($hrgsat_barang);
$hrgjual_barang = $r2['hrgjual_barang'];
 $hrgjual_barang1 = format_rupiah($hrgjual_barang);
$laba = ($hrgjual_barang - $hrgsat_barang) * $ttlqty;
	$laba1 = format_rupiah($laba);
$jumlah[]=$laba;
$omzet = $hrgjual_barang * $ttlqty;
$omzet1[]=$omzet;


$query3=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir_detail 
WHERE id_dtrkasir='$lihat[id_dtrkasir]'");
$r3=mysqli_fetch_array($query3);
$kd_barang = $r3['kd_barang'];
$nmbrg_dtrkasir = $r3['nmbrg_dtrkasir'];
$sat_dtrkasir = $r3['sat_dtrkasir'];

	$pdf->Cell(1, 0.6, $no , 1, 0, 'C');
	$pdf->Cell(2.5, 0.6, $kd_barang,1, 0, 'L');
	$pdf->Cell(11, 0.6, $nmbrg_dtrkasir, 1, 0,'L');
	$pdf->Cell(1.5, 0.6, $ttlqty, 1, 0,'R');
	$pdf->Cell(2, 0.6, $sat_dtrkasir, 1, 0,'C');
	$pdf->Cell(2, 0.6, $hrgsat_barang1, 1, 0,'C');
	$pdf->Cell(2, 0.6, $hrgjual_barang1, 1, 0,'C');
	$pdf->Cell(3, 0.6, $laba1, 1, 1,'R');
	$no++;

}
/*
var_dump($tgl_awal);
echo "<br>";
var_dump($tgl_akhir);
die();*/

$total_laba=array_sum($jumlah);
$total_omzet=array_sum($omzet1);
/*
$queryx=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT id_trkasir, kd_trkasir,id_carabayar, SUM(ttl_trkasir)as ttlskrg1                                                              
FROM trkasir WHERE tgl_trkasir between '$tgl_awal' and '$tgl_akhir'");
$rx=mysqli_fetch_array($queryx);
$ttlskrg = $rx['ttlskrg1'];
$ttlskrg2 = format_rupiah($ttlskrg);
/*$koneksi = $GLOBALS["___mysqli_ston"];
$query = "SELECT
SUM(a.hrgttl_dtrkasir) as gttl
FROM trkasir_detail AS a 
left join trkasir as b 
on a.kd_trkasir = b.kd_trkasir
where b.tgl_trkasir BETWEEN  '$tgl_awal' AND '$tgl_akhir'";
$result = $koneksi -> query($query);
$row = $result -> fetch_array();
/*$query4=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT SUM(hrgttl_dtrkasir) AS gttl FROM trkasir_detail WHERE tgl_trkasir BETWEEN '$tgl_awal' AND '$tgl_akhir'");
$query5=mysqli_fetch_assoc($query4);
var_dump($query5); die();*/
$pdf->SetFont('Arial','B',9);
$pdf->Cell(18, 0.7, 'TOTAL', 1, 0, 'C');
$pdf->Cell(4, 0.7, format_rupiah($total_omzet), 1, 0, 'C');

$pdf->Cell(3, 0.7,format_rupiah($total_laba) , 1, 0, 'R');



$pdf->Output("Laporan_Laba_JenisObat_Tertentu.pdf","I");

?>

