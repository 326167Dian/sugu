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
$pdf->Cell(25.5,0.7,"LAPORAN DATA BARANG MACET ".date('d-m-Y H:i:s')." ",0,10,'L');
$pdf->ln(0.5);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(1, 0.7, 'NO', 1, 0, 'C');
$pdf->Cell(3, 0.7, 'Kode Barang', 1, 0, 'L');
$pdf->Cell(11, 0.7, 'Nama Barang', 1, 0, 'L');
$pdf->Cell(1.5, 0.7, 'satuan', 1, 0, 'R');
$pdf->Cell(2, 0.7, 'Stok', 1, 0, 'C');
$pdf->Cell(3, 0.7, 'stok fisik', 1, 0, 'C');
$pdf->Cell(3, 0.7, 'waktu', 1, 0, 'C');
$pdf->Cell(3, 0.7, 'acc', 1, 1, 'C');
$pdf->SetFont('Arial','',8);
$no=1;


$tampil_barang = $db->prepare("SELECT * FROM barang ORDER BY barang.stok_barang DESC");
$tampil_barang->execute();
$no=1;
while ($r = $tampil_barang->fetch(PDO::FETCH_ASSOC)){
    $t30 = $r['kd_barang'];

    $tgl_awal = date('Y-m-d');
    $tgl_akhir = date('Y-m-d', strtotime('-30 days', strtotime( $tgl_awal)));

    $pass = $db->prepare("SELECT * FROM trkasir JOIN trkasir_detail
                         ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
                         WHERE kd_barang = '$t30' AND (tgl_trkasir BETWEEN '$_GET[start]' and '$_GET[finish]')");
    $pass->execute();
    // $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
    //                                     ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
    //                                     WHERE kd_barang = '$t30' AND (tgl_trkasir BETWEEN '$tgl_akhir' and '$tgl_awal')");
    $pass1 = $pass->rowCount();

    if($pass1<1){


        $pdf->Cell(1, 0.6, $no , 1, 0, 'C');
        $pdf->Cell(3, 0.6, $r['kd_barang'],1, 0, 'L');
        $pdf->Cell(11, 0.6, $r['nm_barang'], 1, 0,'L');
        $pdf->Cell(1.5, 0.6, $r['sat_barang'], 1, 0,'C');
        $pdf->Cell(2, 0.6, $r['stok_barang'], 1, 0,'C');
        $pdf->Cell(3, 0.6, '', 1, 0,'C');
        $pdf->Cell(3, 0.6, '', 1, 0,'C');
        $pdf->Cell(3, 0.6, '', 1, 1,'C');

        $no++;}
}
$pdf->SetFont('Arial','',10);
$pdf->Cell(2,0.7,"Tanggal Cetak : ".date('d-m-Y h:i:s')." || Dicetak Oleh : ".$_SESSION['namalengkap'],0,0,'L');


$pdf->Output("Laporan_data_barang MACET.pdf","I");
