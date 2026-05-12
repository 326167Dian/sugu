<?php
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

//ambil header
$ah = $db->query("SELECT * FROM setheader");
$rh = $ah->fetch(PDO::FETCH_ASSOC);

$dt = $db->prepare("SELECT * FROM cekdarah 
JOIN pelanggan ON (cekdarah.id_pelanggan = pelanggan.id_pelanggan) WHERE id_cekdarah = ?");
$dt->execute([$_GET['id']]);
$r1 = $dt->fetch(PDO::FETCH_ASSOC);


$tinggikertas = 16.5;



$pdf = new FPDF("P", "cm", array($tinggikertas, 7.5));
$pdf->SetMargins(0.5, -1, 0.5);
$pdf->AliasNbPages();
$pdf->AddPage();

//$pdf->Image('../../images/mmd.jpg',1,1.5,5,2);
//HEADER 1
$pdf->Line(0.8, 2.5, 6.5, 2.5); //horisontal bawah

$pdf->ln(1.3);
$pdf->SetFont('Arial','B', 11);
$pdf->Cell(6.5, 0.4, $rh['satu'], 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(6.5, 0.4, $rh['dua'], 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(6.5, 0.4, $rh['tiga'], 0, 1, 'C');
$pdf->Cell(6.5, 0.3, $rh['empat'], 0, 1, 'C');
$pdf->Cell(6.5, 0.3, $rh['lima'], 0, 1, 'C');
$pdf->Cell(6.5, 0.3, $rh['enam'], 0, 1, 'C');
$pdf->Cell(6.5, 0.5, $rh['tujuh'], 0, 1, 'C');

$pdf->ln(0.1);
$pdf->SetFont('Arial','B', 11);
$pdf->Cell(6.5, 0.6,'HASIL CEK DARAH', 0, 1, 'C');
$pdf->SetFont('Arial','', 9);
$pdf->Cell(6.5, 0.3,$r1['waktu'], 0, 1, 'C');
$pdf->SetFont('Arial','', 10);
$pdf->Cell(2, 0.4,'', 0, 0, 'L');
$pdf->Cell(3, 0.4,'', 0, 1, 'L');
$pdf->Cell(2, 0.4,'Pasien', 0, 0, 'L');
$pdf->Cell(3, 0.4,': '.$r1['nm_pelanggan'], 0, 1, 'L');
$pdf->Cell(2, 0.4,'Glukosa', 0, 0, 'L');
$pdf->Cell(3, 0.4,': '.$r1['gula'].' mg/dl', 0, 1, 'L');
$pdf->Cell(2, 0.4,'Asam Urat', 0, 0, 'L');
$pdf->Cell(3, 0.4,': '.$r1['asamurat'].' mg/dl', 0, 1, 'L');
$pdf->Cell(2, 0.4,'Kolesterol', 0, 0, 'L');
$pdf->Cell(3, 0.4,': '.$r1['kolesterol'].' mg/dl', 0, 1, 'L');
$pdf->Cell(2, 0.4,'Tensi', 0, 0, 'L');
$pdf->Cell(3, 0.4,': '.$r1['tensi'].' mmHg', 0, 1, 'L');

$pdf->ln(0.2);
$pdf->SetFont('Arial','', 10);
$pdf->Cell(6.5, 0.4,'Tabel Glukosa Darah', 0, 1, 'L');
$pdf->Cell(1.5, 0.4,'', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'Normal', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'Pre DM', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'DM', 0, 1, 'C');

$pdf->Cell(1.5, 0.4,'Puasa', 0, 0, 'L');
$pdf->Cell(1.5, 0.4,'70-100', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'100-124', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'>125', 0, 1, 'C');

$pdf->Cell(1.5, 0.4,'2PP', 0, 0, 'L');
$pdf->Cell(1.5, 0.4,'<140', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'140-200', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'>200', 0, 1, 'C');

$pdf->ln(0.2);
$pdf->SetFont('Arial','', 10);
$pdf->Cell(6.5, 0.4,'Tabel Asam Urat', 0, 1, 'L');
$pdf->Cell(1.5, 0.4,'Usia', 0, 0, 'L');
$pdf->Cell(1.5, 0.4,'10-18', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'18-40', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'>40', 0, 1, 'C');

$pdf->Cell(1.5, 0.4,'Pria', 0, 0, 'L');
$pdf->Cell(1.5, 0.4,'3.6-5.5', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'2-7.5', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'2-8.5', 0, 1, 'C');

$pdf->Cell(1.5, 0.4,'Wanita', 0, 0, 'L');
$pdf->Cell(1.5, 0.4,'3.6-5.5', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'2-6.5', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'2-8', 0, 1, 'C');

$pdf->ln(0.2);
$pdf->SetFont('Arial','', 10);
$pdf->Cell(6.5, 0.4,'Tabel Kolesterol Total', 0, 1, 'L');
$pdf->Cell(1.5, 0.4,'Kelamin', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'Normal', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'PreTinggi', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'Tinggi', 0, 1, 'C');
$pdf->Cell(1.5, 0.4,'P/W', 0, 0, 'L');
$pdf->Cell(1.5, 0.4,'<200', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'200-239', 0, 0, 'C');
$pdf->Cell(1.5, 0.4,'>240', 0, 1, 'C');

$pdf->ln(0.2);
$pdf->SetFont('Arial','', 10);
$pdf->Cell(6.5, 0.4,'Tabel Tekanan Darah', 0, 1, 'L');
$pdf->Cell(2.5, 0.4,'Kategori', 0, 0, 'L');
$pdf->Cell(2.0, 0.4,'Sistolik', 0, 0, 'C');
$pdf->Cell(2.0, 0.4,'Diastolik', 0, 1, 'C');

$pdf->Cell(2.5, 0.4,'Optimal', 0, 0, 'L');
$pdf->Cell(2.0, 0.4,'<120', 0, 0, 'C');
$pdf->Cell(2.0, 0.4,'<80', 0, 1, 'C');

$pdf->Cell(2.5, 0.4,'Normal', 0, 0, 'L');
$pdf->Cell(2.0, 0.4,'<130', 0, 0, 'C');
$pdf->Cell(2.0, 0.4,'<85', 0, 1, 'C');

$pdf->Cell(2.5, 0.4,'Pre Hipertensi', 0, 0, 'L');
$pdf->Cell(2.0, 0.4,'130-139', 0, 0, 'C');
$pdf->Cell(2.0, 0.4,'85-89', 0, 1, 'C');

$pdf->Cell(2.5, 0.4,'derajat 1', 0, 0, 'L');
$pdf->Cell(2.0, 0.4,'140-159', 0, 0, 'C');
$pdf->Cell(2.0, 0.4,'90-99', 0, 1, 'C');

$pdf->Cell(2.5, 0.4,'derajat 2', 0, 0, 'L');
$pdf->Cell(2.0, 0.4,'160-179', 0, 0, 'C');
$pdf->Cell(2.0, 0.4,'100-109', 0, 1, 'C');

$pdf->Cell(2.5, 0.4,'derajat 3', 0, 0, 'L');
$pdf->Cell(2.0, 0.4,'>180', 0, 0, 'C');
$pdf->Cell(2.0, 0.4,'>110', 0, 1, 'C');



$pdf->Output("hasil_cek_darah", "I");
