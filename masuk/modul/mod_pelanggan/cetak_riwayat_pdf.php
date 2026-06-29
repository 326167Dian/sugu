<?php
// Fungsi pembantu untuk menghitung jumlah baris MultiCell
function NbLines($w, $txt, $pdf) {
    $cw = &$pdf->CurrentFont['cw'];
    if($w==0) $w = $pdf->w - $pdf->rMargin - $pdf->x;
    $wmax = ($w - 2 * $pdf->cMargin) * 1000 / $pdf->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if($nb > 0 && $s[$nb-1] == "\n") $nb--;
    $sep = -1; $i = 0; $j = 0; $l = 0; $nl = 1;
    while($i < $nb) {
        $c = $s[$i];
        if($c == "\n") {
            $i++; $sep = -1; $j = $i; $l = 0; $nl++;
            continue;
        }
        if($c == ' ') $sep = $i;
        $l += $cw[$c];
        if($l > $wmax) {
            if($sep == -1) {
                if($i == $j) $i++;
            } else $i = $sep + 1;
            $sep = -1; $j = $i; $l = 0; $nl++;
        } else $i++;
    }
    return $nl;
}
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');

// Get patient data
$id_pelanggan = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_pelanggan == 0) {
    die("ID Pelanggan tidak valid");
}

// Ambil data header apotek
$ah = $db->query("SELECT * FROM setheader");
$rh = $ah->fetch(PDO::FETCH_ASSOC);

// Ambil data pelanggan
$stmt = $db->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
$stmt->execute([$id_pelanggan]);
$pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pelanggan) {
    die("Data pelanggan tidak ditemukan");
}

// Ambil riwayat pelanggan
$stmt_riwayat = $db->prepare("SELECT * FROM riwayat_pelanggan WHERE id_pelanggan = ? ORDER BY tgl DESC");
$stmt_riwayat->execute([$id_pelanggan]);
$riwayat = $stmt_riwayat->fetchAll(PDO::FETCH_ASSOC);

// Format tanggal lahir
$tgl_lahir = '';
if (!empty($pelanggan['tanggal_lahir'])) {
    $tgl_lahir = date('d-m-Y', strtotime($pelanggan['tanggal_lahir']));
}

// Create PDF - A4 Portrait
$pdf = new FPDF("P", "cm", "A4");
$pdf->SetMargins(1.5, 1, 1.5);
$pdf->AliasNbPages();
$pdf->AddPage();

// Logo di kiri atas
$myImage = "../../images/".$rh['logo'];
if (file_exists($myImage)) {
    $pdf->Image($myImage, 1.5, 1, 2, 2);
}

