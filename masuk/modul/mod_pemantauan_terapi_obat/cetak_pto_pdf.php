<?php
session_start();
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');

function ptoNbLines($pdf, $w, $txt)
{
    $cw = &$pdf->CurrentFont['cw'];
    if ($w == 0) {
        $w = $pdf->w - $pdf->rMargin - $pdf->x;
    }

    $wmax = ($w - 2 * $pdf->cMargin) * 1000 / $pdf->FontSize;
    $s = str_replace("\r", '', (string)$txt);
    $nb = strlen($s);
    if ($nb > 0 && $s[$nb - 1] == "\n") {
        $nb--;
    }

    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;

    while ($i < $nb) {
        $c = $s[$i];
        if ($c == "\n") {
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            $nl++;
            continue;
        }

        if ($c == ' ') {
            $sep = $i;
        }

        $charWidth = isset($cw[$c]) ? $cw[$c] : 500;
        $l += $charWidth;

        if ($l > $wmax) {
            if ($sep == -1) {
                if ($i == $j) {
                    $i++;
                }
            } else {
                $i = $sep + 1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            $nl++;
        } else {
            $i++;
        }
    }

    return $nl;
}

function ptoPrintTableHeader($pdf)
{
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(0.9, 0.7, 'No', 1, 0, 'C', true);
    $pdf->Cell(2.0, 0.7, 'Tanggal 1', 1, 0, 'C', true);
    $pdf->Cell(2.0, 0.7, 'Tanggal 2', 1, 0, 'C', true);
    $pdf->Cell(4.8, 0.7, 'Catatan', 1, 0, 'C', true);
    $pdf->Cell(4.8, 0.7, 'Obat', 1, 0, 'C', true);
    $pdf->Cell(4.8, 0.7, 'Masalah', 1, 0, 'C', true);
    $pdf->Cell(4.8, 0.7, 'Tindak', 1, 0, 'C', true);
    $pdf->Cell(2.0, 0.7, 'Tempat', 1, 0, 'C', true);
    $pdf->Cell(1.6, 0.7, 'TTD', 1, 1, 'C', true);
}

 // Removed login restriction to allow access without login

$id_pelanggan = isset($_GET['id_pelanggan']) ? intval($_GET['id_pelanggan']) : 0;
$tgl_awal = isset($_GET['tgl_awal']) ? trim($_GET['tgl_awal']) : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? trim($_GET['tgl_akhir']) : '';

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_awal)) {
    $tgl_awal = '';
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_akhir)) {
    $tgl_akhir = '';
}

if ($id_pelanggan <= 0) {
    die('ID pelanggan tidak valid');
}

$stmtPelanggan = $db->prepare("SELECT id_pelanggan, nm_pelanggan FROM pelanggan WHERE id_pelanggan = ?");
$stmtPelanggan->execute([$id_pelanggan]);
$pelanggan = $stmtPelanggan->fetch(PDO::FETCH_ASSOC);
if (!$pelanggan) {
    die('Data pelanggan tidak ditemukan');
}

$sql = "SELECT * FROM pto WHERE id_pelanggan = ?";
$params = [$id_pelanggan];

if ($tgl_awal !== '') {
    $sql .= " AND COALESCE(tanggal_1, DATE(created_at)) >= ?";
    $params[] = $tgl_awal;
}
if ($tgl_akhir !== '') {
    $sql .= " AND COALESCE(tanggal_1, DATE(created_at)) <= ?";
    $params[] = $tgl_akhir;
}

