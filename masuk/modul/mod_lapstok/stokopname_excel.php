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

$tgl_awal   = $_GET['start'];
$tgl_akhir  = $_GET['finish'];
$jenisobat  = $_GET['jenisobat'];

$sheet->setCellValue('A1', 'STOK OPNAME BULANAN ');
$sheet->mergeCells('A1:C1');
$sheet->setCellValue('A2', 'Tanggal: ' . tgl_indo($tgl_awal) . ' s/d ' . tgl_indo($tgl_akhir));
$sheet->mergeCells('A2:C2');

$sheet->setCellValue('A4', 'No.');
$sheet->setCellValue('B4', 'Kode');
$sheet->getStyle('B4')->getAlignment()->setHorizontal('center');
$sheet->setCellValue('C4', 'Nama Barang');
$sheet->getStyle('C4')->getAlignment()->setHorizontal('center');
$sheet->setCellValue('D4', 'Satuan');
$sheet->getStyle('D4')->getAlignment()->setHorizontal('center');
$sheet->setCellValue('E4', 'Stok');
$sheet->getStyle('E4')->getAlignment()->setHorizontal('center');
$sheet->setCellValue('F4', 'Stok Fisik');
$sheet->getStyle('F4')->getAlignment()->setHorizontal('center');
$sheet->setCellValue('G4', 'Exp. Date');
$sheet->getStyle('G4')->getAlignment()->setHorizontal('center');
$sheet->setCellValue('H4', 'Harga Jual');
$sheet->getStyle('H4')->getAlignment()->setHorizontal('center');
$sheet->setCellValue('I4', 'Waktu');
$sheet->getStyle('I4')->getAlignment()->setHorizontal('center');
$sheet->setCellValue('J4', 'Acc Manager');
$sheet->getStyle('J4')->getAlignment()->setHorizontal('center');

$no = 1;
$query=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang WHERE jenisobat='$jenisobat' ORDER BY barang.nm_barang");

$row = 5;
$count = mysqli_num_rows($query);
$rows = $row + $count;
while ($lihat = mysqli_fetch_array($query)) {
    $kodebarang         = "'" . $lihat['kd_barang'];
    $namabrg            = $lihat['nm_barang'];
    $satbrg             = $lihat['sat_barang'];
    $stok               = $lihat['stok_barang'];
    $hrgjual_dtrkasir   = $lihat['hrgjual_barang'];
   
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');

    $sheet->setCellValue('B' . $row, $kodebarang);
    $sheet->getColumnDimension('B')->setAutoSize(true);

    $sheet->setCellValue('C' . $row, $namabrg);
    $sheet->getColumnDimension('C')->setAutoSize(true);

    $sheet->setCellValue('D' . $row, $satbrg);
    $sheet->getColumnDimension('D')->setAutoSize(true);

    $sheet->setCellValue('E' . $row, $stok);
    $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal('center');
    $sheet->getColumnDimension('E')->setAutoSize(true);

    $sheet->setCellValue('F' . $row, '');
    $sheet->getColumnDimension('F')->setAutoSize(true);

    $sheet->setCellValue('G' . $row, '');
    $sheet->getColumnDimension('G')->setAutoSize(true);

    $sheet->setCellValue('H' . $row, number_format($hrgjual_dtrkasir));
    $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal('right');
    $sheet->getColumnDimension('H')->setAutoSize(true);

    $sheet->setCellValue('I' . $row, date('H:i:s'));
    $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal('center');
    $sheet->getColumnDimension('I')->setWidth(15);

    $sheet->setCellValue('J' . $row, '');
    $sheet->getColumnDimension('J')->setAutoSize(true);
    $row++;
}

$writer = new Xlsx($spreadsheet);

// Create the table
$table = new Table('A4:J' . $rows, 'Table1');
// Optional: apply some styling to the table
$tableStyle = new TableStyle();
$tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM1);
$tableStyle->setShowRowStripes(true);
$table->setStyle($tableStyle);

// Add the table to the sheet
$sheet->addTable($table);

$writer->save('stok_opname_bulanan.xlsx');
echo "<script>window.location = 'stok_opname_bulanan.xlsx';
setInterval(function(){
    window.close();
},1000)
</script>";