// HEADER - Info Apotek
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(18, 0.7, $rh['satu'], 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(18, 0.5, $rh['dua'], 0, 1, 'C');
$pdf->Cell(18, 0.5, $rh['tiga'], 0, 1, 'C');
$pdf->Cell(18, 0.5, $rh['empat'], 0, 1, 'C');


// Line separator
$pdf->Line(1.5, $pdf->GetY() + 0.3, 19.5, $pdf->GetY() + 0.3);
$pdf->Ln(0.5);

// TITLE
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(18, 0.7, 'RIWAYAT SWAMEDIKASI PELANGGAN', 0, 1, 'C');
$pdf->Ln(0.3);

// DATA PELANGGAN
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(18, 0.6, 'DATA PELANGGAN', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);

$pdf->Cell(4, 0.5, 'Nama', 0, 0, 'L');
$pdf->Cell(0.5, 0.5, ':', 0, 0, 'L');
$pdf->Cell(13.5, 0.5, $pelanggan['nm_pelanggan'], 0, 1, 'L');

$pdf->Cell(4, 0.5, 'Jenis Kelamin', 0, 0, 'L');
$pdf->Cell(0.5, 0.5, ':', 0, 0, 'L');
$pdf->Cell(13.5, 0.5, $pelanggan['jenis_kelamin'], 0, 1, 'L');

$pdf->Cell(4, 0.5, 'Tanggal Lahir', 0, 0, 'L');
$pdf->Cell(0.5, 0.5, ':', 0, 0, 'L');
$pdf->Cell(13.5, 0.5, $tgl_lahir, 0, 1, 'L');

$pdf->Cell(4, 0.5, 'Telepon', 0, 0, 'L');
$pdf->Cell(0.5, 0.5, ':', 0, 0, 'L');
$pdf->Cell(13.5, 0.5, $pelanggan['tlp_pelanggan'], 0, 1, 'L');

$pdf->Cell(4, 0.5, 'Alamat', 0, 0, 'L');
$pdf->Cell(0.5, 0.5, ':', 0, 0, 'L');
$pdf->MultiCell(13.5, 0.5, $pelanggan['alamat_pelanggan'], 0, 'L');

$pdf->Ln(0.3);

// TABEL RIWAYAT
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(18, 0.6, 'RIWAYAT SWAMEDIKASI', 0, 1, 'L');
$pdf->Ln(0.2);

// Table header
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(0.8, 0.6, 'No', 1, 0, 'C', true);
$pdf->Cell(2.2, 0.6, 'Tanggal', 1, 0, 'C', true);
$pdf->Cell(4.5, 0.6, 'Diagnosa', 1, 0, 'C', true);
$pdf->Cell(4.5, 0.6, 'Tindakan', 1, 0, 'C', true);
$pdf->Cell(4.5, 0.6, 'Followup', 1, 0, 'C', true);
$pdf->Cell(1.5, 0.6, 'Created', 1, 1, 'C', true);

// Table content
$pdf->SetFont('Arial', '', 8);
$no = 1;

if (count($riwayat) > 0) {
    foreach ($riwayat as $rw) {
        // Ambil data
        $tgl_format = date('d-m-Y', strtotime($rw['tgl']));
        $diagnosa   = $rw['diagnosa'];
        $tindakan   = $rw['tindakan'];
        $followup   = $rw['followup'];
        $created    = date('d-m-Y', strtotime($rw['created_at']));

        // --- LANGKAH 1: HITUNG TINGGI MAKSIMUM ---
        // Kita hitung jumlah baris yang akan dihasilkan oleh masing-masing MultiCell
        $w_diag = 4.5;
        $w_tind = 4.5;
        $w_foll = 4.5;

        $h_diag = NbLines($w_diag, $diagnosa, $pdf) * 0.5;
        $h_tind = NbLines($w_tind, $tindakan, $pdf) * 0.5;
        $h_foll = NbLines($w_foll, $followup, $pdf) * 0.5;

        // Ambil tinggi tertinggi dari semua kolom
        $height = max($h_diag, $h_tind, $h_foll);
        if ($height < 0.6) $height = 0.6; // Tinggi minimal baris

        // --- LANGKAH 2: CHECK PAGE BREAK ---
        if ($pdf->GetY() + $height > 27) {
            $pdf->AddPage();
            // (Ulangi Header Tabel di sini jika perlu)
        }

        // --- LANGKAH 3: GAMBAR BARIS ---
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Gambar Bingkai Luar agar garis vertikal terhubung sempurna
        // Ini trik agar garis kolom yang pendek tetap memanjang sampai bawah baris
        $pdf->Rect($x, $y, 0.8, $height);   // No
        $pdf->Rect($x+0.8, $y, 2.2, $height); // Tanggal
        $pdf->Rect($x+3.0, $y, 4.5, $height); // Diagnosa
        $pdf->Rect($x+7.5, $y, 4.5, $height); // Tindakan
        $pdf->Rect($x+12.0, $y, 4.5, $height); // Followup
        $pdf->Rect($x+16.5, $y, 1.5, $height); // Created

        // Isi Data
        $pdf->Cell(0.8, $height, $no, 0, 0, 'C');
        $pdf->Cell(2.2, $height, $tgl_format, 0, 0, 'C');
        
        // MultiCell untuk kolom dinamis (border diatur 0 karena sudah ada Rect)
        $pdf->SetXY($x+3.0, $y);
        $pdf->MultiCell(4.5, 0.5, $diagnosa, 0, 'L');
        
        $pdf->SetXY($x+7.5, $y);
        $pdf->MultiCell(4.5, 0.5, $tindakan, 0, 'L');
        
        $pdf->SetXY($x+12.0, $y);
        $pdf->MultiCell(4.5, 0.5, $followup, 0, 'L');
        
        $pdf->SetXY($x+16.5, $y);
        $pdf->Cell(1.5, $height, $created, 0, 0, 'C');

        // Pindahkan Y ke baris berikutnya
        $pdf->SetXY($x, $y + $height);
        $no++;
    }
}
$pdf->Ln(1);

// Footer - Tanggal cetak
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(18, 0.5, 'Dicetak pada: ' . date('d-m-Y H:i:s'), 0, 1, 'R');

$pdf->Output("Riwayat_Pelanggan", "I");