$sql .= " ORDER BY id_pto DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF("L", "cm", "A4");
$pdf->SetMargins(1, 1, 1);
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(27.7, 0.8, 'LAPORAN RIWAYAT PTO', 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(27.7, 0.5, 'Nama Pelanggan: ' . $pelanggan['nm_pelanggan'], 0, 1, 'L');

$periodeText = 'Semua Tanggal';
if ($tgl_awal !== '' || $tgl_akhir !== '') {
    $periodeText = ($tgl_awal !== '' ? $tgl_awal : '...') . ' s/d ' . ($tgl_akhir !== '' ? $tgl_akhir : '...');
}
$pdf->Cell(27.7, 0.5, 'Filter Tanggal: ' . $periodeText, 0, 1, 'L');
$pdf->Cell(27.7, 0.5, 'Dicetak: ' . date('Y-m-d H:i:s'), 0, 1, 'L');
$pdf->Ln(0.2);

ptoPrintTableHeader($pdf);

$pdf->SetFont('Arial', '', 7.5);
if (empty($rows)) {
    $pdf->Cell(27.7, 0.8, 'Tidak ada data PTO.', 1, 1, 'C');
} else {
    $no = 1;
    foreach ($rows as $row) {
        $catatanGabung = "1) " . trim((string)$row['catatan_1']) . "\n2) " . trim((string)$row['catatan_2']);
        $obatGabung = "1) " . trim((string)$row['obat_1']) . "\n2) " . trim((string)$row['obat_2']);
        $masalahGabung = "1) " . trim((string)$row['masalah_1']) . "\n2) " . trim((string)$row['masalah_2']);
        $tindakGabung = "1) " . trim((string)$row['tindak_1']) . "\n2) " . trim((string)$row['tindak_2']);

        $lineCounts = [
            ptoNbLines($pdf, 0.9, (string)$no),
            ptoNbLines($pdf, 2.0, (string)$row['tanggal_1']),
            ptoNbLines($pdf, 2.0, (string)$row['tanggal_2']),
            ptoNbLines($pdf, 4.8, $catatanGabung),
            ptoNbLines($pdf, 4.8, $obatGabung),
            ptoNbLines($pdf, 4.8, $masalahGabung),
            ptoNbLines($pdf, 4.8, $tindakGabung),
            ptoNbLines($pdf, 2.0, (string)$row['tempat_ttd']),
            ptoNbLines($pdf, 1.6, (string)$row['tanggal_ttd'])
        ];

        $height = max($lineCounts) * 0.45;
        if ($height < 0.8) {
            $height = 0.8;
        }

        if ($pdf->GetY() + $height > 20) {
            $pdf->AddPage();
            ptoPrintTableHeader($pdf);
            $pdf->SetFont('Arial', '', 7.5);
        }

        $x = $pdf->GetX();
        $y = $pdf->GetY();

        $widths = [0.9, 2.0, 2.0, 4.8, 4.8, 4.8, 4.8, 2.0, 1.6];
        $offset = 0;
        foreach ($widths as $w) {
            $pdf->Rect($x + $offset, $y, $w, $height);
            $offset += $w;
        }

        $pdf->SetXY($x, $y);
        $pdf->Cell(0.9, $height, $no, 0, 0, 'C');

        $pdf->SetXY($x + 0.9, $y);
        $pdf->Cell(2.0, $height, (string)$row['tanggal_1'], 0, 0, 'C');

        $pdf->SetXY($x + 2.9, $y);
        $pdf->Cell(2.0, $height, (string)$row['tanggal_2'], 0, 0, 'C');

        $pdf->SetXY($x + 4.9, $y);
        $pdf->MultiCell(4.8, 0.45, $catatanGabung, 0, 'L');

        $pdf->SetXY($x + 9.7, $y);
        $pdf->MultiCell(4.8, 0.45, $obatGabung, 0, 'L');

        $pdf->SetXY($x + 14.5, $y);
        $pdf->MultiCell(4.8, 0.45, $masalahGabung, 0, 'L');

        $pdf->SetXY($x + 19.3, $y);
        $pdf->MultiCell(4.8, 0.45, $tindakGabung, 0, 'L');

        $pdf->SetXY($x + 24.1, $y);
        $pdf->MultiCell(2.0, 0.45, (string)$row['tempat_ttd'], 0, 'L');

        $pdf->SetXY($x + 26.1, $y);
        $pdf->Cell(1.6, $height, (string)$row['tanggal_ttd'], 0, 0, 'C');

        $pdf->SetXY($x, $y + $height);
        $no++;
    }
}

$pdf->Output('Riwayat_PTO_' . $id_pelanggan . '.pdf', 'I');
