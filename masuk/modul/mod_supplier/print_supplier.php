<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
    echo "<link href=../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
    die();
}

include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');

// Bungkus teks per kata supaya muat di lebar kolom $width (cm), berdasarkan lebar render
// sesungguhnya (bukan cuma tebak jumlah karakter) -- dipakai untuk kolom yang isinya bisa panjang
// seperti Nama Supplier / Alamat Supplier.
function wrapTextByWidth($pdf, $text, $width) {
    $words = explode(' ', $text);
    $lines = [];
    $current = '';
    foreach ($words as $word) {
        $tentative = ($current === '') ? $word : $current . ' ' . $word;
        if ($pdf->GetStringWidth($tentative) > $width && $current !== '') {
            $lines[] = $current;
            $current = $word;
        } else {
            $current = $tentative;
        }
    }
    if ($current !== '') {
        $lines[] = $current;
    }
    if (empty($lines)) {
        $lines[] = '';
    }
    return $lines;
}

// Gambar kotak border kosong setinggi $rowHeight, lalu tempatkan blok teks ($lines, tinggi
// aslinya sendiri) di tengah secara vertikal di dalam kotak itu -- dipakai untuk kolom yang
// isinya lebih pendek dari baris tertinggi di baris tabel yang sama (mis. Nama Supplier / Telp
// saat Alamat Supplier di baris yang sama sampai 2-3 baris).
function drawCenteredColumn($pdf, $x, $y, $width, $rowHeight, $lineHeight, $lines, $align) {
    $pdf->Rect($x, $y, $width, $rowHeight);
    $tinggiTeks = count($lines) * $lineHeight;
    $offsetY = ($rowHeight - $tinggiTeks) / 2;
    $pdf->SetXY($x, $y + $offsetY);
    foreach ($lines as $line) {
        $pdf->Cell($width, $lineHeight, $line, 0, 2, $align);
    }
}

$pdf = new FPDF("P", "cm", "A4");
$pdf->SetMargins(1.5, 1.5, 1.5);
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(18, 0.7, "DATA SUPPLIER", 0, 1, 'C');
$pdf->ln(0.3);

$colNo = 1.2;
$colNama = 5.5;
$colTelp = 3.5;
$colAlamat = 7.8;
$lineHeight = 0.6;
$paddingX = 0.15; // sedikit jarak dalam supaya teks tidak nempel garis tepi kolom

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell($colNo, 0.7, 'No', 1, 0, 'C');
$pdf->Cell($colNama, 0.7, 'Nama Supplier', 1, 0, 'L');
$pdf->Cell($colTelp, 0.7, 'Telp', 1, 0, 'L');
$pdf->Cell($colAlamat, 0.7, 'Alamat Supplier', 1, 1, 'L');

$pdf->SetFont('Arial', '', 8);

$stmt = $db->query("SELECT * FROM supplier ORDER BY id_supplier ASC");

$no = 1;
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $namaLines = wrapTextByWidth($pdf, $r['nm_supplier'], $colNama - (2 * $paddingX));
    $alamatLines = wrapTextByWidth($pdf, $r['alamat_supplier'], $colAlamat - (2 * $paddingX));
    $jumlahBaris = max(count($namaLines), count($alamatLines), 1);
    $tinggiBaris = $lineHeight * $jumlahBaris;

    // Cegah baris tabel terpotong/menimpa header saat pindah halaman.
    if ($pdf->GetY() + $tinggiBaris > $pdf->PageBreakTrigger) {
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($colNo, 0.7, 'No', 1, 0, 'C');
        $pdf->Cell($colNama, 0.7, 'Nama Supplier', 1, 0, 'L');
        $pdf->Cell($colTelp, 0.7, 'Telp', 1, 0, 'L');
        $pdf->Cell($colAlamat, 0.7, 'Alamat Supplier', 1, 1, 'L');
        $pdf->SetFont('Arial', '', 8);
    }

    $xAwal = $pdf->GetX();
    $yAwal = $pdf->GetY();

    drawCenteredColumn($pdf, $xAwal, $yAwal, $colNo, $tinggiBaris, $lineHeight, [$no], 'C');
    drawCenteredColumn($pdf, $xAwal + $colNo, $yAwal, $colNama, $tinggiBaris, $lineHeight, $namaLines, 'L');
    drawCenteredColumn($pdf, $xAwal + $colNo + $colNama, $yAwal, $colTelp, $tinggiBaris, $lineHeight, [$r['tlp_supplier']], 'L');
    drawCenteredColumn($pdf, $xAwal + $colNo + $colNama + $colTelp, $yAwal, $colAlamat, $tinggiBaris, $lineHeight, $alamatLines, 'L');

    $pdf->SetXY($xAwal, $yAwal + $tinggiBaris);
    $no++;
}

$pdf->ln(0.3);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(18, 0.5, "Tanggal Cetak : " . date('d-m-Y H:i:s') . " || Dicetak Oleh : " . $_SESSION['namalengkap'], 0, 0, 'L');

$pdf->Output("Data_Supplier.pdf", "I");
