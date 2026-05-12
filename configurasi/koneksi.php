<?php
Date_Default_timezone_set('Asia/jakarta');

// Load helper koneksi rahasia dari folder yang sama.
$hiddenConnectionFiles = array('.terhubung.php', '.terhubung');
$loadedHelper = false;

foreach ($hiddenConnectionFiles as $helperFile) {
	$helperPath = __DIR__ . DIRECTORY_SEPARATOR . $helperFile;
	if (is_file($helperPath)) {
		require_once $helperPath;
		$loadedHelper = true;
		break;
	}
}

if (!$loadedHelper) {
	die('File koneksi rahasia tidak ditemukan. Gunakan .terhubung atau .terhubung.php di folder configurasi.');
}

if (!class_exists('Database')) {
	die('Class Database tidak ditemukan pada file koneksi rahasia.');
}

$databaseConnector = new Database();
$db = $databaseConnector->connect();

// Alias untuk kompatibilitas file lama yang memakai variabel $conn.
$conn = $db;

?>