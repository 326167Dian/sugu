<?php
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

//ambil header
$stmt = $db->prepare("SELECT * FROM setheader");
$stmt->execute();
$rh = $stmt->fetch(PDO::FETCH_ASSOC);

$id = $_GET['idshift'];

$stmt = $db->prepare("SELECT * FROM waktukerja WHERE id_shift=?");
$stmt->execute([$id]);
$rshift = $stmt->fetch(PDO::FETCH_ASSOC);

$shift = $rshift['shift'];
$tgl_trkasir = $rshift['tanggal'];

$stmt = $db->prepare("SELECT SUM(ttl_trkasir) as ttl_penjualan FROM trkasir WHERE shift=? AND tgl_trkasir=?");
$stmt->execute([$shift, $tgl_trkasir]);
$r1 = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT * FROM trkasir WHERE shift=? AND tgl_trkasir=?");
$stmt->execute([$shift, $tgl_trkasir]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$rnum = count($results);
$rrnum = $results[0] ?? [];

$stmt = $db->prepare("SELECT SUM(ttl_trkasir) as ttl_tunai FROM trkasir WHERE shift=? AND tgl_trkasir=? AND id_carabayar=?");
$stmt->execute([$shift, $tgl_trkasir, 1]);
$r2 = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT SUM(ttl_trkasir) as ttl_transfer FROM trkasir WHERE shift=? AND tgl_trkasir=? AND id_carabayar=?");
$stmt->execute([$shift, $tgl_trkasir, 2]);
$r3 = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT SUM(ttl_trkasir) as ttl_tempo FROM trkasir WHERE shift=? AND tgl_trkasir=? AND id_carabayar=?");
$stmt->execute([$shift, $tgl_trkasir, 3]);
$r4 = $stmt->fetch(PDO::FETCH_ASSOC);

$tgl_awal = date('Y-m-d');
$stmt = $db->prepare("SELECT * FROM trkasir WHERE tgl_trkasir=? ORDER BY id_trkasir DESC");
$stmt->execute([$tgl_awal]);
$results_detail = $stmt->fetchAll(PDO::FETCH_ASSOC);
$countdetail = count($results_detail);

$ukuran1 = 14.7; //setingan kertas
$ukuran2 = 5.4; //garis akhir tabel

$tambahukuran = $countdetail * 0.4;
$tinggikertas = $ukuran1 + $tambahukuran;
$posisigaris = $ukuran2 + $tambahukuran;




//$pdf = new FPDF("P","cm","A4");
$pdf = new FPDF("P", "cm", array($tinggikertas, 7.5));
$pdf->SetMargins(-0.3, -0.8, 0);
$pdf->AliasNbPages();
$pdf->AddPage();

//$pdf->Image('../../images/mmd.jpg',1,1.5,5,2);
//HEADER 1
$pdf->Line(0, 2.7, 8, 2.7); //horisontal bawah

$pdf->Line(0, 6.5, 8, 6.5); //judul tabel atas



$pdf->ln(1.3);
$pdf->SetFont('Arial', '', 9);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 0.4, $rh['satu'], 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 0.4, $rh['dua'], 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 0.4, $rh['tiga'], 0, 1, 'C');
$pdf->Cell(0, 0.3, $rh['empat'], 0, 1, 'C');
$pdf->Cell(0, 0.3, $rh['lima'], 0, 1, 'C');
$pdf->Cell(0, 0.3, $rh['enam'], 0, 1, 'C');
$pdf->Cell(0, 0.5, '', 0, 1, 'C');

$pdf->ln(0.1);
$pdf->SetX(0.6);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(3, 0, 'Tanggal', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(1, 0, tgl_indo($tgl_trkasir), 0, 0, 'L');

//KIRI 1
$pdf->ln(0.4);
$pdf->SetX(0.6);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(3, 0, 'Total Penjualan', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(1, 0, format_rupiah($r1['ttl_penjualan']), 0, 0, 'L');

$tamtot_stmt = $db->prepare("SELECT * FROM carabayar");
$tamtot_stmt->execute();
$tamtot = $tamtot_stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($tamtot as $tt){
    $stmt2 = $db->prepare("SELECT id_trkasir, kd_trkasir, SUM(ttl_trkasir) as ttlskrg1 FROM trkasir WHERE tgl_trkasir=? AND id_carabayar=? AND shift=?");
    $stmt2->execute([$rshift['tanggal'], $tt['id_carabayar'], $rshift['shift']]);
    $tamtcb = $stmt2->fetch(PDO::FETCH_ASSOC);
    $dtamtcb = format_rupiah($tamtcb['ttlskrg1']);
    $pdf->ln(0.4);
    $pdf->SetX(0.6);
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(3, 0, $tt['nm_carabayar'], 0, 0, 'L');
    $pdf->Cell(0.5, 0, ':', 0, 0, 'L');
    $pdf->Cell(1.5, 0,$dtamtcb , 0, 1, 'R');

}

//KIRI 5
$pdf->ln(0.4);
$pdf->SetX(0.6);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(3, 0, 'Jumlah Transaksi', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(1, 0, $rnum, 0, 0, 'L');

//KIRI 6
$pdf->ln(0.4);
$pdf->SetX(0.6);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(3, 0, 'Petugas Buka', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(1, 0, ($rnum == 0) ? "" : $rshift['petugasbuka'], 0, 0, 'L');

//KIRI 7
$pdf->ln(0.4);
$pdf->SetX(0.6);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(3, 0, 'Petugas Tutup', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(1, 0, ($rnum == 0) ? "" : $rshift['petugastutup'], 0, 0, 'L');

$pdf->ln(0.6);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 0.3, $rh['delapan'], 0, 1, 'C');
$pdf->Cell(0, 0.3, $rh['sembilan'], 0, 1, 'C');
$pdf->Cell(0, 0.3, $rh['sepuluh'], 0, 1, 'C');
// $pdf->Cell(0,0.3,"Kasir : ".$r1['petugas'],0,1,'C');

$pdf->Output("struk_wallpaper", "I");
