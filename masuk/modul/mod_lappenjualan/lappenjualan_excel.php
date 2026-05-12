<!DOCTYPE html>
<html>

<head>
    <title>Laporan Data Penjualan</title>
</head>

<body>
    <style type="text/css">
        body {
            font-family: sans-serif;
        }

        table {
            margin: 20px auto;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #3c3c3c;
            padding: 3px 8px;

        }

        a {
            background: blue;
            color: #fff;
            padding: 8px 10px;
            text-decoration: none;
            border-radius: 2px;
        }
    </style>

    <?php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_data_penjualan.xls");
    include_once '../../../configurasi/koneksi.php';
    include "../../../configurasi/fungsi_rupiah.php";

    
    ?>

    <CENTER>
        <h4>MySIFA LAPORAN PENJUALAN</h4>
    </CENTER>
    <br>

    <table border="1">
       
        <thead>
            <tr>
                <th style="text-align: center; ">No</th>
                <th style="text-align: center; ">Kode Barang</th>
                <th style="text-align: center; ">Nama Barang</th>
                <th style="text-align: center; ">Qty</th>
                <th style="text-align: center; ">Satuan</th>
                <th style="text-align: center; ">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $tgl_awal = $_GET['tgl_awal'];
            $tgl_akhir = $_GET['tgl_akhir'];
            $shift = $_GET['shift'];
             if ($_GET['shift']<4){
	            $shift = $_GET['shift'];}
                else {
                    $shift=("1,2,3");
                }
    
            $no = 1;
            $stmt = $db->prepare("SELECT *, 
                    SUM(trkasir_detail.qty_dtrkasir) as q30,
                    SUM(trkasir_detail.hrgttl_dtrkasir) as om30 FROM trkasir_detail
                    JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                    WHERE shift in ($shift) AND trkasir.tgl_trkasir BETWEEN ? AND ?
                    GROUP BY trkasir_detail.kd_barang");
            $stmt->execute([$tgl_awal, $tgl_akhir]);
                    
            
            while($value = $stmt->fetch(PDO::FETCH_ASSOC)):
                
            
            ?>
                    <tr>
                        <td style="text-align: center; "><?= $no; ?></td>
                        <td style="text-align: left; width: 150px;"><?= $value['kd_barang']; ?></td>
                        <td style="text-align: left; width: 300px"><?= $value['nmbrg_dtrkasir'] ?></td>
                        <td style="text-align: center; width: 80px;"><?= $value['q30'] ?></td>
                        <td style="text-align: center; width: 100px;"><?= $value['sat_dtrkasir'] ?></td>
                        <td style="text-align: right; width: 100px;"><?=format_rupiah($value['om30']) ?></td>
                    </tr>

            <?php
                    $no++;
                
            endwhile;
            
            ?>
        </tbody>
    </table>
    <?php
            $tgl_awal = $_GET['tgl_awal'];
            $tgl_akhir = $_GET['tgl_akhir'];
            $shift = $_GET['shift'];
             if ($_GET['shift']<4){
	            $shift = $_GET['shift'];}
                else {
                    $shift=("1,2,3");
                }
     $tamtot = $db->query("select * from carabayar ");
$no3 = 1;

$grandtotal = 0;   // <-- tambahkan ini

while ($tt = $tamtot->fetch(PDO::FETCH_ASSOC)){

    $tcb= $db->prepare( "
        SELECT id_trkasir, kd_trkasir, SUM(ttl_trkasir) as ttlskrg1
        FROM trkasir 
        WHERE shift in ($shift) 
        and tgl_trkasir BETWEEN ? AND ? 
        AND id_carabayar=?
    ");
    $tcb->execute([$tgl_awal, $tgl_akhir, $tt['id_carabayar']]);

    $tamtcb = $tcb->fetch(PDO::FETCH_ASSOC);
    $dtamtcb = format_rupiah($tamtcb['ttlskrg1']);

    echo "
        <p style='font-weight:bold;'>Pembayaran $tt[nm_carabayar] : Rp. $dtamtcb </p>
    ";

    $no3++;

    // jumlahkan grand total
    $grandtotal += $tamtcb['ttlskrg1'];
}

    $dtgrandtotal = format_rupiah($grandtotal);

    echo "
        <p style='font-weight:bold; font-size:24px;'>
            GRAND TOTAL PENJUALAN SHIFT $shift: Rp. $dtgrandtotal
        </p>
    ";

    ?>
</body>

</html>