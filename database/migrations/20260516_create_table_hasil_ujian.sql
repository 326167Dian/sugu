-- Create table hasil_ujian for storing exam results (idempotent)
CREATE TABLE IF NOT EXISTS hasil_ujian (
    id_hasil BIGINT(20) NOT NULL AUTO_INCREMENT,
    id_admin INT(11) DEFAULT NULL,
    username VARCHAR(100) DEFAULT NULL,
    nama_lengkap VARCHAR(150) DEFAULT NULL,
    total_soal INT(11) NOT NULL DEFAULT 0,
    jawaban_benar INT(11) NOT NULL DEFAULT 0,
    jawaban_salah INT(11) NOT NULL DEFAULT 0,
    soal_tidak_valid INT(11) NOT NULL DEFAULT 0,
    nilai_akhir DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    waktu_mulai DATETIME DEFAULT NULL,
    waktu_selesai DATETIME NOT NULL,
    durasi_detik INT(11) DEFAULT NULL,
    durasi_batas_detik INT(11) DEFAULT NULL,
    status_waktu ENUM('on_time','timeout') NOT NULL DEFAULT 'on_time',
    jawaban_json LONGTEXT DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_hasil),
    KEY idx_hasil_ujian_id_admin_waktu (id_admin, waktu_selesai)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
