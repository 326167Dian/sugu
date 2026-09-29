-- Create table soal_header (master ujian) for ujian module (idempotent)
-- Harus dijalankan sebelum 20260516_add_fk_soal_to_soal_header.sql
CREATE TABLE IF NOT EXISTS soal_header (
    id_soal INT(11) NOT NULL AUTO_INCREMENT,
    nm_ujian VARCHAR(100) NOT NULL,
    durasi INT(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (id_soal)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
