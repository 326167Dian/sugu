<?php
if (!isset($db)) {
    echo '<div class="section mt-2"><div class="alert alert-danger">Koneksi database tidak tersedia.</div></div>';
    return;
}

$module = isset($module) ? $module : 'home';
$mobileStatus = isset($_GET['status']) ? $_GET['status'] : '';
$mobileMessage = isset($_GET['msg']) ? $_GET['msg'] : '';
$mobileTrxCode = isset($_GET['trx']) ? trim($_GET['trx']) : '';
$mobilePaidFlag = isset($_GET['paid']) ? $_GET['paid'] : '';

if (!function_exists('mobileHasAccess')) {
    function mobileHasAccess($sessionKey)
    {
        return isset($_SESSION[$sessionKey]) && $_SESSION[$sessionKey] === 'Y';
    }
}

function mobileTableExists($db, $tableName)
{
    try {
        $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
        $stmt->execute(array($tableName));
        return (int)$stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        return false;
    }
}

function mobileColumnExists($db, $tableName, $columnName)
{
    try {
        $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
        $stmt->execute(array($tableName, $columnName));
        return (int)$stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        return false;
    }
}

function mobileScalar($db, $sql, $default = 0)
{
    try {
        $stmt = $db->query($sql);
        $value = $stmt->fetchColumn();
        if ($value === false || $value === null) {
            return $default;
        }
        return $value;
    } catch (Exception $e) {
        return $default;
    }
}

