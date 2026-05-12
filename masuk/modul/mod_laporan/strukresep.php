<?php
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

//ambil header
$ah = $db->prepare("SELECT * FROM setheader");
$ah->execute();
$rh = $ah->fetch(PDO::FETCH_ASSOC);

$dt = $db->prepare("SELECT * FROM trkasir
                    JOIN carabayar ON trkasir.id_carabayar = carabayar.id_carabayar
                    WHERE trkasir.kd_trkasir='$_GET[kd_trkasir]'");
$dt->execute();
$r1 = $dt->fetch(PDO::FETCH_ASSOC);

$jumlah = $db->prepare("SELECT * FROM trkasir_detail WHERE kd_trkasir='$_GET[kd_trkasir]'");
$jumlah->execute();
$jumlahdetail = $jumlah->rowCount();

$ukuran1 = 12.7; //setingan kertas
$ukuran2 = 5.4; //garis akhir tabel

$tambahukuran = $jumlahdetail * 0.4;
$tinggikertas = $ukuran1 + $tambahukuran;
$posisigaris = $ukuran2 + $tambahukuran;




//$pdf = new FPDF("P","cm","A4");
$pdf = new FPDF("P", "cm", array($tinggikertas, 7.5));
$pdf->SetMargins(0.3,0.1, 0);
$pdf->AliasNbPages();
$pdf->AddPage();

//$pdf->Image('../../images/mmd.jpg',1,1.5,5,2);
//HEADER 1
$pdf->Line(0, 3.5, 8, 3.5); //horisontal bawah

$pdf->Line(0, 5.7, 8, 5.7); //judul tabel atas



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

//KIRI 1
$pdf->ln(0.3);
$pdf->SetX(0.6);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2, 0, 'No Nota', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(1, 0, $r1['kd_trkasir'], 0, 0, 'L');

//KIRI 2
$pdf->ln(0.4);
$pdf->SetX(0.6);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2, 0, 'Tanggal', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(1, 0, tgl_indo($r1['tgl_trkasir']), 0, 0, 'L');


//KIRI 3
$pdf->ln(0.4);
$pdf->SetX(0.6);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2, 0, 'Pelanggan', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(1, 0, $r1['nm_pelanggan'], 0, 0, 'L');

//KIRI 4
$pdf->ln(0.4);
$pdf->SetX(0.6);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2, 0, 'No Telp/HP', 0, 0, 'L');
$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(1, 0, $r1['tlp_pelanggan'], 0, 0, 'L');

$pdf->ln(0.3);
$pdf->SetX(0.6);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(2, 0.5, 'Item', 0, 0, 'L');
$pdf->Cell(0.8, 0.5, 'Qty', 0, 0, 'C');
$pdf->Cell(2.2, 0.5, 'Harga', 0, 0, 'R');
$pdf->Cell(2.0, 0.5, 'Jumlah', 0, 1, 'R');

$pdf->SetX(0.6);
$pdf->SetFont('Arial', '', 9);

$no = 1;
$query = $db->prepare("SELECT * FROM trkasir WHERE kd_trkasir='$_GET[kd_trkasir]'");
$query->execute();
while ($r2 = $query->fetch(PDO::FETCH_ASSOC)) {

	$pdf->SetX(0.6);

	$pdf->Cell(3, 0.4, "Resep / Racikan Nomor " . $r2['kd_trkasir'], 0, 1, 'L');
	$pdf->Cell(1.3, 0.4, '1', 0, 0, 'R');
	$pdf->Cell(2.6, 0.4, 'Unit', 0, 0, 'C');
	$pdf->Cell(1.5, 0.4, format_rupiah($r2['ttl_trkasir']), 0, 0, 'R');
	$pdf->Cell(1.7, 0.4, format_rupiah($r2['ttl_trkasir']), 0, 1, 'R');

	$no++;
}
$pdf->ln(0.4);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetX(0.6);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(2, 0.4, 'Metode bayar : ', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(3.3, 0.4, 'Sub Total : ', 0, 0, 'R');
$pdf->Cell(1.6, 0.4, format_rupiah($r1['ttl_trkasir']), 0, 1, 'R');
$pdf->SetX(0.6);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(2, 0.4, $r1['nm_carabayar'], 0, 0, 'L');
$pdf->SetX(0.6);
$pdf->Cell(5.3, 0.4, 'Bayar Cash : ', 0, 0, 'R');
$pdf->Cell(1.6, 0.4, format_rupiah($r1['dp_bayar']), 0, 1, 'R');
$pdf->SetX(0.6);
$pdf->Cell(5.3, 0.4, 'Kembalian : ', 0, 0, 'R');
$pdf->Cell(1.6, 0.4, format_rupiah($r1['sisa_bayar']), 0, 1, 'R');


$pdf->ln(0.1);
$pdf->SetX(0.6);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 0.3, 'Keterangan :', 0, 1, 'L');
$pdf->SetX(0.6);
$pdf->Cell(0, 0.4, $r1['ket_trkasir'], 0, 0, 'L');

$pdf->ln(0.6);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 0.3, $rh['delapan'], 0, 1, 'C');
$pdf->Cell(0, 0.3, $rh['sembilan'], 0, 1, 'C');
$pdf->Cell(0, 0.3, $rh['sepuluh'], 0, 1, 'C');
$pdf->Cell(0, 0.3, $rh['sebelas'], 0, 1, 'C');

$pdf->Output("struk_wallpaper", "I");
