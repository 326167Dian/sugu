-- Migration: Add indexes for laporan laba penjualan performance
-- Date: 2026-02-25
-- Run this script once on the target database.

SET @db_name = DATABASE();

-- trkasir(shift, tgl_trkasir, id_carabayar, kd_trkasir)
SET @idx_exists = (
    SELECT COUNT(1)
    FROM information_schema.statistics
    WHERE table_schema = @db_name
      AND table_name = 'trkasir'
      AND index_name = 'idx_trkasir_shift_tgl_carabayar_kd'
);
SET @sql_stmt = IF(
    @idx_exists = 0,
    'ALTER TABLE trkasir ADD INDEX idx_trkasir_shift_tgl_carabayar_kd (shift, tgl_trkasir, id_carabayar, kd_trkasir)',
    'SELECT ''SKIP: idx_trkasir_shift_tgl_carabayar_kd already exists'''
);
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- trkasir_detail(kd_trkasir, nmbrg_dtrkasir)
SET @idx_exists = (
    SELECT COUNT(1)
    FROM information_schema.statistics
    WHERE table_schema = @db_name
      AND table_name = 'trkasir_detail'
      AND index_name = 'idx_trkasir_detail_kdtrkasir_nmbrg'
);
SET @sql_stmt = IF(
    @idx_exists = 0,
    'ALTER TABLE trkasir_detail ADD INDEX idx_trkasir_detail_kdtrkasir_nmbrg (kd_trkasir, nmbrg_dtrkasir)',
    'SELECT ''SKIP: idx_trkasir_detail_kdtrkasir_nmbrg already exists'''
);
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- trkasir_detail(id_barang)
SET @idx_exists = (
    SELECT COUNT(1)
    FROM information_schema.statistics
    WHERE table_schema = @db_name
      AND table_name = 'trkasir_detail'
      AND index_name = 'idx_trkasir_detail_id_barang'
);
SET @sql_stmt = IF(
    @idx_exists = 0,
    'ALTER TABLE trkasir_detail ADD INDEX idx_trkasir_detail_id_barang (id_barang)',
    'SELECT ''SKIP: idx_trkasir_detail_id_barang already exists'''
);
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
