<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/fpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

class OrdersPDF extends FPDF {
    private $rh;
    private $kdorders;
    private $res;
    private $alt;

    public function __construct($orientation, $unit, $size, $rh, $kdorders, $res, $alt) {
        parent::__construct($orientation, $unit, $size);
        $this->rh = $rh;
        $this->kdorders = $kdorders;
        $this->res = $res;
        $this->alt = $alt;
    }

    public function Header() {
        // $this->Image('../../images/logo.png',1,0.7,2,2.5,'');
        $myImage = "../../images/".$this->rh['logo'];
        $this->Image($myImage, 1, 0.7, 2, 2.3);
        $this->ln(1);
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(3, 0.4,'' , 0, 0, 'C');
        $this->Cell(10, 0.4, $this->rh['satu'], 0, 1, 'C');

        $this->ln(0.3);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(2.5, 0.4,'' , 0, 0, 'C');
        $this->Cell(10, 0.5,$this->rh['dua'], 0, 1, 'C');
        $this->Cell(2.5, 0.4,'' , 0, 0, 'C');
        $this->Cell(10, 0.5, $this->rh['tiga'], 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(2.5, 0.4,'' , 0, 0, 'C');
        $this->Cell(10, 0.5,'SIA : '.$this->rh['lima'].'  '.'Telp : '.$this->rh['enam'] , 0, 1, 'C');
        $this->Cell(2.5, 0.4,'' , 0, 0, 'C');
        //$this->Cell(10, 0.5,'APJ : '.$this->rh['empat'], 0, 1, 'C');

        $this->SetLineWidth(0.15);
        $this->Line(0.5, 3.3, 14.3, 3.3); //horisontal bawah

        $this->ln(0.7);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(14, 0, 'SURAT PESANAN OBAT', 0, 0, 'C');

        $this->ln(1);
        $this->SetX(1);
        $this->SetFont('Arial', '', 10);
        $this->Cell(2.5, 0, 'Nomor SP', 0, 0, 'L');
        $this->Cell(0.5, 0, ':', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(10, 0, $this->kdorders, 0, 0, 'L');

        $this->ln(0.5);
        $this->SetX(1);
        $this->SetFont('Arial', '', 10);
        $this->Cell(2.5, 0, 'Tanggal', 0, 0, 'L');
        $this->Cell(0.5, 0, ':', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(10, 0, tgl_indo($this->res['tgl_trbmasuk']), 0, 0, 'L');

        $this->ln(0.5);
        $this->SetX(1);
        $this->SetFont('Arial', '', 10);
        $this->Cell(2.5, 0, 'Kepada', 0, 0, 'L');
        $this->Cell(0.5, 0, ':', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(10, 0, $this->res['nm_supplier'], 0, 0, 'L');

        $this->ln(0.5);
        $this->SetX(1);
        $this->SetFont('Arial', '', 10);
        $this->Cell(2.5, 0, 'Alamat', 0, 0, 'L');
        $this->Cell(0.5, 0, ':', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);

        $text1 = substr($this->alt['alamat_supplier'], 0,60);
        $text2 = substr($this->alt['alamat_supplier'], 60,108);

        $this->Cell(10, 0,$text1 , 0, 1, 'L');
        $this->Cell(3, 0.7,' ' , 0, 0, 'L');
        $this->Cell(10, 0.7,$text2, 0, 0, 'L');

        $this->SetLineWidth(0);
        $this->ln(0.8);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(1, 0.7, 'No.', 1, 0, 'C');
        $this->Cell(6.5, 0.7, 'Nama Obat', 1, 0, 'C');
        $this->Cell(1.5, 0.7, 'Satuan', 1, 0, 'C');
        $this->Cell(1.5, 0.7, 'Jumlah', 1, 0, 'C');
        $this->Cell(2.5, 0.7, 'Ket', 1, 0, 'C');
        // $this->ln(0.7);
        // $this->SetFont('Arial', '', 10);
    }

    public function Footer() {
        $this->SetY(-1);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 0.5, 'Halaman '.$this->PageNo().' dari {nb}', 0, 0, 'C');
    }
}

$kdorders = $_GET['id'];

$query = $db->prepare("SELECT * FROM orders WHERE kd_trbmasuk = ?");
$query->execute([$kdorders]);
$res = $query->fetch(PDO::FETCH_ASSOC);
$alamat = $db->prepare("select * from supplier where id_supplier=?");
$alamat->execute([$res['id_supplier']]);
$alt = $alamat->fetch(PDO::FETCH_ASSOC);
//ambil header
$ah = $db->prepare("SELECT * FROM setheader ");
$ah->execute();
$rh = $ah->fetch(PDO::FETCH_ASSOC);

$pdf = new OrdersPDF("P", "cm", "A5", $rh, $kdorders, $res, $alt);

$pdf->SetMargins(1, 0, 1);
$pdf->SetAutoPageBreak(true, 1.5);
$pdf->AliasNbPages();
$pdf->AddPage();

$no = 1;
$query1 = $db->prepare("SELECT * FROM ordersdetail WHERE kd_trbmasuk = ?");
$query1->execute([$kdorders]);

$pdf->ln(0.7);
$pdf->SetX(1);

while ($lihat = $query1->fetch(PDO::FETCH_ASSOC)) {
    $qty = ($lihat['qtygrosir_dtrbmasuk'] == "") ? $lihat['qty_dtrbmasuk'] : $lihat['qtygrosir_dtrbmasuk'];
    $satuan = ($lihat['satgrosir_dtrbmasuk'] == "") ? $lihat['sat_dtrbmasuk'] : $lihat['satgrosir_dtrbmasuk'];
    $namaObat = $lihat['nmbrg_dtrbmasuk'];
    if (strlen($namaObat) > 35) {
        $namaObat = wordwrap($namaObat, 35, "\n", false);
    }
    $jumlahBarisNama = substr_count($namaObat, "\n") + 1;
    $tinggiBaris = 0.7 * $jumlahBarisNama;

    // Cegah baris tabel terpotong/menimpa header saat pindah halaman.
    $batasBawahHalaman = $pdf->PageBreakTrigger;
    if ($pdf->GetY() + $tinggiBaris > $batasBawahHalaman) {
        $pdf->AddPage();
        $pdf->ln(0.7);
        $pdf->SetX(1);
    }

    $pdf->SetFont('Arial', '', 10);

    $xAwal = $pdf->GetX();
    $yAwal = $pdf->GetY();

    $pdf->Cell(1, $tinggiBaris, $no, 1, 0, 'C');
    $pdf->MultiCell(6.5, 0.7, $namaObat, 1, 'L');

    $pdf->SetXY($xAwal + 7.5, $yAwal);
    $pdf->Cell(1.5, $tinggiBaris, $satuan, 1, 0, 'C');
    $pdf->Cell(1.5, $tinggiBaris, $qty, 1, 0, 'C');
    $pdf->Cell(2.5, $tinggiBaris, terbilang($qty), 1, 0, 'C');

    $pdf->SetXY($xAwal, $yAwal + $tinggiBaris);
    $no++;
}

$pdf->ln(1.5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(5, 0, '', 0, 0, 'R');
$pdf->Cell(9, 0, $rh['tigabelas'].', ' . tgl_indo(date("Y-m-d")), 0, 1, 'C');

$pdf->ln(0.4);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(5, 0, '', 0, 0, 'R');
$pdf->Cell(9, 0, 'Apoteker Pemesan,', 0, 0, 'C');

$signaturePath = "../../images/".$rh['tandatangan'];
$signatureFile = __DIR__."/../../images/".$rh['tandatangan'];
if (($res['tandatangan']) == 'YA' && $rh['tandatangan'] != '') {
    $pdf->ln(0.3);
    $pdf->SetX(9);
    $pdf->Image($signaturePath, $pdf->GetX(), $pdf->GetY()-0.5, 4, 4);
}

$pdf->ln(2.5);
$pdf->SetFont('Arial', 'BU', 10);
$pdf->Cell(5, 0, '', 0, 0, 'R');
$pdf->Cell(9, 0,$rh['empat'],0, 0, 'C');

$pdf->ln(0.4);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(5, 0, '', 0, 0, 'R');
$pdf->Cell(9, 0,$rh['tujuh'], 0, 0, 'C');
$pdf->Output("order".$res['tgl_trbmasuk'], "I");
?>