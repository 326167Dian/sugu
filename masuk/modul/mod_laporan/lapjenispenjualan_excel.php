<?php
include '../../../configurasi/koneksi.php';
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$tgl_awal = $_GET['start'];
$tgl_akhir = $_GET['finish'];
$tipe = $_GET['tipe'];

if ($tipe<7){
    $tipex = $tipe;
}
else {
    $tipex=("1,2,3,4,5,6");
}

$tipeting = $db->query("select * from jenispenjualan where id_penjualan=$tipe ");
$tipx = $tipeting->fetch(PDO::FETCH_ASSOC);
$tip = $tipx['nm_penjualan'];

$sheet->setCellValue('A1', 'LAPORAN JENIS PENJUALAN : ' . $tip);
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A2', 'Tanggal: ' . tgl_indo($tgl_awal) . ' s/d ' . tgl_indo($tgl_akhir));
$sheet->mergeCells('A2:C2');

$sheet->setCellValue('A4', 'No.');
$sheet->setCellValue('B4', 'Tgl');
$sheet->getStyle('B4')->getAlignment()->setHorizontal('center');

$sheet->setCellValue('C4', 'Kode Transaksi');
$sheet->getStyle('C4')->getAlignment()->setHorizontal('center');

$sheet->setCellValue('D4', 'Petugas');
$sheet->getStyle('D4')->getAlignment()->setHorizontal('center');

$sheet->setCellValue('E4', 'Pelanggan');
$sheet->getStyle('E4')->getAlignment()->setHorizontal('center');

$sheet->setCellValue('F4', 'No Pesanan');
$sheet->getStyle('F4')->getAlignment()->setHorizontal('center');

$sheet->setCellValue('G4', 'Nilai Pesanan');
$sheet->getStyle('G4')->getAlignment()->setHorizontal('center');

$sheet->setCellValue('H4', 'Jenis Transaksi');
$sheet->getStyle('H4')->getAlignment()->setHorizontal('center');


$no = 1;
$query = $db->prepare("SELECT                 
                trkasir_detail.kd_trkasir,
                trkasir_detail.tipe,
                trkasir.kd_trkasir,
                trkasir.tgl_trkasir,
                trkasir.petugas,
                trkasir.nm_pelanggan,
                trkasir.kodetx,
                trkasir.ttl_trkasir
                FROM trkasir_detail 
                JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
                WHERE trkasir.tgl_trkasir BETWEEN :tgl_awal AND :tgl_akhir AND tipe in($tipex)
                GROUP BY trkasir_detail.kd_trkasir");
$query->execute([':tgl_awal' => $tgl_awal, ':tgl_akhir' => $tgl_akhir]);


$row = 5;
$count = $query->rowCount();
$rows = $row + $count;
while ($lihat = $query->fetch(PDO::FETCH_ASSOC)) {

$tipx = $db->query("select * from jenispenjualan where id_penjualan='$lihat[tipe]' ");
$tipy = $tipx->fetch(PDO::FETCH_ASSOC);

    $sheet->setCellValue('A' . $row, $no++);
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');

    $sheet->setCellValue('B' . $row, $lihat['tgl_trkasir']);
    $sheet->getColumnDimension('B')->setAutoSize(true);

    $sheet->setCellValue('C' . $row, $lihat['kd_trkasir']);
    $sheet->getColumnDimension('C')->setAutoSize(true);

    $sheet->setCellValue('D' . $row, $lihat['petugas']);
    $sheet->getColumnDimension('D')->setAutoSize(true);

    $sheet->setCellValue('E' . $row, $lihat['nm_pelanggan']);
    $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal('center');
    $sheet->getColumnDimension('E')->setAutoSize(true);

    $sheet->setCellValue('F' . $row, $lihat['kodetx']);
    $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal('center');
    $sheet->getColumnDimension('F')->setAutoSize(true);

    $sheet->setCellValue('G' . $row,$lihat['ttl_trkasir']);
    $sheet->getColumnDimension('G')->setAutoSize(true);

    $sheet->setCellValue('H' . $row,$tipy['nm_penjualan']);
    $sheet->getColumnDimension('H')->setAutoSize(true);


    $row++;
}
$toti = $db->query("select SUM(hrgttl_dtrkasir) as totalsemua
 								from trkasir_detail join trkasir
								on(trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
								where trkasir_detail.tipe in($tipex) and tgl_trkasir between '$tgl_awal' and '$tgl_akhir' ");
$totu = $toti->fetch(PDO::FETCH_ASSOC);
$total = $totu['totalsemua'];

$sheet->setCellValue('A' . $row, 'Total Penjualan : ' . $total);
$sheet->getStyle('F' . $row)->getAlignment()->setHorizontal('center');
$sheet->getColumnDimension('F')->setAutoSize(true);

$writer = new Xlsx($spreadsheet);

// Create the table
$table = new Table('A4:L' . $rows, 'Table1');
// Optional: apply some styling to the table
$tableStyle = new TableStyle();
$tableStyle->setTheme(TableStyle::TABLE_STYLE_LIGHT10);
$tableStyle->setShowRowStripes(true);
$table->setStyle($tableStyle);

// Add the table to the sheet
$sheet->addTable($table);

$writer->save('laporanjenispenjualan_'.$tgl_awal.'_'.$tgl_akhir.'_'.$tipex.'.xlsx');
echo "<script>window.location = 'laporanjenispenjualan_".$tgl_awal."_".$tgl_akhir."_".$tipex.".xlsx';
setInterval(function(){
    window.close();
},3000)
</script>";
