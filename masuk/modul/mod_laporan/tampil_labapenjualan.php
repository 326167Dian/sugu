<?php
session_start();
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";


$tgl_awal = $_POST['tgl_awal'];
$tgl_akhir = $_POST['tgl_akhir'];

$allowedShifts = array(1, 2, 3);
$shiftValue = isset($_POST['shift']) ? $_POST['shift'] : '';

if (is_array($shiftValue)) {
	$selectedShifts = array();
	foreach ($shiftValue as $sv) {
		$shiftInt = (int)$sv;
		if (in_array($shiftInt, $allowedShifts, true)) {
			$selectedShifts[] = $shiftInt;
		}
	}
	$selectedShifts = array_values(array_unique($selectedShifts));
} else {
	$shiftInt = (int)$shiftValue;
	if (in_array($shiftInt, $allowedShifts, true)) {
		$selectedShifts = array($shiftInt);
	} else {
		$selectedShifts = $allowedShifts;
	}
}

$shiftPlaceholders = implode(',', array_fill(0, count($selectedShifts), '?'));

$penjualanSql = "select t.kd_trkasir,
							 t.nm_pelanggan,
							 t.ttl_trkasir,
							 t.id_carabayar,
							 coalesce(c.nm_carabayar, '') as nm_carabayar
					 from trkasir t
					 left join carabayar c on c.id_carabayar = t.id_carabayar
					 where t.tgl_trkasir between ? and ?
					 and t.shift in ($shiftPlaceholders)
					 order by t.id_carabayar, t.kd_trkasir";

$penjualan = $db->prepare($penjualanSql);
$penjualanParams = array($tgl_awal, $tgl_akhir);
foreach ($selectedShifts as $shiftItem) {
	$penjualanParams[] = $shiftItem;
}
$penjualan->execute($penjualanParams);
$penjualanRows = $penjualan->fetchAll(PDO::FETCH_ASSOC);

$kdTrxList = array();
foreach ($penjualanRows as $rowTrx) {
	$kdTrxList[] = $rowTrx['kd_trkasir'];
}

$detailByKd = array();
if (!empty($kdTrxList)) {
	$kdPlaceholders = implode(',', array_fill(0, count($kdTrxList), '?'));
	$detailSql = "select d.kd_trkasir,
							 d.id_barang,
							 d.nmbrg_dtrkasir,
							 d.qty_dtrkasir,
							 d.sat_dtrkasir,
							 d.hrgjual_dtrkasir,
							 coalesce(d.disc, 0) as disc,
							 d.profit,
							 d.hrgttl_dtrkasir,
							 coalesce(b.hrgsat_barang, 0) as hrgsat_barang
					 from trkasir_detail d
					 left join barang b on b.id_barang = d.id_barang
					 where d.kd_trkasir in ($kdPlaceholders)
					 order by d.kd_trkasir, d.nmbrg_dtrkasir";

	$detailStmt = $db->prepare($detailSql);
	$detailStmt->execute($kdTrxList);
	while ($detailRow = $detailStmt->fetch(PDO::FETCH_ASSOC)) {
		$detailByKd[$detailRow['kd_trkasir']][] = $detailRow;
	}
}

$pdf = new FPDF("P","cm","A4");

