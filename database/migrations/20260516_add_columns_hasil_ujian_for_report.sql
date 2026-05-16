-- Add columns for final exam report table (idempotent)

SET @col_ujian_id := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'hasil_ujian'
      AND COLUMN_NAME = 'ujian_id'
);

SET @sql_ujian_id := IF(
    @col_ujian_id = 0,
    "ALTER TABLE hasil_ujian ADD COLUMN ujian_id INT(11) DEFAULT NULL AFTER nama_lengkap",
    "SELECT 'Column ujian_id already exists'"
);
PREPARE stmt_ujian_id FROM @sql_ujian_id;
EXECUTE stmt_ujian_id;
DEALLOCATE PREPARE stmt_ujian_id;

SET @col_nama_ujian := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'hasil_ujian'
      AND COLUMN_NAME = 'nama_ujian'
);

SET @sql_nama_ujian := IF(
    @col_nama_ujian = 0,
    "ALTER TABLE hasil_ujian ADD COLUMN nama_ujian VARCHAR(100) DEFAULT NULL AFTER ujian_id",
    "SELECT 'Column nama_ujian already exists'"
);
PREPARE stmt_nama_ujian FROM @sql_nama_ujian;
EXECUTE stmt_nama_ujian;
DEALLOCATE PREPARE stmt_nama_ujian;

SET @col_tidak_dijawab := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'hasil_ujian'
      AND COLUMN_NAME = 'tidak_dijawab'
);

SET @sql_tidak_dijawab := IF(
    @col_tidak_dijawab = 0,
    "ALTER TABLE hasil_ujian ADD COLUMN tidak_dijawab INT(11) NOT NULL DEFAULT 0 AFTER jawaban_salah",
    "SELECT 'Column tidak_dijawab already exists'"
);
PREPARE stmt_tidak_dijawab FROM @sql_tidak_dijawab;
EXECUTE stmt_tidak_dijawab;
DEALLOCATE PREPARE stmt_tidak_dijawab;

SET @idx_hasil_ujian_ujian := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'hasil_ujian'
      AND INDEX_NAME = 'idx_hasil_ujian_ujian'
);

SET @sql_idx_hasil_ujian_ujian := IF(
    @idx_hasil_ujian_ujian = 0,
    "ALTER TABLE hasil_ujian ADD INDEX idx_hasil_ujian_ujian (ujian_id, waktu_selesai)",
    "SELECT 'Index idx_hasil_ujian_ujian already exists'"
);
PREPARE stmt_idx_hasil_ujian_ujian FROM @sql_idx_hasil_ujian_ujian;
EXECUTE stmt_idx_hasil_ujian_ujian;
DEALLOCATE PREPARE stmt_idx_hasil_ujian_ujian;