function mobileRupiah($angka)
{
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

$todayDate = date('Y-m-d');

$showHome = ($module === 'home');
$showKasir = ($module === 'kasir');
$showKeranjang = ($module === 'keranjang');
$showTransaksi = ($module === 'transaksi');
$showStok = ($module === 'stok');
$showProfil = ($module === 'profil');

if ($showHome) {
    $totalSwamedikasiHariIni = 0;
    $totalPelanggan = 0;
    $totalTransaksiHariIni = 0;
    $totalOmzetHariIni = 0;
    $totalBarangMacet = 0;
    $topProduk = array();
    ?>

    <div class="card mobile-hero-card">
        <div class="card-body">
            <div class="mobile-hello">Assalamu'alaikum,</div>
            <div class="mobile-user-name"><?php echo htmlspecialchars($_SESSION['namalengkap']); ?></div>
            <div class="mobile-id-chip">ID: <?php echo htmlspecialchars((string)$_SESSION['idadmin']); ?></div>
        </div>
    </div>

    <div class="mobile-grid">
        <div class="card mobile-stat-card">
            <div class="card-body">
                <span class="mobile-stat-icon"><ion-icon name="cash-outline"></ion-icon></span>
                <span class="mobile-stat-title">Omzet Hari Ini</span>
                <div id="mobile_home_omzet" class="mobile-stat-value"><?php echo mobileRupiah($totalOmzetHariIni); ?></div>
            </div>
        </div>

        <div class="card mobile-stat-card">
            <div class="card-body">
                <span class="mobile-stat-icon"><ion-icon name="receipt-outline"></ion-icon></span>
                <span class="mobile-stat-title">Transaksi Hari Ini</span>
                <div id="mobile_home_transaksi" class="mobile-stat-value"><?php echo number_format($totalTransaksiHariIni, 0, ',', '.'); ?></div>
            </div>
        </div>

        <div class="card mobile-stat-card">
            <div class="card-body">
                <span class="mobile-stat-icon"><ion-icon name="cube-outline"></ion-icon></span>
                <span class="mobile-stat-title">Total Swamedikasi Hari Ini</span>
                <div id="mobile_home_total_swamedikasi" class="mobile-stat-value"><?php echo number_format($totalSwamedikasiHariIni, 0, ',', '.'); ?></div>
            </div>
        </div>

        <div class="card mobile-stat-card">
            <div class="card-body">
                <span class="mobile-stat-icon"><ion-icon name="alert-circle-outline"></ion-icon></span>
                <span class="mobile-stat-title">Total Barang Macet</span>
                <div id="mobile_home_total_macet" class="mobile-stat-value"><?php echo number_format($totalBarangMacet, 0, ',', '.'); ?></div>
            </div>
        </div>
    </div>

    <div class="mobile-section-title">Aksi Cepat</div>
    <div class="mobile-menu-actions mb-3">
        <?php if (mobileHasAccess('tpk')) { ?>
            <a href="media_mobile.php?module=kasir" class="btn btn-primary">
                <ion-icon name="cart-outline"></ion-icon>&nbsp; Kasir Mobile
            </a>
        <?php } ?>
        <?php if (mobileHasAccess('orders')) { ?>
            <a href="media_admin.php?module=orders" class="btn btn-success">
                <ion-icon name="file-tray-full-outline"></ion-icon>&nbsp; Pesanan
            </a>
        <?php } ?>
        <?php if (mobileHasAccess('mbarang')) { ?>
            <a href="media_admin.php?module=barang" class="btn btn-secondary">
                <ion-icon name="albums-outline"></ion-icon>&nbsp; Data Barang
            </a>
        <?php } ?>
        <?php if (mobileHasAccess('mstok')) { ?>
            <a href="media_mobile.php?module=stok" class="btn btn-warning">
                <ion-icon name="stats-chart-outline"></ion-icon>&nbsp; Ringkasan Stok
            </a>
        <?php } ?>
    </div>

    <div class="mobile-section-title">Produk Terlaris Hari Ini</div>
    <div class="card mobile-list-card mb-3">
        <ul id="mobile_home_top_produk" class="listview flush transparent no-line image-listview">
            <?php if (count($topProduk) > 0) { ?>
                <?php foreach ($topProduk as $produk) { ?>
                    <li>
                        <div class="item">
                            <div class="icon-box bg-primary">
                                <ion-icon name="pricetag-outline"></ion-icon>
                            </div>
                            <div class="in">
                                <div>
                                    <?php echo htmlspecialchars($produk['nm_barang']); ?>
                                    <div class="text-muted" style="font-size:13px;">Terjual: <?php echo number_format((float)$produk['qty_total'], 0, ',', '.'); ?></div>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            <?php } else { ?>
                <li>
                    <div class="item">
                        <div class="in">
                            <div>Memuat data produk terlaris...</div>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>

    <div class="card mobile-list-card mb-3">
        <div class="card-body" style="padding:14px;">
            <strong>Total Pelanggan</strong>
            <div class="text-muted">Data real dari tabel pelanggan</div>
            <h2 id="mobile_home_total_pelanggan" style="margin-top:8px; font-weight:800; color:#1d4fd8;"><?php echo number_format($totalPelanggan, 0, ',', '.'); ?></h2>
        </div>
    </div>

    <script>
        (function () {
            function formatRupiah(value) {
                var number = Number(value || 0);
                return 'Rp ' + number.toLocaleString('id-ID');
            }

            function formatAngka(value) {
                var number = Number(value || 0);
                return number.toLocaleString('id-ID');
            }

            function setText(id, text) {
                var node = document.getElementById(id);
                if (node) {
                    node.textContent = text;
                }
            }

            function renderTopProduk(items) {
                var listNode = document.getElementById('mobile_home_top_produk');
                if (!listNode) {
                    return;
                }

                if (!items || !items.length) {
                    listNode.innerHTML = '<li><div class="item"><div class="in"><div>Belum ada data penjualan hari ini.</div></div></div></li>';
                    return;
                }

                var html = '';
                items.forEach(function (item) {
                    html += '<li>' +
                        '<div class="item">' +
                        '<div class="icon-box bg-primary"><ion-icon name="pricetag-outline"></ion-icon></div>' +
                        '<div class="in"><div>' +
                        String(item.nm_barang || '-') +
                        '<div class="text-muted" style="font-size:13px;">Terjual: ' + formatAngka(item.qty_total || 0) + '</div>' +
                        '</div></div></div></li>';
                });
                listNode.innerHTML = html;
            }

            fetch('mobile/home_dashboard_data.php', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(function (response) { return response.json(); })
            .then(function (json) {
                if (!json || json.status !== 'ok') {
                    throw new Error('invalid_response');
                }

                setText('mobile_home_omzet', formatRupiah(json.data.total_omzet_hari_ini || 0));
                setText('mobile_home_transaksi', formatAngka(json.data.total_transaksi_hari_ini || 0));
                setText('mobile_home_total_swamedikasi', formatAngka(json.data.total_swamedikasi_hari_ini || 0));
                setText('mobile_home_total_macet', formatAngka(json.data.total_barang_macet || 0));
                setText('mobile_home_total_pelanggan', formatAngka(json.data.total_pelanggan || 0));
                renderTopProduk(json.data.top_produk || []);
            })
            .catch(function () {
                renderTopProduk([]);
            });
        })();
    </script>

<?php }

if ($showKasir) {
    if (!mobileHasAccess('tpk')) {
        ?>
        <div class="mobile-section-title">Kasir Mobile</div>
        <div class="card mobile-list-card mb-3">
            <div class="card-body" style="padding:14px;">
                <div class="text-danger">Anda tidak memiliki hak akses ke modul Kasir.</div>
            </div>
        </div>
        <?php
    } else {
        $kasirHariIni = array('jumlah_transaksi' => 0, 'total_omzet' => 0);
        $transaksiAktif = null;
        $detailAktif = array();
        $riwayatKasir = array();
        $metodeBayar = array();
        $totalKeranjangAktif = 0;
        $totalBarisKeranjang = 0;
        $totalQtyKeranjang = 0;
        $shiftStatusText = 'Shift belum dibuka';
        $shiftRowAktif = null;

        try {
            if (mobileTableExists($db, 'trkasir')) {
                $stmtKasirHariIni = $db->prepare("SELECT COUNT(*) AS jumlah_transaksi, COALESCE(SUM(ttl_trkasir), 0) AS total_omzet FROM trkasir WHERE DATE(tgl_trkasir) = CURDATE() AND id_user = ?");
                $stmtKasirHariIni->execute(array($_SESSION['idadmin']));
                $rowKasirHariIni = $stmtKasirHariIni->fetch(PDO::FETCH_ASSOC);
                if ($rowKasirHariIni) {
                    $kasirHariIni = $rowKasirHariIni;
                }

                $stmtRiwayatKasir = $db->prepare("SELECT kd_trkasir, tgl_trkasir, ttl_trkasir FROM trkasir WHERE id_user = ? ORDER BY tgl_trkasir DESC LIMIT 10");
                $stmtRiwayatKasir->execute(array($_SESSION['idadmin']));
                $riwayatKasir = $stmtRiwayatKasir->fetchAll(PDO::FETCH_ASSOC);
            }

            if (mobileTableExists($db, 'waktukerja')) {
                $stmtShiftAktif = $db->prepare("SELECT * FROM waktukerja WHERE tanggal = ? AND status = 'ON' LIMIT 1");
                $stmtShiftAktif->execute(array(date('Y-m-d')));
                $shiftRowAktif = $stmtShiftAktif->fetch(PDO::FETCH_ASSOC);
                if ($shiftRowAktif) {
                    $labelShift = ((string)$shiftRowAktif['shift'] === '1') ? 'PAGI' : (((string)$shiftRowAktif['shift'] === '2') ? 'SORE' : $shiftRowAktif['shift']);
                    $shiftStatusText = 'Shift ' . $labelShift . ' sedang ON';
                }
            }

            if (mobileTableExists($db, 'kdtk')) {
                $stmtAktif = $db->prepare("SELECT kd_trkasir FROM kdtk WHERE id_admin = ? AND stt_kdtk = 'ON' ORDER BY id_kdtk DESC LIMIT 1");
                $stmtAktif->execute(array($_SESSION['idadmin']));
                $transaksiAktif = $stmtAktif->fetchColumn();
            }

            if (empty($transaksiAktif) && mobileTableExists($db, 'trkasir_detail') && mobileTableExists($db, 'trkasir')) {
                $stmtDraft = $db->prepare("SELECT d.kd_trkasir
                                           FROM trkasir_detail d
                                           LEFT JOIN trkasir t ON t.kd_trkasir = d.kd_trkasir
                                           WHERE t.kd_trkasir IS NULL
                                             AND (
                                                d.idadmin = ?
                                                OR EXISTS(
                                                    SELECT 1
                                                    FROM kdtk k
                                                    WHERE k.kd_trkasir = d.kd_trkasir
                                                      AND k.id_admin = ?
                                                )
                                             )
                                           ORDER BY d.id_dtrkasir DESC
                                           LIMIT 1");
                $stmtDraft->execute(array($_SESSION['idadmin'], $_SESSION['idadmin']));
                $draftKode = $stmtDraft->fetchColumn();
                if (!empty($draftKode)) {
                    $transaksiAktif = $draftKode;
                }
            }

            if (!empty($transaksiAktif) && mobileTableExists($db, 'trkasir_detail')) {
                $stmtDetail = $db->prepare("SELECT d.nmbrg_dtrkasir,
                                                   d.id_dtrkasir,
                                                   d.kd_barang,
                                                   d.sat_dtrkasir,
                                                   d.qty_dtrkasir,
                                                   d.hrgjual_dtrkasir,
                                                   d.hrgttl_dtrkasir,
                                                   COALESCE(b.nm_barang, d.nmbrg_dtrkasir) AS nm_barang_tampil
                                            FROM trkasir_detail d
                                            LEFT JOIN barang b ON b.id_barang = d.id_barang
                                            WHERE d.kd_trkasir = ?
                                            ORDER BY d.id_dtrkasir DESC
                                            LIMIT 100");
                $stmtDetail->execute(array($transaksiAktif));
                $detailAktif = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);

                foreach ($detailAktif as $line) {
                    $totalKeranjangAktif += (float)$line['hrgttl_dtrkasir'];
                    $totalQtyKeranjang += (float)$line['qty_dtrkasir'];
                }
                $totalBarisKeranjang = count($detailAktif);
            }

            if (mobileTableExists($db, 'carabayar')) {
                $stmtCarabayar = $db->query("SELECT id_carabayar, nm_carabayar FROM carabayar ORDER BY id_carabayar ASC");
                $metodeBayar = $stmtCarabayar->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            $riwayatKasir = array();
            $detailAktif = array();
            $metodeBayar = array();
        }
        ?>
        <div class="mobile-section-title">Kasir Mobile</div>

        <?php if ($mobileStatus === 'success' && $mobileMessage !== '') { ?>
            <div class="card mobile-list-card mb-3">
                <div class="card-body" style="padding:14px;">
                    <div class="text-success" style="font-weight:700;"><?php echo htmlspecialchars($mobileMessage); ?></div>
                </div>
            </div>
        <?php } ?>

        <?php if ($mobileStatus === 'success' && $mobilePaidFlag === '1' && $mobileTrxCode !== '') { ?>
            <div class="card mobile-list-card mb-3">
                <div class="card-body mobile-form-body">
                    <div class="text-success" style="font-weight:700; margin-bottom:8px;">Transaksi tersimpan ke tabel.</div>
                    <div class="text-muted" style="font-size:13px; margin-bottom:10px;">Kode transaksi: <?php echo htmlspecialchars($mobileTrxCode); ?></div>
                    <div class="mobile-menu-actions" style="margin:0;">
                        <a href="modul/mod_laporan/struk.php?kd_trkasir=<?php echo urlencode($mobileTrxCode); ?>" target="_blank" class="btn btn-success">
                            <ion-icon name="print-outline"></ion-icon>&nbsp; Cetak Struk
                        </a>
                        <a href="modul/mod_laporan/kwitansi.php?kd_trkasir=<?php echo urlencode($mobileTrxCode); ?>" target="_blank" class="btn btn-secondary">
                            <ion-icon name="receipt-outline"></ion-icon>&nbsp; Cetak Kwitansi
                        </a>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if ($mobileStatus === 'error' && $mobileMessage !== '') { ?>
            <div class="card mobile-list-card mb-3">
                <div class="card-body" style="padding:14px;">
                    <div class="text-danger" style="font-weight:700;"><?php echo htmlspecialchars($mobileMessage); ?></div>
                </div>
            </div>
        <?php } ?>

        <div class="mobile-grid">
            <div class="card mobile-stat-card">
                <div class="card-body">
                    <span class="mobile-stat-icon"><ion-icon name="receipt-outline"></ion-icon></span>
                    <span class="mobile-stat-title">Transaksi Saya Hari Ini</span>
                    <div class="mobile-stat-value"><?php echo number_format((float)$kasirHariIni['jumlah_transaksi'], 0, ',', '.'); ?></div>
                </div>
            </div>
            <div class="card mobile-stat-card">
                <div class="card-body">
                    <span class="mobile-stat-icon"><ion-icon name="cash-outline"></ion-icon></span>
                    <span class="mobile-stat-title">Omzet Saya Hari Ini</span>
                    <div class="mobile-stat-value" style="font-size:20px;"><?php echo mobileRupiah($kasirHariIni['total_omzet']); ?></div>
                </div>
            </div>
        </div>

        <div class="card mobile-list-card mb-3" style="margin-top:12px;">
            <div class="card-body" style="padding:14px;">
                <div class="text-muted">Kode Transaksi Aktif</div>
                <h3 id="mobile_active_kd" style="margin:6px 0 0; font-weight:800;"><?php echo !empty($transaksiAktif) ? htmlspecialchars($transaksiAktif) : 'Belum ada transaksi aktif'; ?></h3>
                <?php if (empty($transaksiAktif)) { ?>
                    <div id="mobile_draft_hint" class="text-muted" style="margin-top:6px; font-size:13px;">Kode transaksi akan dibuat otomatis saat input barcode pertama.</div>
                <?php } ?>
                <?php if (!empty($transaksiAktif)) { ?>
                    <div id="mobile_draft_summary" class="text-muted" style="margin-top:6px; font-size:13px;">
                        Draft keranjang: <?php echo number_format($totalBarisKeranjang, 0, ',', '.'); ?> baris item,
                        total qty <?php echo number_format($totalQtyKeranjang, 0, ',', '.'); ?>.
                    </div>
                <?php } else { ?>
                    <div id="mobile_draft_summary" class="text-muted" style="display:none; margin-top:6px; font-size:13px;"></div>
                <?php } ?>
            </div>
        </div>

        <?php if (mobileHasAccess('shiftkerja')) { ?>
            <div class="mobile-section-title">Shift Kasir</div>
            <div class="card mobile-list-card mb-3">
                <div class="card-body mobile-form-body">
                    <div class="text-muted">Status Hari Ini</div>
                    <h3 style="margin:6px 0 10px; font-weight:800;"><?php echo htmlspecialchars($shiftStatusText); ?></h3>
                    <?php if ($shiftRowAktif) { ?>
                        <div class="text-muted" style="font-size:13px; margin-bottom:10px;">
                            Buka: <?php echo htmlspecialchars($shiftRowAktif['waktubuka']); ?>
                            | Saldo Awal: <?php echo mobileRupiah($shiftRowAktif['saldoawal']); ?>
                        </div>

                        <form method="post" action="mobile/kasir_mobile_action.php">
                            <input type="hidden" name="mobile_action" value="close_shift">
                            <input type="hidden" name="return_module" value="kasir">
                            <div class="form-group mb-2">
                                <label for="mobile_saldoakhir_shift" class="mobile-form-label">Saldo Akhir</label>
                                <input id="mobile_saldoakhir_shift" type="text" name="saldoakhir" class="form-control mobile-form-input" placeholder="Contoh: 150000" required>
                            </div>
                            <button type="submit" class="btn btn-danger btn-block mobile-submit-btn">
                                <ion-icon name="lock-closed-outline"></ion-icon>
                                Tutup Kasir
                            </button>
                        </form>
                    <?php } else { ?>
                        <form method="post" action="mobile/kasir_mobile_action.php">
                            <input type="hidden" name="mobile_action" value="open_shift">
                            <input type="hidden" name="return_module" value="kasir">

                            <div class="form-group mb-2">
                                <label for="mobile_shift_pilih" class="mobile-form-label">Pilih Shift</label>
                                <select id="mobile_shift_pilih" name="shift" class="form-control mobile-form-input" required>
                                    <option value="1">SHIFT PAGI</option>
                                    <option value="2">SHIFT SORE</option>
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label for="mobile_saldoawal_shift" class="mobile-form-label">Saldo Awal</label>
                                <input id="mobile_saldoawal_shift" type="text" name="saldoawal" class="form-control mobile-form-input" placeholder="Contoh: 100000" required>
                            </div>
                            <button type="submit" class="btn btn-success btn-block mobile-submit-btn">
                                <ion-icon name="lock-open-outline"></ion-icon>
                                Open Kasir
                            </button>
                        </form>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <div class="mobile-section-title">Input Barang</div>
        <div class="card mobile-list-card mb-3">
            <div class="card-body mobile-form-body">
                <div id="mobile_add_item_feedback" style="display:none; margin-bottom:10px;"></div>
                <form id="mobile_add_item_form" method="post" action="mobile/kasir_mobile_action.php">
                    <input type="hidden" name="mobile_action" value="add_item">
                    <input type="hidden" name="return_module" value="kasir">
                    <input type="hidden" name="barcode" id="mobile_selected_barcode" value="">
                    <div class="form-group mb-2">
                        <label for="mobile_nama_barang_input" class="mobile-form-label">Nama Barang (autocomplete)</label>
                        <input id="mobile_nama_barang_input" type="text" class="form-control mobile-form-input" placeholder="Ketik minimal 2 huruf nama barang" autocomplete="off" required>
                        <div id="mobile_barang_suggestion" class="mobile-suggestion-list"></div>
                    </div>
                    <div class="form-group mb-2">
                        <label for="mobile_kode_barang_view" class="mobile-form-label">Kode Barang</label>
                        <input id="mobile_kode_barang_view" type="text" class="form-control mobile-form-input" readonly>
                    </div>
                    <div class="form-group mb-2">
                        <label for="mobile_satuan_view" class="mobile-form-label">Satuan</label>
                        <input id="mobile_satuan_view" type="text" class="form-control mobile-form-input" readonly>
                    </div>
                    <div class="form-group mb-2">
                        <label for="mobile_harga_view" class="mobile-form-label">Harga Jual</label>
                        <input id="mobile_harga_view" type="text" class="form-control mobile-form-input" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label for="mobile_qty_input" class="mobile-form-label">Qty</label>
                        <input id="mobile_qty_input" type="number" name="qty" class="form-control mobile-form-input" min="1" step="1" value="1" required>
                    </div>
                    <button id="mobile_add_item_button" type="submit" class="btn btn-primary btn-block mobile-submit-btn">
                        <ion-icon name="add-circle-outline"></ion-icon>
                        Tambah Ke Keranjang
                    </button>
                </form>
            </div>
        </div>
        <script>
            (function () {
                var namaInput = document.getElementById('mobile_nama_barang_input');
                var suggestionBox = document.getElementById('mobile_barang_suggestion');
                var barcodeInput = document.getElementById('mobile_selected_barcode');
                var kodeView = document.getElementById('mobile_kode_barang_view');
                var satuanView = document.getElementById('mobile_satuan_view');
                var hargaView = document.getElementById('mobile_harga_view');
                var qtyInput = document.getElementById('mobile_qty_input');
                var form = document.getElementById('mobile_add_item_form');
                var submitButton = document.getElementById('mobile_add_item_button');
                var feedbackNode = document.getElementById('mobile_add_item_feedback');
                // Elemen-elemen ini berada di bawah tag <script> sehingga harus di-lookup secara lazy
                function getDetailList() { return document.getElementById('mobile_detail_list'); }
                function getTotalKeranjangNode() { return document.getElementById('mobile_total_keranjang'); }
                function getKodeTransaksiNode() { return document.getElementById('mobile_active_kd'); }
                function getDraftHintNode() { return document.getElementById('mobile_draft_hint'); }
                function getDraftSummaryNode() { return document.getElementById('mobile_draft_summary'); }
                var debounceTimer = null;
                var currentItems = [];

                function formatRupiahMobile(value) {
                    var number = Number(value || 0);
                    return 'Rp ' + number.toLocaleString('id-ID');
                }

                function clearSelection() {
                    barcodeInput.value = '';
                    kodeView.value = '';
                    satuanView.value = '';
                    hargaView.value = '';
                }

                function escapeHtml(value) {
                    return String(value || '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                }

                function formatAngka(value) {
                    var number = Number(value || 0);
                    return number.toLocaleString('id-ID');
                }

                function showFeedback(message, isSuccess) {
                    if (!feedbackNode) {
                        return;
                    }

                    feedbackNode.style.display = 'block';
                    feedbackNode.className = isSuccess ? 'text-success' : 'text-danger';
                    feedbackNode.style.fontWeight = '700';
                    feedbackNode.textContent = message;
                    feedbackNode.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }

                function buildDetailItemHtml(item) {
                    var idRow = Number(item.id_dtrkasir || 0);
                    var namaTampil = item.nm_barang_tampil || item.nm_barang || '-';
                    var satTampil = item.sat_dtrkasir || item.sat_barang || '-';
                    var qtyTampil = item.qty_dtrkasir || item.qty || 0;
                    var hargaTampil = item.hrgjual_dtrkasir || item.harga || 0;
                    var subtotalTampil = item.hrgttl_dtrkasir || item.subtotal || 0;

                    return '<li data-row-id="' + String(idRow) + '">' +
                        '<div class="item">' +
                        '<div class="icon-box bg-success"><ion-icon name="bag-check-outline"></ion-icon></div>' +
                        '<div class="in">' +
                        '<div>' +
                        escapeHtml(namaTampil) +
                        '<div class="text-muted" style="font-size:13px;">' +
                        'Kode: ' + escapeHtml(item.kd_barang || '-') +
                        ' | Sat: ' + escapeHtml(satTampil) +
                        '</div>' +
                        '<div class="text-muted" style="font-size:13px;">' +
                        'Qty: ' + formatAngka(qtyTampil) +
                        ' | Harga: ' + formatRupiahMobile(hargaTampil) +
                        ' | Subtotal: ' + formatRupiahMobile(subtotalTampil) +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</li>';
                }

                function refreshCartUiFromAjax(data) {
                    if (!data || !data.cart || !data.item) {
                        return;
                    }

                    var kodeTransaksiNode = getKodeTransaksiNode();
                    var draftHintNode = getDraftHintNode();
                    var draftSummaryNode = getDraftSummaryNode();
                    var totalKeranjangNode = getTotalKeranjangNode();
                    var detailList = getDetailList();

                    if (kodeTransaksiNode && data.cart.kd_trkasir) {
                        kodeTransaksiNode.textContent = data.cart.kd_trkasir;
                    }

                    if (draftHintNode) {
                        draftHintNode.style.display = 'none';
                    }

                    if (draftSummaryNode) {
                        draftSummaryNode.style.display = 'block';
                        draftSummaryNode.textContent = 'Draft keranjang: ' + formatAngka(data.cart.total_baris || 0) + ' baris item, total qty ' + formatAngka(data.cart.total_qty || 0) + '.';
                    }

                    if (totalKeranjangNode) {
                        totalKeranjangNode.textContent = 'Total Keranjang Aktif: ' + formatRupiahMobile(data.cart.total_harga || 0);
                    }

                    if (!detailList || !data.cart) {
                        return;
                    }

                    var allRows = Array.isArray(data.cart.details) ? data.cart.details : [];
                    if (allRows.length > 0) {
                        var html = '';
                        allRows.forEach(function (row) {
                            html += buildDetailItemHtml(row);
                        });
                        detailList.innerHTML = html;
                    } else {
                        detailList.innerHTML = '<li data-empty="1"><div class="item"><div class="in">Belum ada item pada transaksi aktif.</div></div></li>';
                    }
                }

                function renderSuggestions(items) {
                    currentItems = items || [];
                    suggestionBox.innerHTML = '';
                    if (!currentItems.length) {
                        suggestionBox.style.display = 'none';
                        return;
                    }

                    currentItems.forEach(function (item, index) {
                        var row = document.createElement('div');
                        row.className = 'mobile-suggestion-item';
                        row.setAttribute('data-index', String(index));
                        row.innerHTML = '<strong>' + item.nm_barang + '</strong><span>' + item.kd_barang + ' | Stok: ' + item.stok_barang + ' | ' + item.sat_barang + '</span>';
                        suggestionBox.appendChild(row);
                    });
                    suggestionBox.style.display = 'block';
                }

                function chooseItem(item) {
                    namaInput.value = item.nm_barang;
                    barcodeInput.value = item.kd_barang;
                    kodeView.value = item.kd_barang;
                    satuanView.value = item.sat_barang || '';
                    hargaView.value = formatRupiahMobile(item.harga_jual || 0);
                    qtyInput.value = 1;
                    suggestionBox.style.display = 'none';
                }

                function fetchSuggestions(keyword) {
                    fetch('mobile/barang_autocomplete_mobile.php?q=' + encodeURIComponent(keyword), {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    }).then(function (response) {
                        return response.json();
                    }).then(function (json) {
                        if (!json || json.status !== 'ok') {
                            renderSuggestions([]);
                            return;
                        }
                        renderSuggestions(json.items || []);
                    }).catch(function () {
                        renderSuggestions([]);
                    });
                }

                namaInput.addEventListener('input', function () {
                    var keyword = namaInput.value.trim();
                    clearSelection();

                    if (debounceTimer) {
                        clearTimeout(debounceTimer);
                    }

                    if (keyword.length < 2) {
                        renderSuggestions([]);
                        return;
                    }

                    debounceTimer = setTimeout(function () {
                        fetchSuggestions(keyword);
                    }, 250);
                });

                suggestionBox.addEventListener('click', function (event) {
                    var target = event.target;
                    while (target && !target.classList.contains('mobile-suggestion-item')) {
                        target = target.parentElement;
                    }

                    if (!target) {
                        return;
                    }

                    var idx = Number(target.getAttribute('data-index'));
                    if (Number.isNaN(idx) || !currentItems[idx]) {
                        return;
                    }

                    chooseItem(currentItems[idx]);
                });

                document.addEventListener('click', function (event) {
                    if (!suggestionBox.contains(event.target) && event.target !== namaInput) {
                        suggestionBox.style.display = 'none';
                    }
                });

                form.addEventListener('submit', function (event) {
                    if (!barcodeInput.value) {
                        event.preventDefault();
                        alert('Pilih barang dari daftar autocomplete terlebih dahulu.');
                        return false;
                    }

                    event.preventDefault();

                    submitButton.disabled = true;
                    var originalText = submitButton.innerHTML;
                    submitButton.innerHTML = '<ion-icon name="hourglass-outline"></ion-icon> Menyimpan...';

                    var formData = new FormData(form);
                    formData.append('ajax', '1');

                    fetch(form.getAttribute('action'), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    }).then(function (response) {
                        return response.json();
                    }).then(function (json) {
                        if (!json || json.status !== 'success') {
                            showFeedback((json && json.message) ? json.message : 'Gagal menambah barang ke keranjang.', false);
                            return;
                        }

                        showFeedback(json.message || 'Barang berhasil ditambahkan ke keranjang aktif.', true);
                        refreshCartUiFromAjax(json);
                        clearSelection();
                        namaInput.value = '';
                        qtyInput.value = 1;
                        suggestionBox.style.display = 'none';
                        namaInput.focus();
                    }).catch(function () {
                        showFeedback('Terjadi kendala jaringan. Coba lagi.', false);
                    }).finally(function () {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    });

                    return false;
                });
            })();
        </script>

        <div class="mobile-section-title">Detail Keranjang Aktif</div>
        <div class="card mobile-list-card mb-3">
            <ul id="mobile_detail_list" class="listview flush transparent no-line image-listview">
                <?php if (count($detailAktif) > 0) { ?>
                    <?php foreach ($detailAktif as $detail) { ?>
                        <li data-row-id="<?php echo (int)$detail['id_dtrkasir']; ?>">
                            <div class="item">
                                <div class="icon-box bg-success">
                                    <ion-icon name="bag-check-outline"></ion-icon>
                                </div>
                                <div class="in">
                                    <div>
                                        <?php echo htmlspecialchars($detail['nm_barang_tampil']); ?>
                                        <div class="text-muted" style="font-size:13px;">
                                            Kode: <?php echo htmlspecialchars($detail['kd_barang']); ?>
                                            | Sat: <?php echo htmlspecialchars($detail['sat_dtrkasir']); ?>
                                        </div>
                                        <div class="text-muted" style="font-size:13px;">
                                            Qty: <?php echo number_format((float)$detail['qty_dtrkasir'], 0, ',', '.'); ?>
                                            | Harga: <?php echo mobileRupiah($detail['hrgjual_dtrkasir']); ?>
                                            | Subtotal: <?php echo mobileRupiah($detail['hrgttl_dtrkasir']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                <?php } else { ?>
                    <li data-empty="1">
                        <div class="item">
                            <div class="in">Belum ada item pada transaksi aktif.</div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <div class="mobile-section-title" id="payment">Proses Pembayaran</div>
        <div class="card mobile-list-card mb-3">
            <div class="card-body mobile-form-body">
                <div id="mobile_total_keranjang" class="mobile-payment-total">Total Keranjang Aktif: <?php echo mobileRupiah($totalKeranjangAktif); ?></div>
                <form method="post" action="mobile/kasir_mobile_action.php">
                    <input type="hidden" name="mobile_action" value="process_payment">
                    <input type="hidden" name="return_module" value="kasir">

                    <div class="form-group mb-2">
                        <label for="mobile_bayar_input" class="mobile-form-label">Jumlah Bayar</label>
                        <input id="mobile_bayar_input" type="text" name="jumlah_bayar" class="form-control mobile-form-input" placeholder="Contoh: 50000" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="mobile_carabayar_select" class="mobile-form-label">Metode Pembayaran</label>
                        <select id="mobile_carabayar_select" name="id_carabayar" class="form-control mobile-form-input" required>
                            <option value="">Pilih metode</option>
                            <?php foreach ($metodeBayar as $cb) { ?>
                                <option value="<?php echo (int)$cb['id_carabayar']; ?>"><?php echo htmlspecialchars($cb['nm_carabayar']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success btn-block mobile-submit-btn" <?php echo $totalKeranjangAktif <= 0 ? 'disabled' : ''; ?>>
                        <ion-icon name="cash-outline"></ion-icon>
                        Proses Pembayaran
                    </button>
                </form>
            </div>
        </div>

        <div class="mobile-section-title">Riwayat Transaksi Saya</div>
        <div class="card mobile-list-card mb-3">
            <ul class="listview flush transparent no-line image-listview">
                <?php if (count($riwayatKasir) > 0) { ?>
                    <?php foreach ($riwayatKasir as $trx) { ?>
                        <li>
                            <div class="item">
                                <div class="icon-box bg-primary">
                                    <ion-icon name="receipt-outline"></ion-icon>
                                </div>
                                <div class="in">
                                    <div>
                                        <?php echo htmlspecialchars($trx['kd_trkasir']); ?>
                                        <div class="text-muted" style="font-size:13px;">
                                            <?php echo date('d-m-Y H:i', strtotime($trx['tgl_trkasir'])); ?>
                                            | <?php echo mobileRupiah($trx['ttl_trkasir']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                <?php } else { ?>
                    <li>
                        <div class="item">
                            <div class="in">Belum ada riwayat transaksi.</div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <?php
    }
}

if ($showKeranjang) {
    if (!mobileHasAccess('tpk')) {
        ?>
        <div class="mobile-section-title">Keranjang Aktif</div>

        <?php if ($mobileStatus === 'success' && $mobileMessage !== '') { ?>
            <div class="card mobile-list-card mb-3">
                <div class="card-body" style="padding:14px;">
                    <div class="text-success" style="font-weight:700;"><?php echo htmlspecialchars($mobileMessage); ?></div>
                </div>
            </div>
        <?php } ?>

        <?php if ($mobileStatus === 'error' && $mobileMessage !== '') { ?>
            <div class="card mobile-list-card mb-3">
                <div class="card-body" style="padding:14px;">
                    <div class="text-danger" style="font-weight:700;"><?php echo htmlspecialchars($mobileMessage); ?></div>
                </div>
            </div>
        <?php } ?>

        <?php if ($mobileStatus === 'success' && $mobilePaidFlag === '1' && $mobileTrxCode !== '') { ?>
            <div class="card mobile-list-card mb-3">
                <div class="card-body mobile-form-body">
                    <div class="text-success" style="font-weight:700; margin-bottom:8px;">Transaksi tersimpan ke tabel.</div>
                    <div class="text-muted" style="font-size:13px; margin-bottom:10px;">Kode transaksi: <?php echo htmlspecialchars($mobileTrxCode); ?></div>
                    <div class="mobile-menu-actions" style="margin:0;">
                        <a href="modul/mod_laporan/struk.php?kd_trkasir=<?php echo urlencode($mobileTrxCode); ?>" target="_blank" class="btn btn-success">
                            <ion-icon name="print-outline"></ion-icon>&nbsp; Cetak Struk
                        </a>
                        <a href="modul/mod_laporan/kwitansi.php?kd_trkasir=<?php echo urlencode($mobileTrxCode); ?>" target="_blank" class="btn btn-secondary">
                            <ion-icon name="receipt-outline"></ion-icon>&nbsp; Cetak Kwitansi
                        </a>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="card mobile-list-card mb-3">
            <div class="card-body" style="padding:14px;">
                <div class="text-danger">Anda tidak memiliki hak akses ke menu Keranjang.</div>
            </div>
        </div>
        <?php
    } else {
        $transaksiAktif = null;
        $detailAktif = array();
        $totalKeranjangAktif = 0;
        $totalBarisKeranjang = 0;
        $totalQtyKeranjang = 0;
        $metodeBayar = array();

        try {
            if (mobileTableExists($db, 'kdtk')) {
                $stmtAktif = $db->prepare("SELECT kd_trkasir FROM kdtk WHERE id_admin = ? AND stt_kdtk = 'ON' ORDER BY id_kdtk DESC LIMIT 1");
                $stmtAktif->execute(array($_SESSION['idadmin']));
                $transaksiAktif = $stmtAktif->fetchColumn();
            }

            if (empty($transaksiAktif) && mobileTableExists($db, 'trkasir_detail') && mobileTableExists($db, 'trkasir')) {
                $stmtDraft = $db->prepare("SELECT d.kd_trkasir
                                           FROM trkasir_detail d
                                           LEFT JOIN trkasir t ON t.kd_trkasir = d.kd_trkasir
                                           WHERE t.kd_trkasir IS NULL
                                             AND (
                                                d.idadmin = ?
                                                OR EXISTS(
                                                    SELECT 1
                                                    FROM kdtk k
                                                    WHERE k.kd_trkasir = d.kd_trkasir
                                                      AND k.id_admin = ?
                                                )
                                             )
                                           ORDER BY d.id_dtrkasir DESC
                                           LIMIT 1");
                $stmtDraft->execute(array($_SESSION['idadmin'], $_SESSION['idadmin']));
                $draftKode = $stmtDraft->fetchColumn();
                if (!empty($draftKode)) {
                    $transaksiAktif = $draftKode;
                }
            }

            if (!empty($transaksiAktif) && mobileTableExists($db, 'trkasir_detail')) {
                $stmtDetail = $db->prepare("SELECT d.nmbrg_dtrkasir,
                                                   d.kd_barang,
                                                   d.sat_dtrkasir,
                                                   d.qty_dtrkasir,
                                                   d.hrgjual_dtrkasir,
                                                   d.hrgttl_dtrkasir,
                                                   COALESCE(b.nm_barang, d.nmbrg_dtrkasir) AS nm_barang_tampil
                                            FROM trkasir_detail d
                                            LEFT JOIN barang b ON b.id_barang = d.id_barang
                                            WHERE d.kd_trkasir = ?
                                            ORDER BY d.id_dtrkasir DESC
                                            LIMIT 100");
                $stmtDetail->execute(array($transaksiAktif));
                $detailAktif = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);

                foreach ($detailAktif as $line) {
                    $totalKeranjangAktif += (float)$line['hrgttl_dtrkasir'];
                    $totalQtyKeranjang += (float)$line['qty_dtrkasir'];
                }
                $totalBarisKeranjang = count($detailAktif);
            }

            if (mobileTableExists($db, 'carabayar')) {
                $stmtCarabayar = $db->query("SELECT id_carabayar, nm_carabayar FROM carabayar ORDER BY id_carabayar ASC");
                $metodeBayar = $stmtCarabayar->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            $detailAktif = array();
            $metodeBayar = array();
        }
        ?>
        <div class="mobile-section-title">Keranjang Aktif</div>
        <div class="card mobile-list-card mb-3">
            <div class="card-body" style="padding:14px;">
                <div class="text-muted">Kode Draft</div>
                <h3 style="margin:6px 0 0; font-weight:800;"><?php echo !empty($transaksiAktif) ? htmlspecialchars($transaksiAktif) : 'Belum ada draft'; ?></h3>
                <div class="text-muted" style="margin-top:6px; font-size:13px;">
                    <?php echo number_format($totalBarisKeranjang, 0, ',', '.'); ?> baris item,
                    qty <?php echo number_format($totalQtyKeranjang, 0, ',', '.'); ?>,
                    total <?php echo mobileRupiah($totalKeranjangAktif); ?>
                </div>
            </div>
        </div>

        <div class="card mobile-list-card mb-3">
            <ul class="listview flush transparent no-line image-listview">
                <?php if (count($detailAktif) > 0) { ?>
                    <?php foreach ($detailAktif as $detail) { ?>
                        <li>
                            <div class="item">
                                <div class="icon-box bg-success">
                                    <ion-icon name="basket-outline"></ion-icon>
                                </div>
                                <div class="in">
                                    <div>
                                        <?php echo htmlspecialchars($detail['nm_barang_tampil']); ?>
                                        <div class="text-muted" style="font-size:13px;">
                                            Kode: <?php echo htmlspecialchars($detail['kd_barang']); ?>
                                            | Sat: <?php echo htmlspecialchars($detail['sat_dtrkasir']); ?>
                                        </div>
                                        <div class="text-muted" style="font-size:13px;">
                                            Qty: <?php echo number_format((float)$detail['qty_dtrkasir'], 0, ',', '.'); ?>
                                            | Harga: <?php echo mobileRupiah($detail['hrgjual_dtrkasir']); ?>
                                            | Subtotal: <?php echo mobileRupiah($detail['hrgttl_dtrkasir']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                <?php } else { ?>
                    <li>
                        <div class="item">
                            <div class="in">Belum ada item simpan_detail. Tambahkan item di menu Kasir.</div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <div class="mobile-menu-actions mb-3">
            <a href="media_mobile.php?module=kasir" class="btn btn-primary">
                <ion-icon name="cart-outline"></ion-icon>&nbsp; Kembali ke Kasir
            </a>
            <a href="#payment-keranjang" class="btn btn-success">
                <ion-icon name="cash-outline"></ion-icon>&nbsp; Proses Pembayaran
            </a>
        </div>

        <div class="mobile-section-title" id="payment-keranjang">Proses Pembayaran</div>
        <div class="card mobile-list-card mb-3">
            <div class="card-body mobile-form-body">
                <div class="mobile-payment-total">Total Keranjang Aktif: <?php echo mobileRupiah($totalKeranjangAktif); ?></div>
                <form method="post" action="mobile/kasir_mobile_action.php">
                    <input type="hidden" name="mobile_action" value="process_payment">
                    <input type="hidden" name="return_module" value="keranjang">

                    <div class="form-group mb-2">
                        <label for="mobile_bayar_input_keranjang" class="mobile-form-label">Jumlah Bayar</label>
                        <input id="mobile_bayar_input_keranjang" type="text" name="jumlah_bayar" class="form-control mobile-form-input" placeholder="Contoh: 50000" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="mobile_carabayar_select_keranjang" class="mobile-form-label">Metode Pembayaran</label>
                        <select id="mobile_carabayar_select_keranjang" name="id_carabayar" class="form-control mobile-form-input" required>
                            <option value="">Pilih metode</option>
                            <?php foreach ($metodeBayar as $cb) { ?>
                                <option value="<?php echo (int)$cb['id_carabayar']; ?>"><?php echo htmlspecialchars($cb['nm_carabayar']); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success btn-block mobile-submit-btn" <?php echo $totalKeranjangAktif <= 0 ? 'disabled' : ''; ?>>
                        <ion-icon name="cash-outline"></ion-icon>
                        Simpan Transaksi
                    </button>
                </form>
            </div>
        </div>
        <?php
    }
}

if ($showTransaksi) {
    if (!(mobileHasAccess('orders') || mobileHasAccess('tbm') || mobileHasAccess('tbmpbf') || mobileHasAccess('byrkredit') || mobileHasAccess('penjualansebelum') || mobileHasAccess('shiftkerja') || mobileHasAccess('cekdarah') || mobileHasAccess('catatan'))) {
        ?>
        <div class="mobile-section-title">Transaksi</div>
        <div class="card mobile-list-card mb-3">
            <div class="card-body" style="padding:14px;">
                <div class="text-danger">Anda tidak memiliki hak akses ke modul Transaksi.</div>
            </div>
        </div>
        <?php
        return;
    }

    $statusShift = 'Belum ada shift aktif';
    if (mobileTableExists($db, 'waktukerja')) {
        try {
            $stmtShift = $db->prepare("SELECT * FROM waktukerja WHERE tanggal = ? AND status = 'ON' LIMIT 1");
            $stmtShift->execute(array($todayDate));
            $shiftRow = $stmtShift->fetch(PDO::FETCH_ASSOC);
            if ($shiftRow) {
                $statusShift = 'Shift aktif: ' . $shiftRow['shift'];
            }
        } catch (Exception $e) {
            $statusShift = 'Status shift belum tersedia';
        }
    }
    ?>
    <div class="mobile-section-title">Transaksi</div>
    <div class="card mobile-list-card mb-2">
        <div class="card-body" style="padding:14px;">
            <div class="text-muted">Status Hari Ini</div>
            <h3 style="margin:8px 0 0; font-weight:800;"><?php echo htmlspecialchars($statusShift); ?></h3>
        </div>
    </div>

    <div class="mobile-menu-actions mb-3">
        <?php if (mobileHasAccess('tpk')) { ?>
            <a href="media_mobile.php?module=kasir" class="btn btn-primary">
                <ion-icon name="cart-outline"></ion-icon>&nbsp; Kasir Mobile
            </a>
        <?php } ?>
        <?php if (mobileHasAccess('penjualansebelum')) { ?>
            <a href="media_admin.php?module=penjualansebelumnya" class="btn btn-danger">
                <ion-icon name="create-outline"></ion-icon>&nbsp; Edit Penjualan
            </a>
        <?php } ?>
        <?php if (mobileHasAccess('orders')) { ?>
            <a href="media_admin.php?module=orders" class="btn btn-success">
                <ion-icon name="bag-add-outline"></ion-icon>&nbsp; Pesan Barang
            </a>
        <?php } ?>
        <?php if (mobileHasAccess('shiftkerja')) { ?>
            <a href="media_admin.php?module=shiftkerja" class="btn btn-warning">
                <ion-icon name="time-outline"></ion-icon>&nbsp; Open/Tutup Kasir
            </a>
        <?php } ?>
    </div>
<?php }

if ($showStok) {
    if (!(mobileHasAccess('mstok') || mobileHasAccess('stok_kritis') || mobileHasAccess('stokopname') || mobileHasAccess('soharian') || mobileHasAccess('kartustok') || mobileHasAccess('jurnalkas'))) {
        ?>
        <div class="mobile-section-title">Ringkasan Stok</div>
        <div class="card mobile-list-card mb-3">
            <div class="card-body" style="padding:14px;">
                <div class="text-danger">Anda tidak memiliki hak akses ke modul Stok.</div>
            </div>
        </div>
        <?php
        return;
    }

    $totalItem = mobileTableExists($db, 'barang') ? (int)mobileScalar($db, "SELECT COUNT(*) FROM barang", 0) : 0;

    $nilaiStok = 0;
    if (mobileTableExists($db, 'barang') && mobileColumnExists($db, 'barang', 'hrgsat_barang') && mobileColumnExists($db, 'barang', 'stok_barang')) {
        $nilaiStok = (float)mobileScalar($db, "SELECT COALESCE(SUM(hrgsat_barang * stok_barang), 0) FROM barang", 0);
    }

    $stokMenipis = array();
    if (mobileTableExists($db, 'barang') && mobileColumnExists($db, 'barang', 'stok_barang')) {
        try {
            $batas = 5;
            if (mobileColumnExists($db, 'barang', 'minstok_barang')) {
                $sqlStok = "SELECT nm_barang, stok_barang, minstok_barang AS batas_stok FROM barang WHERE stok_barang <= minstok_barang AND minstok_barang > 0 ORDER BY stok_barang ASC LIMIT 8";
            } elseif (mobileColumnExists($db, 'barang', 'stok_min')) {
                $sqlStok = "SELECT nm_barang, stok_barang, stok_min AS batas_stok FROM barang WHERE stok_barang <= stok_min AND stok_min > 0 ORDER BY stok_barang ASC LIMIT 8";
            } else {
                $sqlStok = "SELECT nm_barang, stok_barang, " . (int)$batas . " AS batas_stok FROM barang WHERE stok_barang <= " . (int)$batas . " ORDER BY stok_barang ASC LIMIT 8";
            }

            $stmtStok = $db->query($sqlStok);
            $stokMenipis = $stmtStok->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $stokMenipis = array();
        }
    }
    ?>
    <div class="mobile-section-title">Ringkasan Stok</div>
    <div class="mobile-grid">
        <div class="card mobile-stat-card">
            <div class="card-body">
                <span class="mobile-stat-icon"><ion-icon name="cube-outline"></ion-icon></span>
                <span class="mobile-stat-title">Total Item</span>
                <div class="mobile-stat-value"><?php echo number_format($totalItem, 0, ',', '.'); ?></div>
            </div>
        </div>
        <div class="card mobile-stat-card">
            <div class="card-body">
                <span class="mobile-stat-icon"><ion-icon name="wallet-outline"></ion-icon></span>
                <span class="mobile-stat-title">Nilai Stok</span>
                <div class="mobile-stat-value" style="font-size:20px;"><?php echo mobileRupiah($nilaiStok); ?></div>
            </div>
        </div>
    </div>

    <div class="mobile-section-title">Daftar Stok Menipis</div>
    <div class="card mobile-list-card mb-3">
        <ul class="listview flush transparent no-line image-listview">
            <?php if (count($stokMenipis) > 0) { ?>
                <?php foreach ($stokMenipis as $row) { ?>
                    <li>
                        <div class="item">
                            <div class="icon-box bg-warning">
                                <ion-icon name="warning-outline"></ion-icon>
                            </div>
                            <div class="in">
                                <div>
                                    <?php echo htmlspecialchars($row['nm_barang']); ?>
                                    <div class="text-muted" style="font-size:13px;">Stok: <?php echo (float)$row['stok_barang']; ?> | Batas: <?php echo (float)$row['batas_stok']; ?></div>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            <?php } else { ?>
                <li>
                    <div class="item">
                        <div class="in">Tidak ada item stok menipis saat ini.</div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>

    <div class="mobile-menu-actions mb-3">
        <?php if (mobileHasAccess('mstok')) { ?>
            <a href="media_admin.php?module=lapstok" class="btn btn-primary">
                <ion-icon name="bar-chart-outline"></ion-icon>&nbsp; Nilai Stok
            </a>
        <?php } ?>
        <?php if (mobileHasAccess('stok_kritis')) { ?>
            <a href="media_admin.php?module=stok_kritis" class="btn btn-danger">
                <ion-icon name="alert-circle-outline"></ion-icon>&nbsp; Stok Kritis
            </a>
        <?php } ?>
        <?php if (mobileHasAccess('stokopname')) { ?>
            <a href="media_admin.php?module=stokopname" class="btn btn-success">
                <ion-icon name="clipboard-outline"></ion-icon>&nbsp; Stok Opname
            </a>
        <?php } ?>
        <?php if (mobileHasAccess('kartustok')) { ?>
            <a href="media_admin.php?module=kartustok" class="btn btn-secondary">
                <ion-icon name="list-outline"></ion-icon>&nbsp; Kartu Stok
            </a>
        <?php } ?>
    </div>
<?php }

if ($showProfil) {
    $lastLoginText = '-';
    if (mobileTableExists($db, 'user_login_logs')) {
        try {
            $stmtLog = $db->prepare("SELECT login_time FROM user_login_logs WHERE user_id = ? ORDER BY id DESC LIMIT 1");
            $stmtLog->execute(array($_SESSION['idadmin']));
            $lastLogin = $stmtLog->fetchColumn();
            if (!empty($lastLogin)) {
                $lastLoginText = date('d-m-Y H:i', strtotime($lastLogin));
            }
        } catch (Exception $e) {
            $lastLoginText = '-';
        }
    }
    ?>
    <div class="mobile-section-title">Profil</div>
    <div class="card mobile-list-card mb-3">
        <div class="card-body" style="padding:14px;">
            <div class="text-muted">Nama</div>
            <h3 style="margin:0 0 10px; font-weight:800;"><?php echo htmlspecialchars($_SESSION['namalengkap']); ?></h3>

            <div class="text-muted">Username</div>
            <div style="margin-bottom:8px;"><?php echo htmlspecialchars($_SESSION['username']); ?></div>

            <div class="text-muted">Level Akses</div>
            <div style="margin-bottom:8px;"><?php echo htmlspecialchars($_SESSION['level']); ?></div>

            <div class="text-muted">Login Terakhir</div>
            <div><?php echo htmlspecialchars($lastLoginText); ?></div>
        </div>
    </div>

    <div class="mobile-menu-actions mb-3">
        <a href="media_admin.php?module=profil&act=editprofil&id=<?php echo urlencode((string)$_SESSION['idadmin']); ?>" class="btn btn-primary">
            <ion-icon name="create-outline"></ion-icon>&nbsp; Edit Profil
        </a>
        <a href="media_admin.php?module=home" class="btn btn-secondary">
            <ion-icon name="desktop-outline"></ion-icon>&nbsp; Versi Desktop
        </a>
        <a href="logout.php" class="btn btn-danger" style="grid-column: span 2;">
            <ion-icon name="log-out-outline"></ion-icon>&nbsp; Logout
        </a>
    </div>
<?php }
