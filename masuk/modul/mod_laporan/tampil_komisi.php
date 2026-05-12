<?php
session_start();
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";


$tgl_awal = date('Y-m-d',strtotime($_POST['tgl_awal']));
$tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));

$pdf = new FPDF("L","cm","A4");

$pdf->SetMargins(1,1,1);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(25.5,0.7,"LAPORAN KOMISI PEGAWAI",0,10,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(5.5,0.5,"Tanggal Cetak : ".date('d-m-Y h:i:s'),0,0,'L');
$pdf->Cell(5,0.5,"Dicetak Oleh : ".$_SESSION['namalengkap'],0,1,'L');
$pdf->Cell(5.5,0.5,"Periode : ".tgl_indo($tgl_awal)." - ".tgl_indo($tgl_akhir),0,0,'L');
$pdf->Line(1,2.7,28.5,2.7); //horisontal bawah
$pdf->ln(1);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(1, 0.7, 'NO', 1, 0, 'C');
$pdf->Cell(11, 0.7, 'Nama Pegawai', 1, 0, 'L');
$pdf->Cell(3, 0.7, 'Total', 1, 1, 'C');
$pdf->SetFont('Arial','',8);
$no=1;


$query=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM admin WHERE akses_level ='petugas'");


$ttl=0;
while($lihat=mysqli_fetch_array($query)){

    $getkomisi = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT SUM(ttl_komisi) as total FROM komisi_pegawai 
        WHERE id_admin ='$lihat[id_admin]'  AND tgl_komisi BETWEEN '$tgl_awal' AND '$tgl_akhir'");
    $get = mysqli_fetch_array($getkomisi);
    $ttl = $ttl + $get['total'];
    
	$pdf->Cell(1, 0.6, $no , 1, 0, 'C');
	$pdf->Cell(11, 0.6, $lihat['nama_lengkap'],1, 0, 'L');
	$pdf->Cell(3, 0.6, format_rupiah($get['total']), 1, 1,'R');
	$no++;
}



$pdf->SetFont('Arial','B',9);
$pdf->Cell(12, 0.7, 'TOTAL', 1, 0, 'C');
$pdf->Cell(3, 0.7, $ttl , 1, 1, 'R');


$pdf->Output("Laporan_komisi_pegawai.pdf","I");

?>

