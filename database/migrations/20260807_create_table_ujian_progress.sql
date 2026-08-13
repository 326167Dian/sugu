-- Create table ujian_progress for autosaving in-progress exam attempts (idempotent)
CREATE TABLE IF NOT EXISTS ujian_progress (
    id_progress BIGINT(20) NOT NULL AUTO_INCREMENT,
    id_admin INT(11) NOT NULL,
    username VARCHAR(100) DEFAULT NULL,
    nama_lengkap VARCHAR(150) DEFAULT NULL,
    ujian_id INT(11) NOT NULL,
    nama_ujian VARCHAR(150) DEFAULT NULL,
    jawaban_json LONGTEXT DEFAULT NULL,
    waktu_mulai DATETIME DEFAULT NULL,
    waktu_update DATETIME DEFAULT NULL,
    PRIMARY KEY (id_progress),
    UNIQUE KEY uniq_ujian_progress_admin_ujian (id_admin, ujian_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

