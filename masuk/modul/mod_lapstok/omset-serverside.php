<?php
include "../../../configurasi/fungsi_rupiah.php";
include "../../../configurasi/koneksi.php";
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simple to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use

$table = 'barang';

$primaryKey = 'id_barang';

$no = 1;
$columns = array(
    array(
        'db' => 'id_barang', 'dt' => 0,
        'formatter' => function ($d, $row) {
            $start = $_GET['start'];
            $finish = $_GET['finish'];

            $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
        WHERE kd_barang = '$row[kd_barang]' AND (tgl_trkasir BETWEEN '$start' and '$finish')");
            $pass1 = mysqli_num_rows($pass);

            if ($pass1 > 10) {
                return $pass1;
            }
        }
    ),
    array(
        'db' => 'kd_barang', 'dt' => 1,
        'formatter' => function ($d, $row) {
            $start = $_GET['start'];
            $finish = $_GET['finish'];

            $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
        WHERE kd_barang = '$row[kd_barang]' AND (tgl_trkasir BETWEEN '$start' and '$finish')");
            $pass1 = mysqli_num_rows($pass);

            if ($pass1 > 10) {
                return $pass1;
            }
        }
    ),
    array(
        'db' => 'nm_barang', 'dt' => 2,
        'formatter' => function ($d, $row) {
            $start = $_GET['start'];
            $finish = $_GET['finish'];

            $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
        WHERE kd_barang = '$row[kd_barang]' AND (tgl_trkasir BETWEEN '$start' and '$finish')");
            $pass1 = mysqli_num_rows($pass);

            if ($pass1 > 10) {
                return $pass1;
            }
        }
    ),
    array(
        'db' => 'stok_barang', 'dt' => 3,
        'formatter' => function ($d, $row) {
            $start = $_GET['start'];
            $finish = $_GET['finish'];

            $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
        WHERE kd_barang = '$row[kd_barang]' AND (tgl_trkasir BETWEEN '$start' and '$finish')");
            $pass1 = mysqli_num_rows($pass);

            if ($pass1 > 10) {
                return $pass1;
            }
        }
    ),
    array(
        'db' => 'stok_buffer', 'dt' => 4,
        'formatter' => function ($d, $row) {
            $start = $_GET['start'];
            $finish = $_GET['finish'];

            $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
        WHERE kd_barang = '$row[kd_barang]' AND (tgl_trkasir BETWEEN '$start' and '$finish')");
            $pass1 = mysqli_num_rows($pass);

            if ($pass1 > 10) {
                return $pass1;
            }
        }
    ),
    array(
        'db' => 'id_barang', 'dt' => 5,
        'formatter' => function ($d, $row) {
            $start = $_GET['start'];
            $finish = $_GET['finish'];

            $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
            ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
            WHERE kd_barang = '$row[kd_barang]' AND (tgl_trkasir BETWEEN '$start' and '$finish')");
            $pass1 = mysqli_num_rows($pass);

            if ($pass1 > 10) {
                return $pass1;
            }
        }
    ),
    array(
        'db' => 'id_barang', 'dt' => 6,
        'formatter' => function ($d, $row) {
            $start = $_GET['start'];
            $finish = $_GET['finish'];

            $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
        WHERE kd_barang = '$row[kd_barang]' AND (tgl_trkasir BETWEEN '$start' and '$finish')");
            $pass1 = mysqli_num_rows($pass);

            if ($pass1 > 10) {
                return $pass1;
            }
        }
    ),
    array(
        'db' => 'id_barang', 'dt' => 7,
        'formatter' => function ($d, $row) {
            $start = $_GET['start'];
            $finish = $_GET['finish'];

            $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
            ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
            WHERE kd_barang = '$row[kd_barang]' AND (tgl_trkasir BETWEEN '$start' and '$finish')");
            $pass1 = mysqli_num_rows($pass);

            if ($pass1 > 10) {
                return $pass1;
            }
        }
    ),
    array(
        'db' => 'id_barang', 'dt' => 8,
        'formatter' => function ($d, $row) {
            $start = $_GET['start'];
            $finish = $_GET['finish'];

            $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
        WHERE kd_barang = '$row[kd_barang]' AND (tgl_trkasir BETWEEN '$start' and '$finish')");
            $pass1 = mysqli_num_rows($pass);

            if ($pass1 > 10) {
                return $pass1;
            }
        }
    ),
    array(
        'db' => 'id_barang', 'dt' => 9,
        'formatter' => function ($d, $row) {
            $start = $_GET['start'];
            $finish = $_GET['finish'];

            $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
        WHERE kd_barang = '$row[kd_barang]' AND (tgl_trkasir BETWEEN '$start' and '$finish')");
            $pass1 = mysqli_num_rows($pass);

            if ($pass1 > 10) {
                return $pass1;
            }
        }
    ),
    array(
        'db' => 'id_barang', 'dt' => 10,
        'formatter' => function ($d, $row) {
            $start = $_GET['start'];
            $finish = $_GET['finish'];

            $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
        WHERE kd_barang = '$row[kd_barang]' AND (tgl_trkasir BETWEEN '$start' and '$finish')");
            $pass1 = mysqli_num_rows($pass);

            if ($pass1 > 10) {
                return $pass1;
            }
        }
    ),
    array(
        'db' => 'id_barang', 'dt' => 11,
        'formatter' => function ($d, $row) {
            $start = $_GET['start'];
            $finish = $_GET['finish'];

            $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
        WHERE kd_barang = '$row[kd_barang]' AND (tgl_trkasir BETWEEN '$start' and '$finish')");
            $pass1 = mysqli_num_rows($pass);

            if ($pass1 > 10) {
                return $pass1;
            }
        }
    ),
    array(
        'db' => 'id_barang', 'dt' => 12,
        'formatter' => function ($d, $row) {
            $start = $_GET['start'];
            $finish = $_GET['finish'];

            $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
        WHERE kd_barang = '$row[kd_barang]' AND (tgl_trkasir BETWEEN '$start' and '$finish')");
            $pass1 = mysqli_num_rows($pass);

            if ($pass1 > 10) {
                return $pass1;
            }
        }
    ),

);

include_once '../../../configurasi/conn.php';

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require('../../../configurasi/ssp.class.php');

echo json_encode(
    // SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns)
    SSP::omset($_GET, $sql_details, $table, $primaryKey, $columns)
);
