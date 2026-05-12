<?php
session_start();
include "../../../configurasi/koneksi.php";
require "../../assets/pdf/fpdf.php";
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

$range = isset($_GET['range']) ? $_GET['range'] : "";
if ($range !== "") {
    $de = explode("/", $range);
    $tgl_awal = isset($de[0]) ? $de[0] : "";
    $tgl_akhir = isset($de[1]) ? $de[1] : "";
} else {
    $tgl_awal = isset($_POST['tgl_awal']) ? $_POST['tgl_awal'] : "";
    $tgl_akhir = isset($_POST['tgl_akhir']) ? $_POST['tgl_akhir'] : "";
}

if ($tgl_awal === "" || $tgl_akhir === "") {
    $awal = date("Y-m-d");
    $akhir = date("Y-m-d");
} else {
    $awal = date("Y-m-d", strtotime($tgl_awal));
    $akhir = date("Y-m-d", strtotime($tgl_akhir));
}

$query1 = $db->prepare("SELECT SUM(ttl_trkasir) AS penjualan FROM trkasir WHERE tgl_trkasir BETWEEN :awal AND :akhir");
$query1->execute(array(":awal" => $awal, ":akhir" => $akhir));

$query2 = $db->prepare("SELECT SUM(stok_barang*hrgsat_barang) AS aset_tdk_lancar FROM barang");
$query2->execute();

$query3 = $db->prepare("SELECT SUM(trkasir.ttl_trkasir) AS piutang FROM trkasir WHERE trkasir.id_carabayar = '3' AND trkasir.tgl_trkasir BETWEEN :awal AND :akhir");
$query3->execute(array(":awal" => $awal, ":akhir" => $akhir));

$query4 = $db->prepare("SELECT SUM(trbmasuk.ttl_trbmasuk) AS hutang FROM trbmasuk WHERE trbmasuk.carabayar = 'KREDIT' AND trbmasuk.tgl_trbmasuk BETWEEN :awal AND :akhir");
$query4->execute(array(":awal" => $awal, ":akhir" => $akhir));

$query5 = $db->prepare("SELECT SUM(trbmasuk.ttl_trbmasuk) AS pembelian_cash FROM trbmasuk WHERE trbmasuk.carabayar = 'LUNAS' AND trbmasuk.tgl_trbmasuk BETWEEN :awal AND :akhir");
$query5->execute(array(":awal" => $awal, ":akhir" => $akhir));

$p = $query1->fetch(PDO::FETCH_ASSOC);
$o = $query5->fetch(PDO::FETCH_ASSOC);
$x = $query3->fetch(PDO::FETCH_ASSOC);
$y = $query4->fetch(PDO::FETCH_ASSOC);
$asettdklancar = $query2->fetch(PDO::FETCH_ASSOC);

$penjualan = isset($p['penjualan']) ? (float)$p['penjualan'] : 0;
$pembelian_cash = isset($o['pembelian_cash']) ? (float)$o['pembelian_cash'] : 0;
$piutang = isset($x['piutang']) ? (float)$x['piutang'] : 0;
$hutang = isset($y['hutang']) ? (float)$y['hutang'] : 0;
$aset_tdk_lancar = isset($asettdklancar['aset_tdk_lancar']) ? (float)$asettdklancar['aset_tdk_lancar'] : 0;

$asetlancar = ($penjualan - $pembelian_cash - $hutang);
$neraca = ($penjualan - $piutang - $hutang - $pembelian_cash);

class PDF extends FPDF
{
    public $periodText = "";
    public $printedBy = "";
    public $logoPath = "";

