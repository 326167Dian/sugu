-- Data keprofesian apoteker untuk laporan "Rekap Administratif Keprofesian"
-- (menu Laporan). Satu baris per admin yang pernah dipilih sebagai apoteker.
-- Diisi/diperbarui otomatis dari form laporan saat dicetak.
--
-- Catatan: tabel ini juga otomatis dibuat saat runtime lewat
-- masuk/modul/mod_rekapkeprofesian/fungsi_rekapkeprofesian.php (pastikan_tabel_apoteker_profesi)
-- sebagai jaring pengaman kalau migrasi manual ini belum sempat dijalankan.
-- Migrasi ini idempotent (CREATE TABLE IF NOT EXISTS).

CREATE TABLE IF NOT EXISTS apoteker_profesi (
    id_admin INT(11) NOT NULL,
    nama_gelar VARCHAR(150) NOT NULL DEFAULT '',
    jabatan VARCHAR(50) NOT NULL DEFAULT 'Apoteker',
    no_stra VARCHAR(100) NOT NULL DEFAULT '',
    no_sipa VARCHAR(100) NOT NULL DEFAULT '',
    sipa_berlaku DATE NULL DEFAULT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_admin)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
