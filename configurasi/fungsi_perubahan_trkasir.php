<?php

function pastikan_skema_perubahan_trkasir($db)
{
    try {
        $cek = $db->prepare("SHOW COLUMNS FROM trkasir LIKE 'tipetx'");
        $cek->execute();
        if ($cek->rowCount() == 0) {
            $db->exec("ALTER TABLE trkasir ADD COLUMN tipetx INT(11) NOT NULL DEFAULT 1 AFTER jenistx");
        }

        $cek = $db->prepare("SHOW COLUMNS FROM trkasir_detail LIKE 'tipetx'");
        $cek->execute();
        if ($cek->rowCount() == 0) {
            $db->exec("ALTER TABLE trkasir_detail ADD COLUMN tipetx INT(11) NOT NULL DEFAULT 1 AFTER idadmin");
        }

        $cek = $db->prepare("SHOW COLUMNS FROM trkasir_detail_hist LIKE 'tipetx_asal'");
        $cek->execute();
        if ($cek->rowCount() == 0) {
            $db->exec("ALTER TABLE trkasir_detail_hist
                        ADD COLUMN tipetx_asal INT(11) NOT NULL DEFAULT 1 AFTER idadmin,
                        ADD COLUMN tipetx_hapus INT(11) NOT NULL DEFAULT 1 AFTER tipetx_asal,
                        ADD COLUMN waktu_hapus TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER tipetx_hapus,
                        ADD COLUMN id_admin_hapus INT(11) NULL AFTER waktu_hapus");
        }

        $cek = $db->prepare("SHOW COLUMNS FROM trkasir_restore LIKE 'tipetx'");
        $cek->execute();
        if ($cek->rowCount() == 0) {
            $db->exec("ALTER TABLE trkasir_restore
                        ADD COLUMN id_dtrkasir INT(11) NULL,
                        ADD COLUMN disc INT(2) NULL,
                        ADD COLUMN resep VARCHAR(10) NULL,
                        ADD COLUMN modal DOUBLE NULL,
                        ADD COLUMN profit DOUBLE NULL,
                        ADD COLUMN no_batch VARCHAR(20) NULL,
                        ADD COLUMN exp_date DATE NULL,
                        ADD COLUMN waktu TIMESTAMP NULL,
                        ADD COLUMN tipe INT(11) NULL,
                        ADD COLUMN komisi INT(11) NULL,
                        ADD COLUMN idadmin INT(11) NULL,
                        ADD COLUMN kd_bundle VARCHAR(50) NULL,
                        ADD COLUMN nm_bundle VARCHAR(100) NULL,
                        ADD COLUMN tipetx INT(11) NOT NULL DEFAULT 1,
                        ADD COLUMN waktu_hapus TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        ADD COLUMN id_admin_hapus INT(11) NULL");
        }

        $cek = $db->prepare("SHOW COLUMNS FROM trkasir_restore LIKE 'id_user'");
        $cek->execute();
        if ($cek->rowCount() == 0) {
            $db->exec("ALTER TABLE trkasir_restore
                        ADD COLUMN id_user INT(11) NULL,
                        ADD COLUMN id_pelanggan INT(11) NULL,
                        ADD COLUMN kodetx VARCHAR(20) NULL,
                        ADD COLUMN jenistx INT(11) NULL,
                        ADD COLUMN waktu_trx DATETIME NULL,
                        ADD COLUMN poin_awal INT(11) NULL,
                        ADD COLUMN tambahan_poin INT(11) NULL,
                        ADD COLUMN redeem_poin INT(11) NULL");
        }

        $db->exec("CREATE TABLE IF NOT EXISTS trkasir_detail_ubah_qty (
                        id_log BIGINT(20) NOT NULL AUTO_INCREMENT,
                        kd_trkasir VARCHAR(100) NOT NULL,
                        id_dtrkasir INT(11) NOT NULL,
                        kd_barang VARCHAR(50) NOT NULL,
                        nmbrg_dtrkasir VARCHAR(100) NOT NULL,
                        qty_sebelum DOUBLE NOT NULL,
                        qty_sesudah DOUBLE NOT NULL,
                        hrgttl_sebelum DOUBLE NOT NULL,
                        hrgttl_sesudah DOUBLE NOT NULL,
                        tipetx INT(11) NOT NULL,
                        id_admin INT(11) NULL,
                        waktu TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (id_log),
                        KEY idx_trkasir_detail_ubah_qty_kd_trkasir (kd_trkasir)
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci");
    } catch (Exception $e) {
    }
}

function apakah_transaksi_final($db, $kd_trkasir)
{
    $cek = $db->prepare("SELECT 1 FROM trkasir WHERE kd_trkasir = ? LIMIT 1");
    $cek->execute([$kd_trkasir]);
    return $cek->rowCount() > 0;
}

function catat_revisi_transaksi($db, $kd_trkasir)
{
    $db->prepare("UPDATE trkasir SET tipetx = tipetx + 1 WHERE kd_trkasir = ?")
        ->execute([$kd_trkasir]);

    $ambil = $db->prepare("SELECT tipetx FROM trkasir WHERE kd_trkasir = ?");
    $ambil->execute([$kd_trkasir]);
    $r = $ambil->fetch(PDO::FETCH_ASSOC);

    return (int) $r['tipetx'];
}

function catat_ubah_qty($db, $kd_trkasir, $id_dtrkasir, $kd_barang, $nmbrg_dtrkasir, $qty_sebelum, $qty_sesudah, $hrgttl_sebelum, $hrgttl_sesudah, $tipetx, $id_admin)
{
    $stmt = $db->prepare("INSERT INTO trkasir_detail_ubah_qty (
                                kd_trkasir, id_dtrkasir, kd_barang, nmbrg_dtrkasir,
                                qty_sebelum, qty_sesudah, hrgttl_sebelum, hrgttl_sesudah,
                                tipetx, id_admin
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$kd_trkasir, $id_dtrkasir, $kd_barang, $nmbrg_dtrkasir, $qty_sebelum, $qty_sesudah, $hrgttl_sebelum, $hrgttl_sesudah, $tipetx, $id_admin]);
}
