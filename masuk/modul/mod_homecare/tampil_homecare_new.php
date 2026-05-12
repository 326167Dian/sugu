<?php
session_start();
include "../../../configurasi/koneksi.php";

$id_homecare = isset($_GET['id']) ? intval($_GET['id']) : 0;
$data = null;
$details = [];
$alert = '';

// Get setheader data
$tampil_setheader = $db->query("SELECT * FROM setheader ORDER BY id_setheader");
$rk = $tampil_setheader->fetch(PDO::FETCH_ASSOC);

if ($id_homecare > 0) {
    // Get HOMECARE main data
    $stmt = $db->prepare("SELECT h.*, p.nm_pelanggan, p.alamat_pelanggan, p.tlp_pelanggan 
        FROM homecare h
        LEFT JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan
        WHERE h.id_homecare = ?");
    $stmt->execute([$id_homecare]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($data) {
        // Get HOMECARE details
        $stmtDetail = $db->prepare("SELECT * FROM homecare_detail WHERE id_homecare = ? ORDER BY no_urut");
        $stmtDetail->execute([$id_homecare]);
        $details = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $alert = "Data Home Care tidak ditemukan.";
    }
} else {
    $alert = "Parameter id Home Care belum diisi.";
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Pharmacy Care</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            background: #f0f0f0;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 20mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .title-container {
            flex: 1;
        }

        .center-title {
            text-align: center;
            font-size: 18px;
            margin-bottom: 0;
            line-height: 1.5;
        }

        .logo-container {
            margin-left: 20px;
            flex-shrink: 0;
        }

        .logo {
            width: 2cm;
            height: 2cm;
            object-fit: contain;
            display: block;
        }

        .id-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .id-grid span {
            display: flex;
            align-items: center;
        }

        .input-inline {
            border: none;
            border-bottom: 1px solid #333;
            flex: 1;
            margin-left: 10px;
            font-family: inherit;
            font-size: inherit;
            outline: none;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .catatan-cell {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .no-print {
            text-align: center;
            margin-top: 20px;
        }

        .no-print button {
            padding: 10px 20px;
            cursor: pointer;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            margin: 0 5px;
        }

        @media print {
            body {
                background: white;
                padding: 1 cm;
                margin: 10mm;
            }
            .container {
                box-shadow: none;
                width: 100%;
                max-width: 100%;
                padding: 15mm;
                margin: 0;
            }
            .header-section {
                margin-bottom: 15px;
            }
            .center-title {
                font-size: 14px;
                line-height: 1.3;
            }
            .logo {
                width: 2 cm;
                height: 2 cm;
            }
            .id-grid {
                font-size: 11px;
                gap: 5px;
                margin-bottom: 10px;
            }
            .input-inline {
                border-bottom: none;
                font-size: 11px;
            }
            .data-table {
                margin-top: 10px;
                font-size: 10px;
            }
            .data-table th,
            .data-table td {
                padding: 4px 6px;
                font-size: 10px;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<?php if ($alert !== '') { ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px auto; max-width: 900px; border: 1px solid #f5c6cb; border-radius: 4px;">
        <?php echo e($alert); ?>
    </div>
<?php } ?>

<div class="container">
    <div class="header-section">
        <div class="title-container">
            <h2 class="center-title">DOKUMENTASI PELAYANAN KEFARMASIAN DI RUMAH<br>(HOME PHARMACY CARE)<?php echo !empty($rk['satu']) ? '<br>' . e($rk['satu']) : ''; ?></h2>
        </div>
        <?php if (!empty($rk['logo'])) { ?>
            <div class="logo-container">
                <img src="../../images/<?php echo e($rk['logo']); ?>" alt="Logo" class="logo">
            </div>
        <?php } ?>
    </div>

    <div class="id-grid">
        <span>Nama Pasien : <input type="text" class="input-inline" value="<?php echo e($data['nama_pasien'] ?? ''); ?>" readonly></span>
        <span>Umur : <input type="text" class="input-inline" value="<?php echo e($data['umur'] ?? ''); ?>" readonly></span>
        <span style="grid-column: span 2;">Alamat : <input type="text" class="input-inline" value="<?php echo e($data['alamat'] ?? ''); ?>" readonly></span>
        <span>No. Telepon : <input type="text" class="input-inline" value="<?php echo e($data['telp'] ?? ''); ?>" readonly></span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="50">No</th>
                <th width="150">Tgl. Kunjungan</th>
                <th>Catatan Apoteker</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (!empty($details)) {
                foreach ($details as $idx => $detail) {
                    ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $idx + 1; ?></td>
                        <td><?php echo e($detail['tgl_kunjungan']); ?></td>
                        <td class="catatan-cell"><?php echo e($detail['catatan_apoteker']); ?></td>
                    </tr>
                    <?php
                }
            } else {
                // Show 5 empty rows if no data
                for ($i = 1; $i <= 5; $i++) {
                    ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $i; ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>

    <div class="no-print">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>
</div>

</body>
</html>
