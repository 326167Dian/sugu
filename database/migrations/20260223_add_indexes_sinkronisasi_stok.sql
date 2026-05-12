-- Migration: Add indexes for stock synchronization performance
-- Date: 2026-02-23
-- Run this script once on the target database.

SET @db_name = DATABASE();

-- trbmasuk_detail.kd_barang
SET @idx_exists = (
    SELECT COUNT(1)
    FROM information_schema.statistics
    WHERE table_schema = @db_name
      AND table_name = 'trbmasuk_detail'
      AND index_name = 'idx_trbmasuk_detail_kd_barang'
);
SET @sql_stmt = IF(
    @idx_exists = 0,
    'ALTER TABLE trbmasuk_detail ADD INDEX idx_trbmasuk_detail_kd_barang (kd_barang)',
    'SELECT ''SKIP: idx_trbmasuk_detail_kd_barang already exists'''
);
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- trkasir_detail.kd_barang
SET @idx_exists = (
    SELECT COUNT(1)
    FROM information_schema.statistics
    WHERE table_schema = @db_name
      AND table_name = 'trkasir_detail'
      AND index_name = 'idx_trkasir_detail_kd_barang'
);
SET @sql_stmt = IF(
    @idx_exists = 0,
    'ALTER TABLE trkasir_detail ADD INDEX idx_trkasir_detail_kd_barang (kd_barang)',
    'SELECT ''SKIP: idx_trkasir_detail_kd_barang already exists'''
);
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- barang.kd_barang
SET @idx_exists = (
    SELECT COUNT(1)
    FROM information_schema.statistics
    WHERE table_schema = @db_name
      AND table_name = 'barang'
      AND index_name = 'idx_barang_kd_barang'
);
SET @sql_stmt = IF(
    @idx_exists = 0,
    'ALTER TABLE barang ADD INDEX idx_barang_kd_barang (kd_barang)',
    'SELECT ''SKIP: idx_barang_kd_barang already exists'''
);
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
