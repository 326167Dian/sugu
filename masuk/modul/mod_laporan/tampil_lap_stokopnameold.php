<?php
session_start();
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

$pdf = new FPDF("L", "cm", "A4");

$pdf->SetMargins(1, 1, 1);
$pdf->AliasNbPages();
$pdf->AddPage();

$tgl_awal = $_POST['tgl_awal'];
$tgl_akhir = $_POST['tgl_akhir'];

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25.5, 0.7, "LAPORAN REKAP STOK OPNAME", 0, 10, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(5.5, 0.5, "Tanggal Cetak : " . date('d-m-Y h:i:s'), 0, 0, 'L');
$pdf->Cell(5, 0.5, "Dicetak Oleh : " . $_SESSION['namalengkap'], 0, 1, 'L');
$pdf->Cell(5.5, 0.5, "Periode : " . tgl_indo($tgl_awal) . " - " . tgl_indo($tgl_akhir), 0, 0, 'L');
$pdf->Line(1, 2.7, 28.5, 2.7); //horisontal bawah

$pdf->ln(1.5);

//bagian header

$header = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM stok_opname 
JOIN admin ON admin.id_admin = stok_opname.id_admin
WHERE stok_opname.tgl_stokopname BETWEEN '$tgl_awal' AND '$tgl_akhir' GROUP BY stok_opname.tgl_stokopname");

