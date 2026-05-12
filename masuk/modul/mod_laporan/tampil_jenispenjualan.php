<?php
session_start();
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";


$tgl_awal = $_POST['tgl_awal'];
$tgl_akhir = $_POST['tgl_akhir'];
if ($_POST['tipe']<7){
    $tipe = $_POST['tipe'];
}
else {
	$tipe=("1,2,3,4,5,6");
}
$jualan = $db->prepare("select * from jenispenjualan where id_penjualan= $_POST[tipe] " );
$jualan->execute();
$tipejual = $jualan->fetch(PDO::FETCH_ASSOC);

$pdf = new FPDF("P","cm","A4");

$pdf->SetMargins(1,1,1);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(25.5,0.7,"LAPORAN TRANSAKSI BERDASARKAN JENIS PENJUALAN",0,10,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(5.5,0.5,"Tanggal Cetak : ".date('d-m-Y H:i:s'),0,0,'L');
$pdf->Cell(5,0.5,"Dicetak Oleh : ".$_SESSION['namalengkap'],0,1,'L');
$pdf->Cell(5.5,0.5,"Periode : ".tgl_indo($tgl_awal)." - ".tgl_indo($tgl_akhir),0,1,'L');
$pdf->SetFont('Arial','B',9);
$pdf->Cell(5.5, 0.5, "Jenis Penjualan : " . $tipejual['nm_penjualan'], 0, 1, 'L');


$pdf->ln(0.5);
$pdf->SetFont('Arial','',9);

$no=1;
$penjualan = $db->prepare("select 
 								*
 								from trkasir_detail join trkasir
								on(trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
								where trkasir_detail.tipe in($tipe) and tgl_trkasir between '$tgl_awal' and '$tgl_akhir' group by trkasir_detail.kd_trkasir ");
$penjualan->execute();
$no=1;
while ($jual = $penjualan->fetch(PDO::FETCH_ASSOC)) {
	$detailsub = $db->prepare("select
								sum(hrgttl_dtrkasir) as ttltx						
								from trkasir_detail where kd_trkasir='$jual[kd_trkasir]' order by nmbrg_dtrkasir ");
	$detailsub->execute();
	$detsub = $detailsub->fetch(PDO::FETCH_ASSOC);
	$hrgdsr = $detsub['ttltx'];
	$diskon = $hrgdsr - $jual['ttl_trkasir'];

	$pdf->Cell(3, 0.4, 'No', 0, 0, 'L');
	$pdf->Cell(0.5, 0.4, ': ', 0, 0, 'L');
	$pdf->Cell(5, 0.4, $no, 0, 1, 'L');

	$pdf->Cell(3, 0.4, 'Nama Pelanggan', 0, 0, 'L');
	$pdf->Cell(0.5, 0.4, ': ', 0, 0, 'L');
	$pdf->Cell(5, 0.4, $jual['nm_pelanggan'].'                            Telp : '.$jual['tlp_pelanggan'], 0, 1, 'L');

	$pdf->Cell(3, 0.4, 'Kode Transaksi', 0, 0, 'L');
	$pdf->Cell(0.5, 0.4, ': ', 0, 0, 'L');
	$pdf->Cell(5, 0.4, $jual['kd_trkasir'], 0, 1, 'L');

	$pdf->Cell(3, 0.4, 'Diskon', 0, 0, 'L');
	$pdf->Cell(0.5, 0.4, ': ', 0, 0, 'L');
	$pdf->Cell(5, 0.4, format_rupiah($diskon), 0, 1, 'L');

	$pdf->Cell(3, 0.4, 'Nilai Transaksi', 0, 0, 'L');
	$pdf->Cell(0.5, 0.4, ': ', 0, 0, 'L');
	$pdf->Cell(5, 0.4, format_rupiah($jual['ttl_trkasir']), 0, 1, 'L');



	$no++;

	$detail = $db->prepare("select
								hrgjual_dtrkasir,
								nmbrg_dtrkasir,
								qty_dtrkasir,
								sat_dtrkasir,
								disc,
								hrgttl_dtrkasir						
								from trkasir_detail where kd_trkasir='$jual[kd_trkasir]' order by nmbrg_dtrkasir ");
	$detail->execute();
	$no2 = 1;

	$pdf->Cell(1, 0.7, 'No', 1, 0, 'C');
	$pdf->Cell(9.5, 0.7, 'Nama Barang', 1, 0, 'C');
	$pdf->Cell(1, 0.7, 'Jml', 1, 0, 'C');
	$pdf->Cell(1.5, 0.7, 'Sat', 1, 0, 'C');
	$pdf->Cell(2, 0.7, 'Harga', 1, 0, 'C');
	$pdf->Cell(2, 0.7, 'Disc', 1, 0, 'C');
	$pdf->Cell(2, 0.7, 'Sub Total', 1, 1, 'C');
	$pdf->SetFont('Arial', '', 8);


	while($det=$detail->fetch(PDO::FETCH_ASSOC)){
		$hrgawl = $det['hrgjual_dtrkasir'] + $det['disc'];
		$pdf->Cell(1, 0.6,$no2, 1, 0, 'C');
		$pdf->Cell(9.5, 0.6,$det['nmbrg_dtrkasir'], 1, 0, 'L');
		$pdf->Cell(1, 0.6, $det['qty_dtrkasir'], 1, 0, 'C');
		$pdf->Cell(1.5, 0.6, $det['sat_dtrkasir'], 1, 0, 'C');
		$pdf->Cell(2, 0.6, format_rupiah($hrgawl), 1, 0, 'R');
		$pdf->Cell(2, 0.6, format_rupiah($det['disc']), 1, 0, 'R');
		$pdf->Cell(2, 0.6, format_rupiah($det['hrgttl_dtrkasir']), 1, 1, 'R');
		$no2++;

	}
	$detail2 = $db->prepare("select
								sum(hrgttl_dtrkasir) as ttltx						
								from trkasir_detail where kd_trkasir='$jual[kd_trkasir]' order by nmbrg_dtrkasir ");
	$detail2->execute();
	$det2 = $detail2->fetch(PDO::FETCH_ASSOC);
	$pdf->Cell(1, 0.6,'', 0, 0, 'C');
	$pdf->Cell(9.5, 0.6,'', 0, 0, 'L');
	$pdf->Cell(1, 0.6,'', 0, 0, 'C');
	$pdf->Cell(1.5, 0.6,'', 0, 0, 'C');
	$pdf->Cell(2, 0.6,'', 0, 0, 'R');
	$pdf->Cell(2, 0.6,'TOTAL', 1, 0, 'C');
	$pdf->Cell(2, 0.6, format_rupiah($det2['ttltx']), 1, 1, 'R');

	$pdf->Cell(2, 0.7, '', 0, 1, 'C');
}
$penjualan2 = $db->prepare("select 
 								SUM(hrgttl_dtrkasir) as totalsemua
 								from trkasir_detail join trkasir
								on(trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
								where trkasir_detail.tipe in($tipe) and tgl_trkasir between '$tgl_awal' and '$tgl_akhir' ");
$penjualan2->execute();
$pnj = $penjualan2->fetch(PDO::FETCH_ASSOC);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(4, 0.4, 'TOTAL PENJUALAN', 0, 0, 'L');
$pdf->Cell(0.5, 0.4, ': ', 0, 0, 'L');
$pdf->Cell(5, 0.4,'Rp. '.format_rupiah($pnj['totalsemua']), 0, 1, 'L');
$pdf->Output("Laporan_data_barang.pdf","I");

?>

