-- Fitur pelacakan revisi transaksi trkasir (tambah/hapus/ubah qty item setelah transaksi final)

ALTER TABLE trkasir ADD COLUMN tipetx INT(11) NOT NULL DEFAULT 1 AFTER jenistx;

ALTER TABLE trkasir_detail ADD COLUMN tipetx INT(11) NOT NULL DEFAULT 1 AFTER idadmin;

ALTER TABLE trkasir_detail_hist
    ADD COLUMN tipetx_asal INT(11) NOT NULL DEFAULT 1 AFTER idadmin,
    ADD COLUMN tipetx_hapus INT(11) NOT NULL DEFAULT 1 AFTER tipetx_asal,
    ADD COLUMN waktu_hapus TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER tipetx_hapus,
    ADD COLUMN id_admin_hapus INT(11) NULL AFTER waktu_hapus;

-- Aktifkan kembali tabel trkasir_restore (sudah ada di skema, belum pernah dipakai)
-- sebagai snapshot kondisi akhir untuk transaksi yang dihapus total.
ALTER TABLE trkasir_restore
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
    ADD COLUMN id_admin_hapus INT(11) NULL;

-- Log qty sebelum/sesudah untuk item yang sudah ada lalu ditambah qty-nya lagi
-- (UPDATE menimpa nilai lama di baris yang sama sehingga perlu dicatat terpisah).
CREATE TABLE IF NOT EXISTS trkasir_detail_ubah_qty (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
