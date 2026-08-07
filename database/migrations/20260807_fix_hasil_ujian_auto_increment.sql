-- Fix: id_hasil pada tabel hasil_ujian kehilangan AUTO_INCREMENT (idempotent).
-- Tanpa AUTO_INCREMENT, INSERT yang tidak menyertakan id_hasil memakai default
-- implisit 0 (sql_mode server tidak STRICT), sehingga baris kedua tanpa id
-- eksplisit gagal dengan "Duplicate entry '0' for key 'PRIMARY'" dan hasil
-- ujian gagal tersimpan tanpa pemberitahuan (di-catch diam-diam oleh proses.php).

-- Langkah 1: pindahkan baris id_hasil = 0 (jika ada) ke id berikutnya yang aman,
-- karena MySQL menolak ALTER ... AUTO_INCREMENT bila ada nilai 0 yang bentrok
-- saat resequencing (0 diperlakukan setara NULL untuk auto_increment).
UPDATE hasil_ujian AS zero_row
JOIN (
    SELECT COALESCE(MAX(id_hasil), 0) + 1 AS next_id
    FROM hasil_ujian
    WHERE id_hasil <> 0
) AS calc ON 1 = 1
SET zero_row.id_hasil = calc.next_id
WHERE zero_row.id_hasil = 0;

-- Langkah 2: aktifkan lagi AUTO_INCREMENT pada id_hasil.
SET @needs_fix := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'hasil_ujian'
      AND COLUMN_NAME = 'id_hasil'
      AND EXTRA NOT LIKE '%auto_increment%'
);

SET @sql_fix := IF(
    @needs_fix > 0,
    'ALTER TABLE hasil_ujian MODIFY id_hasil BIGINT(20) NOT NULL AUTO_INCREMENT',
    "SELECT 'id_hasil already AUTO_INCREMENT'"
);
PREPARE stmt_fix FROM @sql_fix;
EXECUTE stmt_fix;
DEALLOCATE PREPARE stmt_fix;
