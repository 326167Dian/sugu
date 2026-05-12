<?php
session_start();
include_once '../../../configurasi/koneksi.php';

if (empty($_SESSION['username']) && empty($_SESSION['passuser'])) {
    echo "<link href='../css/style.css' rel='stylesheet' type='text/css'>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
    exit;
}

$idBarang = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($idBarang <= 0) {
    echo "ID barang tidak valid";
    exit;
}

$qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
if ($qty < 1) {
    $qty = 1;
}
if ($qty > 500) {
    $qty = 500;
}

$stmt = $db->prepare("SELECT id_barang, kd_barang, nm_barang FROM barang WHERE id_barang = ? LIMIT 1");
$stmt->execute([$idBarang]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "Data barang tidak ditemukan";
    exit;
}

$kdBarang = trim((string)$item['kd_barang']);
$nmBarang = trim((string)$item['nm_barang']);
$barcodeText = urlencode($kdBarang);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Barcode <?= htmlspecialchars($kdBarang, ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        @page { size: A5 portrait; margin: 6mm; }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #fff;
        }
        .sheet {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start;
            gap: 2mm 2mm;
        }
        .barcode-wrap {
            width: 33mm;
            height: 15mm;
            padding: 0.4mm 0.4mm 0.2mm 0.4mm;
            box-sizing: border-box;
            text-align: center;
            overflow: hidden;
            background: #fff;
            border: 0;
        }
        .barcode-wrap img {
            width: 100%;
            height: 11mm;
            object-fit: contain;
            display: block;
        }
        .item-code {
            margin-top: 0.3mm;
            font-size: 3.2mm;
            line-height: 1;
            letter-spacing: 0.35mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: normal;
            color: #000;
        }
        .actions {
            margin-top: 6px;
            text-align: center;
        }
        @media print {
            .actions {
                display: none;
            }
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sheet">
        <?php for ($i = 0; $i < $qty; $i++) { ?>
        <div class="barcode-wrap">
            <img src="../../assets/barcode.php?text=<?= $barcodeText ?>&size=55&codetype=code128&print=false&sizefactor=1" alt="Barcode <?= htmlspecialchars($kdBarang, ENT_QUOTES, 'UTF-8') ?>">
            <div class="item-code"><?= htmlspecialchars($kdBarang, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <?php } ?>
    </div>

    <div class="actions">
        <span>Jumlah label: <?= (int)$qty ?></span>
        &nbsp;|&nbsp;
        <button type="button" onclick="window.print()">Print</button>
    </div>
</body>
</html>