$pdf->SetMargins(1,1,1);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(25.5,0.7,"LAPORAN LABA PENJUALAN",0,10,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(5.5,0.5,"Tanggal Cetak : ".date('d-m-Y H:i:s'),0,0,'L');
$pdf->Cell(5,0.5,"Dicetak Oleh : ".$_SESSION['namalengkap'],0,1,'L');
$pdf->Cell(5.5,0.5,"Periode : ".tgl_indo($tgl_awal)." - ".tgl_indo($tgl_akhir),0,0,'L');


$pdf->ln(0.5);
$pdf->SetFont('Arial','',9);

$no=1;

$totalNilaiTransaksi = 0;
$totalLaba = 0;

foreach ($penjualanRows as $jual) {
	//hitung angsuran

	$masuk=$jual['ttl_trkasir'];
	$totalNilaiTransaksi += (float)$masuk;

	$pdf->Cell(3, 0.4, 'No', 0, 0, 'L');
	$pdf->Cell(0.5, 0.4, ': ', 0, 0, 'L');
	$pdf->Cell(5, 0.4, $no, 0, 1, 'L');

	$pdf->Cell(3, 0.4, 'Nama Pelanggan', 0, 0, 'L');
	$pdf->Cell(0.5, 0.4, ': ', 0, 0, 'L');
	$pdf->Cell(5, 0.4, $jual['nm_pelanggan'], 0, 1, 'L');

	$pdf->Cell(3, 0.4, 'Kode Transaksi', 0,0 , 'L');
	$pdf->Cell(0.5, 0.4, ': ', 0, 0, 'L');
	$pdf->Cell(5, 0.4, $jual['kd_trkasir'], 0, 1, 'L');

	$pdf->Cell(3, 0.4, 'Metode Bayar', 0,0 , 'L');
	$pdf->Cell(0.5, 0.4, ': ', 0, 0, 'L');
	$pdf->Cell(5, 0.4, $jual['nm_carabayar'], 0, 1, 'L');

	$pdf->Cell(3, 0.4, 'Total Transaksi', 0,0 , 'L');
	$pdf->Cell(0.5, 0.4, ': ', 0, 0, 'L');
	$pdf->Cell(5, 0.4, format_rupiah($masuk), 0, 1, 'L');

	$trxDetails = isset($detailByKd[$jual['kd_trkasir']]) ? $detailByKd[$jual['kd_trkasir']] : array();
	$no2=1;

	$pdf->Cell(1, 0.7, 'No', 1, 0, 'C');
	$pdf->Cell(9.5, 0.7, 'Nama Barang', 1, 0, 'C');
	$pdf->Cell(1, 0.7, 'Jml', 1, 0, 'C');
	$pdf->Cell(1.5, 0.7, 'Sat', 1, 0, 'C');
	$pdf->Cell(2, 0.7, 'Harga', 1, 0, 'C');
	$pdf->Cell(2, 0.7, 'Modal', 1, 0, 'C');
	$pdf->Cell(2, 0.7, 'Sub Total', 1, 1, 'C');
	$pdf->SetFont('Arial','',8);



	$sumProfit = 0;
	$sumHrgTtl = 0;
	foreach($trxDetails as $det){
		$hrgawl = $det['hrgjual_dtrkasir'] + $det['disc'];
		$modal = $det['hrgsat_barang'];


		$pdf->Cell(1, 0.6,$no2, 1, 0, 'C');
		$pdf->Cell(9.5, 0.6,$det['nmbrg_dtrkasir'], 1, 0, 'L');
		$pdf->Cell(1, 0.6, $det['qty_dtrkasir'], 1, 0, 'C');
		$pdf->Cell(1.5, 0.6, $det['sat_dtrkasir'], 1, 0, 'C');
		$pdf->Cell(2, 0.6, format_rupiah($hrgawl), 1, 0, 'R');
		$pdf->Cell(2, 0.6, format_rupiah($modal), 1, 0, 'R');
		$pdf->Cell(2, 0.6, format_rupiah($det['profit']), 1, 1, 'R');
		$sumProfit += (float)$det['profit'];
		$sumHrgTtl += (float)$det['hrgttl_dtrkasir'];
		$no2++;

	}

	$sttl = format_rupiah($sumProfit);
	$discfaktur =  ($sumHrgTtl - $masuk);
	$ttllaba = $sumProfit - $discfaktur;
	$totalLaba += $ttllaba;

	$pdf->Cell(17, 0.6,'Sub Total Profit', 1, 0, 'R');
	$pdf->Cell(2, 0.6,$sttl, 1, 1, 'R');
	$pdf->Cell(17, 0.6,'Diskon Transaksi', 1, 0, 'R');
	$pdf->Cell(2, 0.6,format_rupiah($discfaktur), 1, 1, 'R');
	$pdf->Cell(17, 0.6,'Subtotal Laba', 1, 0, 'R');
	$pdf->Cell(2, 0.6,format_rupiah($ttllaba), 1, 1, 'R');
	$pdf->Cell(2, 0.6,'', 0, 1, 'R');

	$no++;
}

$pdf->SetFont('Arial','B',14);

$pdf->Cell(6, 0.7, '', 0, 0, 'L');
$pdf->Cell(0.5, 0.7, '', 0, 0, 'L');
$pdf->Cell(5, 0.7, '', 0, 1, 'R');

$pdf->Cell(6, 0.7, 'Total Nilai transaksi', 0, 0, 'L');
$pdf->Cell(0.5, 0.7, ': Rp. ', 0, 0, 'L');
$pdf->Cell(5, 0.7, format_rupiah($totalNilaiTransaksi), 0, 1, 'R');

$pdf->SetFont('Arial','B',14);
$pdf->Cell(6, 0.7, 'Total Laba', 0, 0, 'L');
$pdf->Cell(0.5, 0.7, ': Rp. ', 0, 0, 'L');
$pdf->Cell(5, 0.7, format_rupiah($totalLaba), 0, 1, 'R');

// $pdf->Cell(6, 0.7, 'Pembayaran Tunai', 0,0 , 'L');
// $pdf->Cell(0.5, 0.7, ': Rp. ', 0, 0, 'L');
// $pdf->Cell(5, 0.7, format_rupiah($subakh3['sisa3']), 0, 1, 'R');

// $pdf->Cell(6, 0.7, 'Pembayaran Transfer', 0,0 , 'L');
// $pdf->Cell(0.5, 0.7, ': Rp. ', 0, 0, 'L');
// $pdf->Cell(5, 0.7, format_rupiah($subakh1['sisa']), 0, 1, 'R');

// $pdf->Cell(6, 0.7, 'Pembayaran Tempo', 0,0 , 'L');
// $pdf->Cell(0.5, 0.7, ': Rp.', 0, 0, 'L');
// $pdf->Cell(5, 0.7, format_rupiah($subakh2['sisa1']), 0, 1, 'R');
$pdf->Output("Laporan_data_barang.pdf","I");


?>

