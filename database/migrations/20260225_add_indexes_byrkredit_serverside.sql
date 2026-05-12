-- Migration: Add indexes for byrkredit server-side table performance
-- Date: 2026-02-25
-- Run this script once on the target database.

SET @db_name = DATABASE();

-- trbmasuk(id_resto, id_trbmasuk)
SET @idx_exists = (
    SELECT COUNT(1)
    FROM information_schema.statistics
    WHERE table_schema = @db_name
      AND table_name = 'trbmasuk'
      AND index_name = 'idx_trbmasuk_idresto_id'
);
SET @sql_stmt = IF(
    @idx_exists = 0,
    'ALTER TABLE trbmasuk ADD INDEX idx_trbmasuk_idresto_id (id_resto, id_trbmasuk)',
    'SELECT ''SKIP: idx_trbmasuk_idresto_id already exists'''
);
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- trbmasuk(id_resto, kd_trbmasuk)
SET @idx_exists = (
    SELECT COUNT(1)
    FROM information_schema.statistics
    WHERE table_schema = @db_name
      AND table_name = 'trbmasuk'
      AND index_name = 'idx_trbmasuk_idresto_kd'
);
SET @sql_stmt = IF(
    @idx_exists = 0,
    'ALTER TABLE trbmasuk ADD INDEX idx_trbmasuk_idresto_kd (id_resto, kd_trbmasuk)',
    'SELECT ''SKIP: idx_trbmasuk_idresto_kd already exists'''
);
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- trbmasuk(id_resto, tgl_trbmasuk)
SET @idx_exists = (
    SELECT COUNT(1)
    FROM information_schema.statistics
    WHERE table_schema = @db_name
      AND table_name = 'trbmasuk'
      AND index_name = 'idx_trbmasuk_idresto_tgl'
);
SET @sql_stmt = IF(
    @idx_exists = 0,
    'ALTER TABLE trbmasuk ADD INDEX idx_trbmasuk_idresto_tgl (id_resto, tgl_trbmasuk)',
    'SELECT ''SKIP: idx_trbmasuk_idresto_tgl already exists'''
);
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- trbmasuk(id_resto, nm_supplier)
SET @idx_exists = (
    SELECT COUNT(1)
    FROM information_schema.statistics
    WHERE table_schema = @db_name
      AND table_name = 'trbmasuk'
      AND index_name = 'idx_trbmasuk_idresto_supplier'
);
SET @sql_stmt = IF(
    @idx_exists = 0,
    'ALTER TABLE trbmasuk ADD INDEX idx_trbmasuk_idresto_supplier (id_resto, nm_supplier)',
    'SELECT ''SKIP: idx_trbmasuk_idresto_supplier already exists'''
);
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- trbmasuk(id_resto, carabayar)
SET @idx_exists = (
    SELECT COUNT(1)
    FROM information_schema.statistics
    WHERE table_schema = @db_name
      AND table_name = 'trbmasuk'
      AND index_name = 'idx_trbmasuk_idresto_carabayar'
);
SET @sql_stmt = IF(
    @idx_exists = 0,
    'ALTER TABLE trbmasuk ADD INDEX idx_trbmasuk_idresto_carabayar (id_resto, carabayar)',
    'SELECT ''SKIP: idx_trbmasuk_idresto_carabayar already exists'''
);
PREPARE stmt FROM @sql_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
