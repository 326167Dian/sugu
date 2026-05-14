<?php
ob_start();
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

$kd_trkasir = isset($_GET['kd_trkasir']) ? $_GET['kd_trkasir'] : '';

function wrapReceiptText($text, $maxChars = 30)
{
    $clean = trim((string)$text);
    if ($clean === '') {
        return '-';
    }

    return wordwrap($clean, $maxChars, "\n", true);
}

//ambil header
$ah = $db->prepare("SELECT * FROM setheader");
$ah->execute();
$rh = $ah->fetch(PDO::FETCH_ASSOC);
if (!$rh) {
    $rh = [
        'satu' => '',
        'dua' => '',
        'tiga' => '',
        'empat' => '',
        'lima' => '',
        'enam' => '',
        'delapan' => '',
        'sembilan' => '',
        'sepuluh' => '',
        'sebelas' => ''
    ];
}

$dt = $db->prepare("SELECT * FROM trkasir
                    JOIN carabayar ON trkasir.id_carabayar = carabayar.id_carabayar
                    WHERE trkasir.kd_trkasir=?");
$dt->execute([$kd_trkasir]);
$r1 = $dt->fetch(PDO::FETCH_ASSOC);
if (!$r1) {
    $r1 = [
        'kd_trkasir' => $kd_trkasir,
        'tgl_trkasir' => date('Y-m-d'),
        'nm_pelanggan' => '',
        'tlp_pelanggan' => '',
        'nm_carabayar' => '',
        'ttl_trkasir' => 0,
        'dp_bayar' => 0,
        'sisa_bayar' => 0,
        'id_pelanggan' => 0,
        'poin_awal' => 0,
        'tambahan_poin' => 0,
        'redeem_poin' => 0,
        'petugas' => ''
    ];
}

$detailQueryForPaper = $db->prepare("SELECT nmbrg_dtrkasir, resep FROM trkasir_detail WHERE kd_trkasir=? ORDER BY id_dtrkasir ASC");
$detailQueryForPaper->execute([$kd_trkasir]);
$detailRowsForPaper = $detailQueryForPaper->fetchAll(PDO::FETCH_ASSOC);

$ukuran1 = 20.7; //setingan kertas
$tambahukuran = 0;
foreach ($detailRowsForPaper as $rowPaper) {
    $isResepLine = isset($rowPaper['resep']) && strtoupper((string)$rowPaper['resep']) === 'YA';
    if ($isResepLine) {
        $tambahukuran += 0.6;
        continue;
    }

    $wrappedName = wrapReceiptText(isset($rowPaper['nmbrg_dtrkasir']) ? $rowPaper['nmbrg_dtrkasir'] : '', 35);
    $lineCount = max(1, substr_count($wrappedName, "\n") + 1);
    // Tinggi nama + detail qty/harga + jarak antar item.
    $tambahukuran += ($lineCount * 0.24) + 0.52;
}
$tinggikertas = $ukuran1 + $tambahukuran;


//$pdf = new FPDF("P","cm","A4");
// $pdf = new FPDF("P", "cm", array($tinggikertas, 8));
$pdf = new FPDF("P", "cm", array($tinggikertas, 5.2));
$pdf->SetMargins(0.2, -1, 0.2);
$pdf->AliasNbPages();
$pdf->AddPage();

//$pdf->Image('../../images/mmd.jpg',1,1.5,5,2);
//HEADER 1
$pdf->Line(0.2, 2.9, 4.8, 2.9); //horisontal bawah

$pdf->Line(0.2, 4.9, 4.8, 4.9); //judul tabel atas
// $pdf->Line(0.2, 5.5, 4.8, 5.5); //judul tabel atas

$text = substr($rh['satu'], 7);


$pdf->ln(1.3);
$pdf->SetFont('Arial','B', 10);
$pdf->Cell(5, 0.4, 'APOTEK', 0, 1, 'C');
$pdf->SetFont('Arial','B', 10);
$pdf->Cell(5, 0.4, $text, 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(5, 0.4, $rh['dua'], 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(5, 0.4, $rh['tiga'], 0, 1, 'C');
$pdf->Cell(5, 0.3, $rh['empat'], 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(5, 0.3,'SIA : '. $rh['lima'], 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(5, 0.3,'Telp : '. $rh['enam'], 0, 1, 'C');
$pdf->Cell(5, 0.5, '', 0, 1, 'C');

//KIRI 1
$pdf->ln(0.2);
$pdf->SetX(0.2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(1.7, 0, 'No Nota', 0, 0, 'L');
$pdf->Cell(0.2, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(1.8, 0, $r1['kd_trkasir'], 0, 0, 'L');

//KIRI 2
$pdf->ln(0.4);
$pdf->SetX(0.2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(1.7, 0, 'Tanggal', 0, 0, 'L');
$pdf->Cell(0.2, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(1.8, 0, tgl_indo($r1['tgl_trkasir']), 0, 0, 'L');


//KIRI 3
$pdf->ln(0.4);
$pdf->SetX(0.2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(1.7, 0, 'Pelanggan', 0, 0, 'L');
$pdf->Cell(0.2, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(1.8, 0, $r1['nm_pelanggan'], 0, 0, 'L');

//KIRI 4
$pdf->ln(0.4);
$pdf->SetX(0.2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(1.7, 0, 'No Telp/HP', 0, 0, 'L');
$pdf->Cell(0.2, 0, ':', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(1.8, 0, $r1['tlp_pelanggan'], 0, 0, 'L');

$pdf->ln(0.3);
$pdf->SetX(0.2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(1, 0.5, 'Item', 0, 0, 'L');
$pdf->Cell(0.5, 0.5, 'Qty', 0, 0, 'C');
$pdf->Cell(1.5, 0.5, 'Harga', 0, 0, 'R');
$pdf->Cell(1.5, 0.5, 'Jumlah', 0, 1, 'R');

$pdf->SetX(0.2);
$pdf->SetFont('Arial', 'B', 8);

$no = 1;

$query = $db->prepare("SELECT * FROM trkasir_detail WHERE kd_trkasir=?
	                    ORDER BY id_dtrkasir ASC");
$query->execute([$kd_trkasir]);

$st = [];
$totalresep = 0;
$adaResep = false;

while ($r2 = $query->fetch(PDO::FETCH_ASSOC)) {
    $st[] = $r2['hrgttl_dtrkasir'];

    if (isset($r2['resep']) && $r2['resep'] === 'YA') {
        $adaResep = true;
        $totalresep += $r2['hrgjual_dtrkasir'];
        continue;
    }

    $pdf->SetX(0.2);

    $namaBarangWrapped = wrapReceiptText($r2['nmbrg_dtrkasir'], 32);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->MultiCell(4.6, 0.24, $namaBarangWrapped, 0, 'L');

    $pdf->SetX(0.2);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(1.2, 0.34, $r2['qty_dtrkasir'], 0, 0, 'R');
    $pdf->Cell(0.7, 0.34, $r2['sat_dtrkasir'], 0, 0, 'C');
    $pdf->Cell(1.3, 0.34, format_rupiah($r2['hrgjual_dtrkasir']), 0, 0, 'R');
    $pdf->Cell(1.4, 0.34, format_rupiah($r2['hrgttl_dtrkasir']), 0, 1, 'R');
    $pdf->Ln(0.12);

    $no++;
}

if ($adaResep) {
    $pdf->SetX(0.2);
    $pdf->Cell(5.7, 0.4, 'Resep ' . $r1['kd_trkasir'], 0, 1, 'L');
    $pdf->Cell(1, 0.4, '1', 0, 0, 'R');
    $pdf->Cell(1, 0.4, '', 0, 0, 'C');
    $pdf->Cell(1, 0.4, format_rupiah($totalresep), 0, 0, 'R');
    $pdf->Cell(1.5, 0.4, format_rupiah($totalresep), 0, 1, 'R');
    
    $pdf->Ln(0.1);
}

$gt = array_sum($st);
$disc = $gt > 0 ? (($gt - $r1['ttl_trkasir']) / $gt) * 100 : 0;
$disc_tampil = number_format($disc, 2, ',', '.') . '%';
$tagihan = format_rupiah($r1['ttl_trkasir']);
$subtotal = format_rupiah($gt);

$lineAfterItemsY = $pdf->GetY() + 0.06;
$pdf->Line(0.2, $lineAfterItemsY, 4.8, $lineAfterItemsY);
$pdf->SetY($lineAfterItemsY);
// $pdf->SetFont('Arial','U');
// $pdf->Cell(4.8, 0.3,'______________________________________', 0, 0, 'L');

$pdf->ln(0.25);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetX(0.2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(2, 0.4, 'Met byr : ', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(1.5, 0.4, 'Sub Total : ', 0, 0, 'R');
$pdf->Cell(1.2, 0.4, $subtotal, 0, 1, 'R');
$pdf->SetX(0.2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(1.5, 0.4, $r1['nm_carabayar'], 0, 0, 'L');
$pdf->SetX(0.2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(2, 0.4, $r1['nm_carabayar'], 0, 0, 'L');
$pdf->Cell(1.5, 0.4, 'Diskon (%) : ', 0, 0, 'R');
$pdf->Cell(1.2, 0.4, $disc_tampil, 0, 1, 'R');
$pdf->SetX(0.2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(3.5, 0.4, 'Tagihan : ', 0, 0, 'R');
$pdf->Cell(1.2, 0.4, $tagihan, 0, 1, 'R');
$pdf->SetX(0.2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(3.5, 0.4, 'Uang Cash : ', 0, 0, 'R');
$pdf->Cell(1.2, 0.4, format_rupiah($r1['dp_bayar']), 0, 1, 'R');
$pdf->SetX(0.2);
$pdf->Cell(3.5, 0.4, 'Kembalian : ', 0, 0, 'R');
$pdf->Cell(1.2, 0.4, format_rupiah($r1['sisa_bayar']), 0, 1, 'R');

$lineAfterTotalY = $pdf->GetY() + 0.08;
$pdf->Line(0.2, $lineAfterTotalY, 4.8, $lineAfterTotalY);
$pdf->SetY($lineAfterTotalY);

$stmt_pelanggan = $db->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = :id_pelanggan");
$stmt_pelanggan->execute([
    ':id_pelanggan' => $r1['id_pelanggan']
]);
$poin = $stmt_pelanggan->fetch(PDO::FETCH_ASSOC);
$total_poin = ($poin && isset($poin['total_poin'])) ? $poin['total_poin'] : 0;

$pdf->ln(0.4);
$pdf->SetX(0.2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(2, 0.4, 'Poin Awal : ', 0, 0, 'L');
$pdf->Cell(2.7, 0.4, format_rupiah($r1['poin_awal']), 0, 1, 'R');

$pdf->SetX(0.2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(2, 0.4, 'Tambahan Poin : ', 0, 0, 'L');
$pdf->Cell(2.7, 0.4, format_rupiah($r1['tambahan_poin']), 0, 1, 'R');

$pdf->SetX(0.2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(2, 0.4, 'Redeem Poin : ', 0, 0, 'L');
$pdf->Cell(2.7, 0.4, format_rupiah($r1['redeem_poin']*-1), 0, 1, 'R');

$pdf->SetX(0.2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(2, 0.4, 'Sisa Poin : ', 0, 0, 'L');
$pdf->Cell(2.7, 0.4, format_rupiah($total_poin), 0, 1, 'R');

// $pdf->ln(0.1);
// $pdf->SetX(0.6);
// $pdf->SetFont('Arial','',10);
// $pdf->Cell(0,0.3,'',0,1,'L');
// $pdf->SetX(0.6);
// $pdf->Cell(0,0.4,$r1['ket_trkasir'],0,0,'L');

$pdf->ln(0.6);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(4.6, 0.3, $rh['delapan'], 0, 1, 'C');
// $pdf->Cell(4.6, 0.3, $rh['sembilan'], 0, 1, 'C');
$pdf->MultiCell(4.6, 0.2, $rh['sembilan'],0,'C');
$pdf->Cell(4.6, 0.3, $rh['sepuluh'], 0, 1, 'C');
$pdf->Cell(4.6, 0.3, $rh['sebelas'], 0, 1, 'C');
$pdf->Cell(4.6, 0.3, "Kasir : " . $r1['petugas'], 0, 1, 'C');

if (ob_get_length()) {
    ob_end_clean();
}
$pdf->Output("struk_wallpaper", "I");
