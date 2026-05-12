<?php
// session_start(); // Sudah aktif di media_admin.php
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";


$tgl_awal = $_POST['tgl_awal'];
$tgl_akhir = $_POST['tgl_akhir'];
if ($_POST['shift']<4){
	$shift = $_POST['shift'];}
else {
	$shift=("1,2,3");
}

$pdf = new FPDF("L","cm","A4");

$pdf->SetMargins(1,1,1);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(25.5,0.7,"LAPORAN PENJUALAN",0,10,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(5.5,0.5,"Tanggal Cetak : ".date('d-m-Y h:i:s'),0,0,'L');
$pdf->Cell(5,0.5,"Dicetak Oleh : ".$_SESSION['namalengkap'],0,1,'L');
$pdf->Cell(5.5,0.5,"Periode : ".tgl_indo($tgl_awal)." - ".tgl_indo($tgl_akhir),0,0,'L');
$pdf->Cell(5.5,0.5,"Shift : ".$shift,0,0,'R');

$pdf->Line(1,2.7,28.5,2.7); //horisontal bawah
$pdf->ln(0.5);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(1, 0.7, 'NO', 1, 0, 'C');
$pdf->Cell(2.5, 0.7, 'Kode Barang', 1, 0, 'L');
$pdf->Cell(11, 0.7, 'Nama Barang', 1, 0, 'L');
$pdf->Cell(1.5, 0.7, 'Qty', 1, 0, 'R');
$pdf->Cell(2, 0.7, 'Satuan', 1, 0, 'C');
$pdf->Cell(3, 0.7, 'Total', 1, 1, 'R');
$pdf->SetFont('Arial','',8);
$no=1;


$query = $db->query("SELECT 
trkasir_detail.kd_barang,
trkasir_detail.id_dtrkasir,
trkasir_detail.kd_trkasir,
trkasir.kd_trkasir,
trkasir.tgl_trkasir
FROM trkasir_detail 
JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
WHERE shift in ($shift) and trkasir.tgl_trkasir BETWEEN '$tgl_awal' AND '$tgl_akhir'
GROUP BY trkasir_detail.kd_barang");



while($lihat = $query->fetch(PDO::FETCH_ASSOC)){

	$query2 = $db->query("SELECT 
trkasir_detail.kd_barang,
trkasir_detail.id_dtrkasir,
trkasir_detail.kd_trkasir,
trkasir_detail.kd_barang,
SUM(trkasir_detail.qty_dtrkasir) as ttlqty,
SUM(trkasir_detail.hrgttl_dtrkasir) as ttlhrg,
trkasir.kd_trkasir,
trkasir.tgl_trkasir
FROM trkasir_detail 
JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
WHERE shift in ($shift) and trkasir_detail.kd_barang='$lihat[kd_barang]'
AND trkasir.tgl_trkasir BETWEEN '$tgl_awal' AND '$tgl_akhir'
ORDER BY trkasir_detail.id_dtrkasir ASC");

	$r2 = $query2->fetch(PDO::FETCH_ASSOC);
	$ttlqty = $r2['ttlqty'];
	$ttlhrg = $r2['ttlhrg'];


	$query3 = $db->prepare("SELECT * FROM trkasir_detail 
WHERE id_dtrkasir=?");
	$query3->execute([$lihat['id_dtrkasir']]);
	$r3 = $query3->fetch(PDO::FETCH_ASSOC);
	$kd_barang = $r3['kd_barang'];
	$nmbrg_dtrkasir = $r3['nmbrg_dtrkasir'];
	$sat_dtrkasir = $r3['sat_dtrkasir'];

	$pdf->Cell(1, 0.6, $no , 1, 0, 'C');
	$pdf->Cell(2.5, 0.6, $kd_barang,1, 0, 'L');
	$pdf->Cell(11, 0.6, $nmbrg_dtrkasir, 1, 0,'L');
	$pdf->Cell(1.5, 0.6, $ttlqty, 1, 0,'R');
	$pdf->Cell(2, 0.6, $sat_dtrkasir, 1, 0,'C');
	$pdf->Cell(3, 0.6, format_rupiah($ttlhrg), 1, 1,'R');
	$no++;
}



$total= "SELECT id_trkasir, kd_trkasir,id_carabayar,shift, SUM(ttl_trkasir)as ttlskrg1                                                              
                                        FROM trkasir WHERE shift in ($shift) and tgl_trkasir between '$tgl_awal' and '$tgl_akhir'" ;
$tunai = "SELECT id_trkasir, kd_trkasir, id_carabayar,shift, SUM(ttl_trkasir)as ttlskrg2                                                              
                                        FROM trkasir WHERE shift in ($shift) and tgl_trkasir BETWEEN '$tgl_awal' AND '$tgl_akhir' AND id_carabayar='1'" ;
$transfer = "SELECT id_trkasir, kd_trkasir, id_carabayar,shift, SUM(ttl_trkasir)as ttlskrg3                                                              
                                        FROM trkasir WHERE shift in ($shift) and tgl_trkasir BETWEEN '$tgl_awal' and '$tgl_akhir' AND id_carabayar='2'" ;
$tempo = "SELECT id_trkasir, kd_trkasir, id_carabayar, shift, SUM(ttl_trkasir)as ttlskrg4                                                              
                                        FROM trkasir WHERE shift in ($shift) and tgl_trkasir BETWEEN '$tgl_awal' and '$tgl_akhir' AND id_carabayar='3'" ;

$query2 = $db->query($total);
$query3 = $db->query($tunai);
$query4 = $db->query($transfer);
$query5 = $db->query($tempo);

$r2 = $query2->fetch(PDO::FETCH_ASSOC);
$ttlskrg = $r2['ttlskrg1'];
$ttlskrg2 = format_rupiah($ttlskrg);

$r3 = $query3->fetch(PDO::FETCH_ASSOC);
$ttltunai = $r3['ttlskrg2'];
$ttltunai2 = format_rupiah($ttltunai);

$r4 = $query4->fetch(PDO::FETCH_ASSOC);
$ttltransfer = $r4['ttlskrg3'];
$ttltransfer2 = format_rupiah($ttltransfer);

$r5 = $query5->fetch(PDO::FETCH_ASSOC);
$ttltempo = $r5['ttlskrg4'];
$ttltempo2 = format_rupiah($ttltempo);

$pdf->SetFont('Arial','B',9);
$pdf->Cell(18, 0.7, 'TUNAI', 1, 0, 'C');
$pdf->Cell(3, 0.7, $ttltunai2 , 1, 1, 'R');
$pdf->Cell(18, 0.7, 'TRANSFER', 1, 0, 'C');
$pdf->Cell(3, 0.7,$ttltransfer2 , 1, 1, 'R');
$pdf->Cell(18, 0.7, 'TEMPO', 1, 0, 'C');
$pdf->Cell(3, 0.7,$ttltempo2, 1, 1, 'R');
$pdf->Cell(18, 0.7, 'GRAND TOTAL', 1, 0, 'C');
$pdf->Cell(3, 0.7,$ttlskrg2, 1, 1, 'R');


$pdf->Output("Laporan_data_barang.pdf","I");

?>

