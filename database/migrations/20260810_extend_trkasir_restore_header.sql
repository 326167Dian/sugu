-- Melengkapi snapshot header transaksi pada trkasir_restore supaya fitur
-- "UNDO TRANSAKSI TERHAPUS" bisa mengembalikan transaksi secara akurat
-- (petugas asli, keterkaitan pelanggan, poin) alih-alih memakai nilai default.

ALTER TABLE trkasir_restore
    ADD COLUMN id_user INT(11) NULL,
    ADD COLUMN id_pelanggan INT(11) NULL,
    ADD COLUMN kodetx VARCHAR(20) NULL,
    ADD COLUMN jenistx INT(11) NULL,
    ADD COLUMN waktu_trx DATETIME NULL,
    ADD COLUMN poin_awal INT(11) NULL,
    ADD COLUMN tambahan_poin INT(11) NULL,
    ADD COLUMN redeem_poin INT(11) NULL;
