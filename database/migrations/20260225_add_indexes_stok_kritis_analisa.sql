-- Migration: Add indexes for stok kritis (kritis30) analysis performance
-- Date: 2026-02-25
-- Run this script once on the target database.

SET @db_name = DATABASE();

-- trkasir(tgl_trkasir, kd_trkasir)
SET @idx_exists = (
    SELECT COUNT(1)
    FROM information_schema.statistics
    WHERE table_schema = @db_name
      AND table_name = 'trkasir'
      AND index_name = 'idx_trkasir_tgl_kd'
);
SET @sql_stmt = IF(
    @idx_exists = 0,
    'ALTER TABLE trkasir ADD INDEX idx_trkasir_tgl_kd (tgl_trkasir, kd_trkasir)',
    'SELECT ''SKIP: idx_trkasir_tgl_kd already exists'''
);
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- trkasir_detail(kd_trkasir, kd_barang)
SET @idx_exists = (
    SELECT COUNT(1)
    FROM information_schema.statistics
    WHERE table_schema = @db_name
      AND table_name = 'trkasir_detail'
      AND index_name = 'idx_trkasir_detail_kdtrkasir_kdbarang'
);
SET @sql_stmt = IF(
    @idx_exists = 0,
    'ALTER TABLE trkasir_detail ADD INDEX idx_trkasir_detail_kdtrkasir_kdbarang (kd_trkasir, kd_barang)',
    'SELECT ''SKIP: idx_trkasir_detail_kdtrkasir_kdbarang already exists'''
);
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
