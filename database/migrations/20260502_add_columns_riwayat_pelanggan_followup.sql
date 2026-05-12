ALTER TABLE `riwayat_pelanggan`
    ADD COLUMN `tgl_followup` DATETIME NULL DEFAULT NULL AFTER `followup`,
    ADD COLUMN `followup_by` VARCHAR(100) NULL DEFAULT NULL AFTER `tgl_followup`;