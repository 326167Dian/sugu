<?php
session_start();
include "../../../configurasi/koneksi.php";
require('../../assets/pdf/rpdf.php');
include "../../../configurasi/fungsi_indotgl.php";
include "../../../configurasi/fungsi_rupiah.php";

//ambil header
$ah = $db->prepare("SELECT * FROM setheader ");
$ah->execute();
$rh = $ah->fetch(PDO::FETCH_ASSOC);
$kd_trkasir= $_GET['kd_trkasir'];

$dt = $db->prepare("SELECT * FROM trkasir
                    WHERE trkasir.kd_trkasir='$_GET[kd_trkasir]'");
$dt->execute();
$r1 = $dt->fetch(PDO::FETCH_ASSOC);
$tgl = tgl_indo($r1['tgl_trkasir']);
$namapelanggan = $r1['nm_pelanggan'];

$jumlah = $db->prepare("SELECT * FROM trkasir_detail WHERE kd_trkasir='$_GET[kd_trkasir]'");
$jumlah->execute();
$jumlahdetail = $jumlah->rowCount();


$pdf = new RPDF("P","cm","A4");
$pdf->SetMargins(0,0.5,0);
$pdf->AliasNbPages();
$pdf->AddPage();

//$pdf->SetAutoPageBreak(true);
//$pdf->Image('../../images/logo_yasfi.png',1,1,1.5,1.4);
$myImage = "../../images/".$rh['logo'];
$pdf->Image($myImage, 1,1,1.5,1.4);
//HEADER 1
$pdf->SetLineWidth(0.1);
$pdf->Line(0.7, 3.5, 20.5, 3.5); //horisontal atas


$pdf->SetLineWidth(0.05);
$pdf->Line(0.7, 5.2, 20.5, 5.2); //judul tabel atas
$pdf->Line(0.7, 5.8, 20.5, 5.8); //judul tabel bawah
$pdf->Line(0.7, 9.5, 20.5, 9.5); //horisontal atas
$pdf->Line(0.7, 5.2, 0.7, 9.5); //vertikal 1
$pdf->Line(1.5, 5.2, 1.5, 9.5); //vertikal 2
$pdf->Line(8, 5.2, 8, 9.5); //vertikal 3
$pdf->Line(10.7, 5.2, 10.7, 9.5); //vertikal 4
$pdf->Line(9.3, 5.2, 9.3, 9.5); //vertikal 5
$pdf->Line(12.8, 5.2, 12.8, 9.5); //vertikal 6
$pdf->Line(14.5, 5.2, 14.5, 9.5); //vertikal 7
$pdf->Line(16.7, 5.2, 16.7, 9.5); //vertikal 8
$pdf->Line(18.5, 5.2, 18.5, 9.5); //vertikal 9
$pdf->Line(20.5, 5.2, 20.5, 9.5); //vertikal 10


$pdf->ln(0.1);
$pdf->SetX(0.5);
$pdf->SetFont('Arial','B',18);
$pdf->Cell(20.5,0.5,'I N V O I CE',0,1,'C');

$pdf->ln(0.1);
$pdf->SetX(3);
$pdf->SetFont('Arial','B',13);
$pdf->Cell(16.5,0.5,$rh['satu'],0,1,'L');

$pdf->ln(0.1);
$pdf->SetX(3);
$pdf->SetFont('Arial','',9);
$pdf->Cell(8,0.2,$rh['dua'],0,1,'L');

$pdf->ln(0.1);
$pdf->SetX(3);
$pdf->SetFont('Arial','',9);
$pdf->Cell(8,0.2,$rh['tiga'],0,1,'L');



$pdf->ln(0.4);
$pdf->SetX(0.6);
$pdf->SetFont('Arial','',9);
$pdf->Cell(2,0.1,"SIA",0,0,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(0.3,0.1,":",0,0,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(10,0.1,$rh['lima'],0,0,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(2, 0.2, 'No. Faktur', 0, 0, 'L');
$pdf->Cell(0.3, 0.2, ':', 0, 0, 'R');
$pdf->Cell(6, 0.2, $kd_trkasir, 0, 1, 'L');

$pdf->ln(0.2);
$pdf->SetX(0.6);
$pdf->SetFont('Arial','',9);
$pdf->Cell(2,0.1,"No. Telp/Wa",0,0,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(0.3,0.1,":",0,0,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(10,0.1,$rh['enam'],0,0,'L');
$pdf->Cell(2, 0.2, 'Tgl Faktur', 0, 0, 'L');
$pdf->Cell(0.3, 0.2, ':', 0, 0, 'R');
$pdf->Cell(6, 0.2, $tgl , 0, 1, 'L');


$pdf->ln(0.6);
$pdf->SetX(0.6);
$pdf->SetFont('Arial','',9);
$pdf->Cell(2,0.1,'Kepada Yth',0,1,'L');



$pdf->ln(0.2);
$pdf->SetX(0.6);
$pdf->SetFont('Arial','',9);
$pdf->Cell(20,0.2,$r1['nm_pelanggan'],0,1,'L');

$pdf->ln(0.2);
$pdf->SetX(0.6);
$pdf->SetFont('Arial','',9);
$pdf->Cell(20,0.2,$r1['alamat_pelanggan']. ' Telp : '. $r1['tlp_pelanggan'],0,1,'L');


$pdf->ln(0.2);
$pdf->SetX(0.6);
$pdf->SetFont('Arial','',9);
$pdf->Cell(3.3,0.2,'',0,1,'L');

$pdf->ln(0.1);
$pdf->SetX(0.6);
$pdf->SetFont('Arial','',9);
$pdf->Cell(1, 0.4, 'No.', 0, 0, 'C');
$pdf->Cell(6, 0.4, 'Nama Barang', 0, 0, 'C');
$pdf->Cell(2, 0.4, 'Jml', 0, 0, 'C');
$pdf->Cell(1, 0.4, 'Sat', 0, 0, 'C');
$pdf->Cell(2, 0.4, 'Batch', 0, 0, 'C');
$pdf->Cell(2, 0.4, 'Exp', 0, 0, 'C');
$pdf->Cell(2, 0.4, 'Harga', 0, 0, 'C');
$pdf->Cell(2, 0.4, 'Disc (%)', 0, 0, 'C');
$pdf->Cell(1.8, 0.4, 'Sub Total', 0, 1, 'C');

$pdf->ln(0.3);
$pdf->SetX(0.6);
$pdf->SetFont('Arial','',9);

$no=1;
$query = $db->prepare("SELECT * FROM trkasir_detail WHERE kd_trkasir='$_GET[kd_trkasir]'
	                    ORDER BY nmbrg_dtrkasir ASC");
$query->execute();

while($r2 = $query->fetch(PDO::FETCH_ASSOC)){
    $st[] = $r2['hrgttl_dtrkasir'];
    $gt = array_sum($st);
    $edt = date('m-Y', strtotime($r2['exp_date']));
    //$rr = $r2['disc'];
    // $disc = (($gt-$r1['ttl_tr5kasir'])/$gt)*100;
    // $wf = 1-($rr/100);
    // $ws = 1.11 * $wf;
    // $wp = format_rupiah($r2['hrgjual_dtrkasir']/$wf);
    // $wpdis[] = $r2['hrgjual_dtrkasir'];
    // $hrgdiskon = round(($r2['hrgjual_dtrkasir']/$wf)/$ws);
    // $diskon =format_rupiah(($r2['hrgjual_dtrkasir']/$wf)-$hrgdiskon);
    // $disc = format_rupiah(($r2['hrgjual_dtrkasir']/$wf) - ($r2['hrgttl_dtrkasir']/$r2['qty_dtrkasir']));
    // $tdisc[]=(($r2['hrgjual_dtrkasir']/$wf) - ($r2['hrgttl_dtrkasir']/$r2['qty_dtrkasir']))* $r2['qty_dtrkasir'];
    // $sblmdiskon[] = $disc * $r2['qty_dtrkasir'];
    // $ttlsbldisc = array_sum($sblmdiskon);
    // $ttdisc = array_sum($tdisc);

    //$tagihan = format_rupiah($r1['ttl_trkasir']);
    $subtotal = format_rupiah($gt);
    // $edt = substr($r2['exp'],0,-3);
    //$edt = date('m-Y', strtotime($r2['exp']));
    $pdf->SetX(0.6);

    $text1 = substr($r2['nmbrg_dtrkasir'], 0,37);
    $text2 = substr($r2['nmbrg_dtrkasir'], 37,72);
    $text3 = strlen($r2['nmbrg_dtrkasir']);

    if ($text3 >37){
        $pdf->Cell(1, 0.4, $no.'.', 0, 0, 'C');
        $pdf->Cell(6, 0.4, $text1, 0, 0, 'L');
        $pdf->Cell(2, 0.4, $r2['qty_dtrkasir'], 0, 0, 'R');
        $pdf->Cell(1, 0.4, $r2['sat_dtrkasir'], 0, 0, 'L');
        $pdf->Cell(2, 0.4, $r2['batch'], 0, 0, 'C');
        $pdf->Cell(2, 0.4,  $edt, 0, 0, 'C');
        $pdf->Cell(2, 0.4, $wp, 0, 0, 'R');
        $pdf->Cell(2, 0.4, $disc, 0, 0, 'C');
        $pdf->Cell(1.8, 0.4, format_rupiah($r2['hrgttl_dtrkasir']), 0, 1, 'R');

        $pdf->SetX(0.6);
        $pdf->Cell(1, 0.4, '', 0, 0, 'C');
        $pdf->Cell(6, 0.4, $text2, 0, 0, 'L');
        $pdf->Cell(2, 0.4, '', 0, 0, 'R');
        $pdf->Cell(1, 0.4, '', 0, 0, 'L');
        $pdf->Cell(2, 0.4, '', 0, 0, 'L');
        $pdf->Cell(2, 0.4, '', 0, 0, 'L');
        $pdf->Cell(2, 0.4, '', 0, 0, 'R');
        $pdf->Cell(2, 0.4, '', 0, 0, 'C');
        $pdf->Cell(1.8, 0.4, '', 0, 1, 'R');

    } else {
        $pdf->Cell(1, 0.4, $no.'.', 0, 0, 'C');
        $pdf->Cell(6, 0.4, $text1, 0, 0, 'L');
        $pdf->Cell(2, 0.4, $r2['qty_dtrkasir'], 0, 0, 'C');
        $pdf->Cell(1, 0.4, $r2['sat_dtrkasir'], 0, 0, 'C');
        $pdf->Cell(2, 0.4, $r2['no_batch'], 0, 0, 'C');
        $pdf->Cell(2, 0.4, $edt, 0, 0, 'C');
        $pdf->Cell(2, 0.4, format_rupiah($r2['hrgjual_dtrkasir']), 0, 0, 'R');
        $pdf->Cell(2, 0.4, $r2['disc'], 0, 0, 'C');
        $pdf->Cell(1.8, 0.4, format_rupiah($r2['hrgttl_dtrkasir']), 0, 1, 'R');


    }

    $no++;
}
// $twpdis = array_sum($wpdis);
// $sebelum1 = $ttdisc + $gt;
// $sebelum = format_rupiah($sebelum1);
// $garis = $garis + 0.4;
//$pdf->ln(0.1);
//$pdf->SetX(0.6);
//$pdf->SetFont('Arial','U');
//$pdf->Cell(20.5, 0.2, '.....................................................................................................................................................................................................................................................................', 0, 0, 'C');

// $lengthPetugas = strlen($r1['petugas']);
// $petugas1 = substr($r1['petugas'], 0,15);
// $petugas2 = substr($r1['petugas'], 15,30);

// $lengthTerbilang = strlen(terbilang($gt));
// $terbilang1 = substr(terbilang($gt), 0,66);
// $terbilang2 = substr(terbilang($gt), 66,130);

// $pdf->ln(0.4);
// $pdf->SetX(0.6);
// $pdf->Cell(10.5, 0.3, 'Transfer BSI no Rek 1089057920 an Badiah ', 0, 0, 'L');

//$pdf->ln(3.3);
$pdf->SetY(9.6);
$pdf->SetX(0.6);
// $pdf->SetFont('Arial','',9);
// $pdf->Cell(4, 0.3,'Hormat Kami', 0, 0, 'C');

// $pdf->SetFont('Arial','',9);
// $pdf->Cell(4, 0.3,'Penerima',0,0,'C');

// $pdf->SetFont('Arial','',9);
// $pdf->Cell(2.5, 0.3, 'Metode bayar', 0, 0, 'R');
// $pdf->Cell(0.3, 0.3, ':', 0, 0, 'R');
// $pdf->Cell(2.5, 0.3, $r1['nm_carabayar'], 0, 0, 'L');

// $pdf->SetFont('Arial','',9);
// $pdf->Cell(3, 0.3, 'Sub Total : ', 0, 0, 'R');
// $pdf->Cell(3, 0.3, $subtotal, 0, 1, 'R');
$pdf->SetFont('Arial','',9);
$pdf->Cell(8, 0.3, 'Terbilang :', 0, 0, 'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(9.8, 0.3, '', 0, 0, 'R');
$pdf->Cell(2.2, 0.3,  '', 0, 1, 'R');

$terbilang_text = trim(terbilang($gt));
$terbilang1 = substr($terbilang_text, 0, 66);
$terbilang2 = trim(substr($terbilang_text, 66, 130));

if(strlen($terbilang_text) > 66){
    $pdf->SetX(0.6);
    $pdf->SetFont('Arial','I',9);
    $pdf->Cell(8, 0.4, $terbilang1, 0, 0, 'L');

    $pdf->SetX(0.6);
    $pdf->SetFont('Arial','I',9);
    $pdf->Cell(8, 0.4, $terbilang2.' Rupiah.', 0, 0, 'L');

    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(9.8, 0.4, 'Harus Dibayar : ', 0, 0, 'R');
    $pdf->Cell(2.2, 0.4, $subtotal, 0, 1, 'R');

} else {
    $pdf->SetX(0.6);
    $pdf->SetFont('Arial','IB',11);
    $pdf->Cell(8, 0.4, $terbilang_text.' Rupiah.', 0, 0, 'L');

    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(9.8, 0.4, 'Total Pembayaran : ', 0, 0, 'R');
    $pdf->Cell(2, 0.4, $subtotal, 0, 1, 'R');

}
//$jatuhtempo = date('d-m-Y',strtotime($r1['jth_tempo']));

$pdf->ln(0.4);
$pdf->SetX(0.6);
$pdf->SetFont('Arial','',9);
$pdf->Cell(10, 0.4, 'Catatan/Ketentuan :','LTR', 0, 'C');
$pdf->Cell(5, 0.4, 'Penerima', 0, 0, 'C');
$pdf->Cell(5, 0.4, 'Apoteker', 0, 0, 'C');

$pdf->ln(0.4);
$pdf->SetX(0.6);
$pdf->SetFont('Arial','',9);
$pdf->Cell(2, 0.4, 'Pembayaran', 'L', 0, 'L');
$pdf->Cell(0.3, 0.4, ':', 0, 0, 'L');
$pdf->Cell(7.7, 0.4,'Cash On Delivery', 'R', 1, 'L');

$pdf->ln(0.1);
$pdf->SetX(0.6);
$pdf->SetFont('Arial','',9);
$pdf->Cell(2, 0.1, '', 'L', 0, 'L');
$pdf->Cell(0.3, 0.1, '', 0, 0, 'L');
$pdf->Cell(7.7, 0.1, '', 'R', 1, 'L');

$pdf->ln(0.1);
$pdf->SetX(0.6);
$pdf->SetFont('Arial','',9);
$pdf->Cell(10, 0.1, '', 'LBR', 0, 'L');
$pdf->Cell(10, 0.1,'', 0, 1, 'L');

$pdf->ln(0.1);
$pdf->SetX(0.6);
$pdf->SetFont('Arial','',9);
$pdf->Cell(10, 0.3, '', 0, 0, 'L');
$pdf->Cell(5, 0.3,'( ____________________ )', 0, 0, 'C');

$text1 = substr($rh['empat'], 0,31);

$pdf->SetFont('Arial','U',9);
$pdf->Cell(5, 0.3,$text1,'',1,'C');




$pdf->ln(0.1);
$pdf->SetX(0.6);
$pdf->SetFont('Arial','',7);
$pdf->Cell(15, 0.3, '', 0, 0, 'L');
$pdf->Cell(5, 0.3,$rh['tujuh'],0,1,'C');





$pdf->Output("Invoice_penjualan_","I");

?>


