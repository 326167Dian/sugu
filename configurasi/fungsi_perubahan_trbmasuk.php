<?php

// DDL (ALTER TABLE) menyebabkan implicit commit di MySQL, jadi harus dipanggil
// SEBELUM beginTransaction() di file yang memakainya, supaya tidak menutup
// transaction secara diam-diam.
function pastikan_kolom_tipe_barang_trbmasuk($db)
{
    try {
        $cek = $db->prepare("SHOW COLUMNS FROM trbmasuk_detail LIKE 'tipe_barang'");
        $cek->execute();
        if ($cek->rowCount() == 0) {
            $db->exec("ALTER TABLE trbmasuk_detail
                        ADD COLUMN tipe_barang ENUM('reguler','bonus') NOT NULL DEFAULT 'reguler' AFTER tipe");
        }
    } catch (Exception $e) {
    }
}
