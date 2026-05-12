<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pembelian Barang </title>
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
    header("Content-Disposition: attachment; filename=Laporan_Pembelian_Obat.xls");
    include_once '../../../configurasi/koneksi.php';
    include "../../../configurasi/fungsi_rupiah.php";
    $tgl_awal = $_GET['tgl_awal'];
    $tgl_akhir = $_GET['tgl_akhir'];
    $supplier = $_GET['supplier'];
    $query = $db->prepare("select * from trbmasuk where id_supplier=? and tgl_trbmasuk between ? and ? ");
    $query->execute([$supplier, $tgl_awal, $tgl_akhir]);
        $sup = $query->fetch(PDO::FETCH_ASSOC);
    ?>

    <CENTER>
        <h4>MySIFA LAPORAN BARANG MASUK <br> <?= $sup['nm_supplier'] ?> dari tanggal <?= $tgl_awal ?> hingga <?= $tgl_akhir ?> </h4>
    </CENTER>
    <br>

    <table border="1">

        <thead>
            <tr>
                <th>No</th>
                <th>tanggal Pembelian</th>
                <th>Kode Transaksi</th>
                <th>Status Pembayaran</th>
                <th>Nilai Faktur</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($value = $query->fetch(PDO::FETCH_ASSOC)):


                ?>
                <tr>
                    <td style="text-align: center; "><?= $no; ?></td>
                    <td style="text-align: left; width: 150px;"><?= $value['tgl_trbmasuk']; ?></td>
                    <td style="text-align: left; width: 300px"><?= $value['kd_trbmasuk'] ?></td>
                    <td style="text-align: center; width: 100px;"><?= $value['carabayar'] ?></td>
                    <td style="text-align: right; width: 100px;"><?= format_rupiah($value['ttl_trbmasuk']) ?></td>
                </tr>

                <?php
                $no++;

            endwhile;

            ?>
        </tbody>
    </table>
</body>

</html>