    public function Header()
    {
        global $db;
        if ($db instanceof PDO) {
            $ah = $db->query("SELECT * FROM setheader");
            $rh = $ah ? $ah->fetch(PDO::FETCH_ASSOC) : null;
            if ($rh && !empty($rh['logo'])) {
                $myImage = __DIR__ . "/../../images/" . $rh['logo'];
                if (file_exists($myImage)) {
                    $this->Image($myImage, 1, 1, 2, 2.3);
                }
            }
        }
        // if ($this->logoPath !== "" && file_exists($this->logoPath)) {
        //     $this->Image($this->logoPath, 1, 0.8, 1.2);
        // }
        $this->SetX(3.5);
        $this->SetFont("Arial", "B", 12);      
        $this->Cell(2.5, 0.7, "NERACA LABA RUGI", 0, 1, "L");
        $this->SetX(3.5);
        $this->SetFont("Arial", "", 9);
        $this->Cell(2.5, 0.5, "Tanggal Cetak : " . date("d-m-Y H:i:s"), 0, 1, "L");
        if ($this->printedBy !== "") {
            $this->SetX(3.5);
            $this->Cell(2.5, 0.5, "Dicetak Oleh : " . $this->printedBy, 0, 1, "L");
        }
        if ($this->periodText !== "") {
            $this->SetX(3.5);
            $this->Cell(2.5, 0.5, "Periode : " . $this->periodText, 0, 1, "L");
        }
        $this->Ln(0.2);
    }

    public function Footer()
    {
        $this->SetY(-1.2);
        $this->SetFont("Arial", "", 8);
        $this->Cell(0, 0.5, "Halaman " . $this->PageNo() . "/{nb}", 0, 0, "R");
    }
}

$pdf = new PDF("P", "cm", "A4");
$pdf->SetMargins(1, 1.2, 1);
$pdf->SetAutoPageBreak(true, 1.5);
$pdf->AliasNbPages();
$pdf->periodText = tgl_indo($awal) . " - " . tgl_indo($akhir);
$pdf->printedBy = isset($_SESSION['namalengkap']) ? $_SESSION['namalengkap'] : "";
$pdf->logoPath = __DIR__ . "/../../images/logo.jpg";
$pdf->AddPage();

$pdf->Ln(0.2);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(1, 0.7, "No", 1, 0, "C");
$pdf->Cell(10, 0.7, "Keterangan", 1, 0, "C");
$pdf->Cell(4, 0.7, "Nilai", 1, 1, "C");

$pdf->SetFont("Arial", "", 9);
$pdf->Cell(1, 0.7, "1", 1, 0, "C");
$pdf->Cell(10, 0.7, "Penjualan", 1, 0, "L");
$pdf->Cell(4, 0.7, "Rp " . format_rupiah($penjualan), 1, 1, "R");

$pdf->Cell(1, 0.7, "2", 1, 0, "C");
$pdf->Cell(10, 0.7, "Pembelian Cash", 1, 0, "L");
$pdf->Cell(4, 0.7, "Rp " . format_rupiah($pembelian_cash), 1, 1, "R");

$pdf->Cell(1, 0.7, "3", 1, 0, "C");
$pdf->Cell(10, 0.7, "Piutang (Total Penjualan Belum Dibayar)", 1, 0, "L");
$pdf->Cell(4, 0.7, "Rp " . format_rupiah($piutang), 1, 1, "R");

$pdf->Cell(1, 0.7, "4", 1, 0, "C");
$pdf->Cell(10, 0.7, "Hutang (Total Pembelian Belum Dibayar)", 1, 0, "L");
$pdf->Cell(4, 0.7, "Rp " . format_rupiah($hutang), 1, 1, "R");

$pdf->Cell(1, 0.7, "5", 1, 0, "C");
$pdf->Cell(10, 0.7, "Total Asset Lancar", 1, 0, "L");
$pdf->Cell(4, 0.7, "Rp " . format_rupiah($asetlancar), 1, 1, "R");

$pdf->Cell(1, 0.7, "6", 1, 0, "C");
$pdf->Cell(10, 0.7, "Total Asset Tidak Lancar", 1, 0, "L");
$pdf->Cell(4, 0.7, "Rp " . format_rupiah($aset_tdk_lancar), 1, 1, "R");

$pdf->Cell(1, 0.7, "7", 1, 0, "C");
$pdf->Cell(10, 0.7, "Neraca Laba/Rugi", 1, 0, "L");
$pdf->Cell(4, 0.7, "Rp " . format_rupiah($neraca), 1, 1, "R");

$pdf->Output("neraca_laba_rugi.pdf", "I");
?>
