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
// $sheet = $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);

$tgl_awal = $_GET['start'];
$tgl_akhir = $_GET['finish'];
$shift = $_GET['shift'];
$shift1 = ($shift == '1') ? 'PAGI' : 'SORE';

$sheet->setCellValue('A1', 'STOK OPNAME SHIFT ' . $shift1);
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

$sheet->setCellValue('F4', 'Terjual');
$sheet->getStyle('F4')->getAlignment()->setHorizontal('center');

$sheet->setCellValue('G4', 'Stok Fisik');
$sheet->getStyle('G4')->getAlignment()->setHorizontal('center');

$sheet->setCellValue('H4', 'Exp. Date');
$sheet->getStyle('H4')->getAlignment()->setHorizontal('center');

$sheet->setCellValue('I4', 'Harga Beli');
$sheet->getStyle('I4')->getAlignment()->setHorizontal('center');

$sheet->setCellValue('J4', 'Harga Jual');
$sheet->getStyle('J4')->getAlignment()->setHorizontal('center');

$sheet->setCellValue('K4', 'Waktu');
$sheet->getStyle('K4')->getAlignment()->setHorizontal('center');

$sheet->setCellValue('L4', 'Acc Manager');
$sheet->getStyle('L4')->getAlignment()->setHorizontal('center');

$no = 1;
$query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT 
                trkasir_detail.kd_barang,
                trkasir_detail.id_dtrkasir,
                trkasir_detail.kd_trkasir,
                trkasir.kd_trkasir,
                trkasir.tgl_trkasir
                FROM trkasir_detail 
                JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
                WHERE trkasir.tgl_trkasir BETWEEN '$tgl_awal' AND '$tgl_akhir' AND shift = '$shift'
                GROUP BY trkasir_detail.kd_barang");

$row = 5;
$count = mysqli_num_rows($query);
$rows = $row + $count;
while ($lihat = mysqli_fetch_array($query)) {

    $query2 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT 
                    trkasir_detail.kd_barang,
                    trkasir_detail.id_dtrkasir,
                    trkasir_detail.kd_trkasir,
                    trkasir_detail.kd_barang,
                    barang.stok_barang,
                    barang.hrgsat_barang,
                    SUM(trkasir_detail.qty_dtrkasir) as ttlqty,
                    SUM(trkasir_detail.hrgttl_dtrkasir) as ttlhrg,
                    trkasir.kd_trkasir,
                    trkasir.tgl_trkasir
                    FROM trkasir_detail 
                    JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
                    join barang on barang.kd_barang = trkasir_detail.kd_barang
                    WHERE trkasir_detail.kd_barang='$lihat[kd_barang]'
                    AND trkasir.tgl_trkasir BETWEEN '$tgl_awal' AND '$tgl_akhir' AND shift = '$shift'
                    ORDER BY trkasir_detail.id_dtrkasir ASC");

    $r2 = mysqli_fetch_array($query2);
    $ttlqty = $r2['ttlqty'];
    $ttlhrg = $r2['ttlhrg'];
    $stok = $r2['stok_barang'];

    $query3 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir_detail 
                    WHERE id_dtrkasir='$lihat[id_dtrkasir]'");
    $r3 = mysqli_fetch_array($query3);
    $kd_barang = "'" . $r3['kd_barang'];
    $nmbrg_dtrkasir = $r3['nmbrg_dtrkasir'];
    $sat_dtrkasir = $r3['sat_dtrkasir'];
    $hrgjual_dtrkasir = $r3['hrgjual_dtrkasir'];
    $hrgsat_barang = $r2['hrgsat_barang'];

    $sheet->setCellValue('A' . $row, $no++);
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');

    $sheet->setCellValue('B' . $row, $kd_barang);
    $sheet->getColumnDimension('B')->setAutoSize(true);

    $sheet->setCellValue('C' . $row, $nmbrg_dtrkasir);
    $sheet->getColumnDimension('C')->setAutoSize(true);

    $sheet->setCellValue('D' . $row, $sat_dtrkasir);
    $sheet->getColumnDimension('D')->setAutoSize(true);

    $sheet->setCellValue('E' . $row, $stok);
    $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal('center');
    $sheet->getColumnDimension('E')->setAutoSize(true);
    
    $sheet->setCellValue('F' . $row, $ttlqty);
    $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal('center');
    $sheet->getColumnDimension('F')->setAutoSize(true);

    $sheet->setCellValue('G' . $row, '');
    $sheet->getColumnDimension('G')->setAutoSize(true);

    $sheet->setCellValue('H' . $row, '');
    $sheet->getColumnDimension('H')->setAutoSize(true);

    $sheet->setCellValue('I' . $row, number_format($hrgsat_barang));
    $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal('right');
    $sheet->getColumnDimension('I')->setAutoSize(true);
    
    $sheet->setCellValue('J' . $row, number_format($hrgjual_dtrkasir));
    $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal('right');
    $sheet->getColumnDimension('J')->setAutoSize(true);

    $sheet->setCellValue('K' . $row, date('H:i:s'));
    $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal('center');
    $sheet->getColumnDimension('K')->setWidth(15);

    $sheet->setCellValue('L' . $row, '');
    $sheet->getColumnDimension('L')->setAutoSize(true);
    $row++;
}

$writer = new Xlsx($spreadsheet);

// Create the table
$table = new Table('A4:L' . $rows, 'Table1');
// Optional: apply some styling to the table
$tableStyle = new TableStyle();
$tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM1);
$tableStyle->setShowRowStripes(true);
$table->setStyle($tableStyle);

// Add the table to the sheet
$sheet->addTable($table);

$writer->save('stok_opname.xlsx');
echo "<script>window.location = 'stok_opname.xlsx';
setInterval(function(){
    window.close();
},3000)
</script>";