$no1 = 1;
while ($r1 = mysqli_fetch_array($header)) {


	$tgl_stok = $r1['tgl_stokopname'];
	// $nm_supplier = $r1['nm_supplier'];
	// $tlp_supplier = $r1['tlp_supplier'];
	// $ttl_trbmasuk = $r1['ttl_trbmasuk'];
	// $ket_trbmasuk = $r1['ket_trbmasuk'];

	//KIRI 1
// 	$pdf->SetX(1);
// 	$pdf->SetFont('Arial', '', 9);
// 	$pdf->Cell(3, 0, 'Petugas Stok Opname : ', 0, 0, 'L');
// 	$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
// 	$pdf->SetFont('Arial', 'B', 9);
// 	$pdf->Cell(1, 0, $r1['nama_lengkap'], 0, 0, 'L');

	//KANAN 1
	// $pdf->SetX(10);
	// $pdf->SetFont('Arial', '', 9);
	// $pdf->Cell(3, 0, 'Supplier', 0, 0, 'L');
	// $pdf->Cell(0.3, 0, ':', 0, 0, 'L');
	// $pdf->SetFont('Arial', 'B', 8);
	// $pdf->Cell(1, 0, $nm_supplier, 0, 0, 'L');

	$pdf->ln(0.5);

	//KIRI 2
	$pdf->SetX(1);
	$pdf->SetFont('Arial', '', 9);
	$pdf->Cell(3, 0, 'Tanggal Stok Opname : ', 0, 0, 'L');
	$pdf->Cell(0.5, 0, ':', 0, 0, 'L');
	$pdf->SetFont('Arial', '', 9);
	$pdf->Cell(1, 0, tgl_indo($tgl_stok), 0, 0, 'L');

	//KANAN 2
	// $pdf->SetX(10);
	// $pdf->SetFont('Arial', '', 9);
	// $pdf->Cell(3, 0, 'Telepon', 0, 0, 'L');
	// $pdf->Cell(0.3, 0, ':', 0, 0, 'L');
	// $pdf->SetFont('Arial', '', 8);
	// $pdf->Cell(1, 0, $tlp_supplier, 0, 0, 'L');

	// $pdf->ln(0.5);

	//KIRI 3
	// $pdf->SetX(1);
	// $pdf->SetFont('Arial', '', 9);
	// $pdf->Cell(3, 0, 'Keterangan', 0, 0, 'L');
	// $pdf->Cell(0.5, 0, ':', 0, 0, 'L');
	// $pdf->SetFont('Arial', '', 8);
	// $pdf->Cell(1, 0, $ket_trbmasuk, 0, 0, 'L');

	$pdf->ln(0.5);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(1, 0.7, 'NO', 1, 0, 'C');
	$pdf->Cell(3, 0.7, 'Petugas', 1, 0, 'C');
// 	$pdf->Cell(1.5, 0.7, 'Rak', 1, 0, 'C');
	$pdf->Cell(3, 0.7, 'Kode Barang', 1, 0, 'L');
	$pdf->Cell(9.5, 0.7, 'Nama Barang', 1, 0, 'L');
	// $pdf->Cell(2, 0.7, 'Qty/Stok', 1, 0, 'R');
	$pdf->Cell(1.5, 0.7, 'Satuan', 1, 0, 'C');
	$pdf->Cell(1, 0.7, 'SS', 1, 0, 'C');
	$pdf->Cell(1, 0.7, 'SF', 1, 0, 'C');
	$pdf->Cell(1, 0.7, 'Hasil', 1, 0, 'C');
	$pdf->Cell(3.5, 0.7, 'Waktu', 1, 0, 'C');
	$pdf->Cell(1.5, 0.7, 'Harga', 1, 0, 'R');
	$pdf->Cell(1.5, 0.7, 'Total', 1, 1, 'R');
	$pdf->SetFont('Arial', '', 8);

	//detail barang
	$detailbrg = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM stok_opname 
	JOIN barang ON barang.id_barang = stok_opname.id_barang 
	JOIN admin ON admin.id_admin = stok_opname.id_admin WHERE stok_opname.tgl_stokopname ='$r1[tgl_stokopname]' ORDER BY stok_opname.tgl_current ASC");
	$no2 = 1;
	$total_minus = 0;
	$total_lebih = 0;
	while ($lihat = mysqli_fetch_array($detailbrg)) {

		if ($lihat['selisih'] < 0) {
			$total_minus += abs($lihat['ttl_hrgbrg']);
		}

		if ($lihat['selisih'] > 0) {
			$total_lebih += $lihat['ttl_hrgbrg'];
		}

		$pdf->Cell(1, 0.5, $no2, 1, 0, 'C');
		$pdf->Cell(3, 0.5, $lihat['nama_lengkap'], 1, 0, 'C');
// 		$pdf->Cell(1.5, 0.5, $lihat['jenisobat'], 1, 0, 'L');
		$pdf->Cell(3, 0.5, $lihat['kd_barang'], 1, 0, 'L');
		$pdf->Cell(9.5, 0.5, $lihat['nm_barang'], 1, 0, 'L');
		$pdf->Cell(1.5, 0.5, $lihat['sat_barang'], 1, 0, 'C');
		$pdf->Cell(1, 0.5, $lihat['stok_sistem'], 1, 0, 'C');
		$pdf->Cell(1, 0.5, $lihat['stok_fisik'], 1, 0, 'C');
		$pdf->Cell(1, 0.5, $lihat['selisih'], 1, 0, 'C');
		$pdf->Cell(3.5, 0.5, date('d M Y - H:i:s', strtotime($lihat['tgl_current'])), 1, 0, 'C');
		$pdf->Cell(1.5, 0.5, format_rupiah($lihat['hrgsat_barang']), 1, 0, 'R');
		$pdf->Cell(1.5, 0.5, format_rupiah($lihat['ttl_hrgbrg']), 1, 1, 'R');

		$no2++;
	}
	//grand total
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(23.5, 0.7, 'Total Barang Minus : ', 1, 0, 'R');
	$pdf->Cell(4, 0.7, format_rupiah($total_minus), 1, 1, 'R');
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(23.5, 0.7, 'Total Barang Lebih : ', 1, 0, 'R');
	$pdf->Cell(4, 0.7, format_rupiah($total_lebih), 1, 1, 'R');
	$pdf->ln(0.7);
}






$pdf->Output("Data-Barang-Masuk.pdf", "I");
