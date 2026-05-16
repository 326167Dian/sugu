-- Add ujian permission column to admin table (idempotent)
SET @col_exists := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'admin'
      AND COLUMN_NAME = 'ujian'
);

SET @sql := IF(
    @col_exists = 0,
    "ALTER TABLE admin ADD COLUMN ujian VARCHAR(1) NOT NULL DEFAULT 'N' AFTER jurnalkas",
    "SELECT 'Column ujian already exists'"
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
