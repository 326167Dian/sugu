-- Add relation soal.id_soal -> soal_header.id_soal (idempotent and safe)

-- 0) Ensure both tables use InnoDB
ALTER TABLE soal ENGINE=InnoDB;
ALTER TABLE soal_header ENGINE=InnoDB;

-- 1) Add column id_soal in table soal if not exists
SET @col_exists := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'soal'
      AND COLUMN_NAME = 'id_soal'
);

SET @sql_col := IF(
    @col_exists = 0,
    "ALTER TABLE soal ADD COLUMN id_soal INT(11) NULL AFTER id",
    "SELECT 'Column id_soal already exists'"
);

PREPARE stmt_col FROM @sql_col;
EXECUTE stmt_col;
DEALLOCATE PREPARE stmt_col;

-- 2) Normalize child column type to match parent key type
ALTER TABLE soal MODIFY COLUMN id_soal INT(11) NULL;

-- 3) Clean orphan values before adding FK (required)
UPDATE soal s
LEFT JOIN soal_header h ON h.id_soal = s.id_soal
SET s.id_soal = NULL
WHERE s.id_soal IS NOT NULL
  AND h.id_soal IS NULL;

-- 4) Add index on id_soal if not exists
SET @idx_exists := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'soal'
      AND INDEX_NAME = 'idx_soal_id_soal'
);

SET @sql_idx := IF(
    @idx_exists = 0,
    "ALTER TABLE soal ADD INDEX idx_soal_id_soal (id_soal)",
    "SELECT 'Index idx_soal_id_soal already exists'"
);

PREPARE stmt_idx FROM @sql_idx;
EXECUTE stmt_idx;
DEALLOCATE PREPARE stmt_idx;

-- 5) Drop existing FK on soal.id_soal (if any) to avoid name/type conflicts
SET @existing_fk := (
    SELECT kcu.CONSTRAINT_NAME
    FROM information_schema.KEY_COLUMN_USAGE kcu
    WHERE kcu.TABLE_SCHEMA = DATABASE()
      AND kcu.TABLE_NAME = 'soal'
      AND kcu.COLUMN_NAME = 'id_soal'
      AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
    LIMIT 1
);

SET @sql_drop_fk := IF(
    @existing_fk IS NOT NULL,
    CONCAT('ALTER TABLE soal DROP FOREIGN KEY ', @existing_fk),
    "SELECT 'No existing FK to drop'"
);

PREPARE stmt_drop_fk FROM @sql_drop_fk;
EXECUTE stmt_drop_fk;
DEALLOCATE PREPARE stmt_drop_fk;

-- 6) Add FK with a stable name
SET @fk_exists := (
    SELECT COUNT(*)
    FROM information_schema.REFERENTIAL_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND CONSTRAINT_NAME = 'fk_soal_soal_header'
      AND TABLE_NAME = 'soal'
);

SET @sql_fk := IF(
    @fk_exists = 0,
    "ALTER TABLE soal ADD CONSTRAINT fk_soal_soal_header FOREIGN KEY (id_soal) REFERENCES soal_header(id_soal) ON UPDATE CASCADE ON DELETE SET NULL",
    "SELECT 'FK fk_soal_soal_header already exists'"
);

PREPARE stmt_fk FROM @sql_fk;
EXECUTE stmt_fk;
DEALLOCATE PREPARE stmt_fk;
