<?php
include "../configurasi/koneksi.php";
include "../configurasi/fungsi_logs.php";

$db = isset($db) ? $db : null;
if (!$db instanceof PDO) {
    echo "<link href=css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Koneksi database tidak tersedia.</div>";
    exit;
}
// function anti_injection($data)
// {
//   $filter = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes(strip_tags(htmlspecialchars($data, ENT_QUOTES))));
//   return $filter;
// }

// $username = anti_injection($_POST['username']);
// $pass     = anti_injection(md5($_POST['password']));

// // pastikan username dan password adalah berupa huruf atau angka.
// if (!ctype_alnum($username) or !ctype_alnum($pass)) {
//   echo "<link href=css/style.css rel=stylesheet type=text/css>";
//   echo "<div class='error msg'>Injeksi Gagal</div>";
// } else {
//   $login = mysqli_query(
//     $GLOBALS["___mysqli_ston"],
//     "SELECT * FROM admin WHERE username='$username' AND password='$pass' AND blokir='N'"
//   );
//   $ketemu = mysqli_num_rows($login);
//   $r = mysqli_fetch_array($login);

//   // Apabila username dan password ditemukan
//   if ($ketemu > 0) {
//     session_start();
//     echo $ketemu;
//     include "timeout.php";

//     $_SESSION['idadmin']    = $r['id_admin'];
//     $_SESSION['username']    = $r['username'];
//     $_SESSION['namauser']    = $r['username'];
//     $_SESSION['namalengkap'] = $r['nama_lengkap'];
//     $_SESSION['passuser']    = $r['password'];
//     $_SESSION['leveluser']   = "admin";
//     $_SESSION['mpengguna']   = $r['mpengguna'];
//     $_SESSION['mheader']   = $r['mheader'];
//     $_SESSION['mjenisbayar']     = $r['mjenisbayar'];
//     $_SESSION['mpelanggan']     = $r['mpelanggan'];
//     $_SESSION['msupplier']     = $r['msupplier'];
//     $_SESSION['msatuan']   = $r['msatuan'];
//     $_SESSION['mjenisobat']   = $r['mjenisobat'];
//     $_SESSION['mbarang']      = $r['mbarang'];
//     $_SESSION['tbm']    = $r['tbm'];
//     $_SESSION['tbmpbf']    = $r['tbmpbf'];
//     $_SESSION['tpk']    = $r['tpk'];
//     $_SESSION['lpitem'] = $r['lpitem'];
//     $_SESSION['lpbrgmasuk'] = $r['lpbrgmasuk'];
//     $_SESSION['lpkasir'] = $r['lpkasir'];
//     $_SESSION['lpsupplier'] = $r['lpsupplier'];
//     $_SESSION['lppelanggan'] = $r['lppelanggan'];
//     $_SESSION['mstok'] = $r['mstok'];
//     $_SESSION['stok_kritis'] = $r['stok_kritis'];
//     $_SESSION['orders'] = $r['orders'];
//     $_SESSION['penjualansebelum'] = $r['penjualansebelum'];
//     $_SESSION['labapenjualan'] = $r['labapenjualan'];
//     $_SESSION['byrkredit'] = $r['byrkredit'];
//     $_SESSION['stokopname'] = $r['stokopname'];
//     $_SESSION['soharian'] = $r['soharian'];
//     $_SESSION['labajenisobat'] = $r['labajenisobat'];
//     $_SESSION['koreksistok'] = $r['koreksistok'];
//     $_SESSION['shiftkerja'] = $r['shiftkerja'];
//     $_SESSION['neraca'] = $r['neraca'];
//     $_SESSION['level'] = $r['akses_level'];
//     $_SESSION['komisi'] = $r['komisi'];
//     $_SESSION['catatan'] = $r['catatan'];
//     $_SESSION['cekdarah'] = $r['cekdarah'];

//     // session timeout
//     $_SESSION['login'] = 1;
//     timer();

//     $sid_lama = session_id();

//     session_regenerate_id();

//     $sid_baru = session_id();


//     insertlogs($r['id_admin'], $r['nama_lengkap'], "Masuk Login");
//     header('location:media_admin.php?module=home');
//   } else {
//     echo "<link href=css/style.css rel=stylesheet type=text/css>";
//     echo "<div class='error msg'>Login Gagal, Username atau Password salah, atau account anda sedang di blokir. ";
//     echo "<a href=index.php><b>ULANGI LAGI</b></a></center></div>";
//   }
// }

$username = $_POST['username'];
        $password = $_POST['password'];
    
        // Ambil data user dari database
        $sql = "SELECT * FROM admin WHERE username = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$username]);
    
        if ($stmt->rowCount() === 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $hashedPassword = $row['password'];
    
            // Verifikasi password
            if (password_verify($password, $hashedPassword)) {
                // Cek apakah akun diblokir
                if ($row['blokir'] == 'N') {
                    // Login berhasil
                    session_start();
                    include "timeout.php";
                
                    $_SESSION['id_admin']    = $row['id_admin'];
                    $_SESSION['idadmin']    = $row['id_admin'];
                    $_SESSION['username']    = $row['username'];
                    $_SESSION['namauser']    = $row['username'];
                    $_SESSION['namalengkap'] = $row['nama_lengkap'];
                    $_SESSION['passuser']    = $row['password'];
                    $_SESSION['leveluser']   = "admin";
                    $_SESSION['mpengguna']   = $row['mpengguna'];
                    $_SESSION['mheader']   = $row['mheader'];
                    $_SESSION['mjenisbayar']     = $row['mjenisbayar'];
                    $_SESSION['mpelanggan']     = $row['mpelanggan'];
                    $_SESSION['msupplier']     = $row['msupplier'];
                    $_SESSION['msatuan']   = $row['msatuan'];
                    $_SESSION['mjenisobat']   = $row['mjenisobat'];
                    $_SESSION['mbarang']      = $row['mbarang'];
                    $_SESSION['tbm']    = $row['tbm'];
                    $_SESSION['tbmpbf']    = $row['tbmpbf'];
                    $_SESSION['tpk']    = $row['tpk'];
                    $_SESSION['lpitem'] = $row['lpitem'];
                    $_SESSION['lpbrgmasuk'] = $row['lpbrgmasuk'];
                    $_SESSION['lpkasir'] = $row['lpkasir'];
                    $_SESSION['lpsupplier'] = $row['lpsupplier'];
                    $_SESSION['lppelanggan'] = $row['lppelanggan'];
                    $_SESSION['mstok'] = $row['mstok'];
                    $_SESSION['stok_kritis'] = $row['stok_kritis'];
                    $_SESSION['orders'] = $row['orders'];
                    $_SESSION['penjualansebelum'] = $row['penjualansebelum'];
                    $_SESSION['labapenjualan'] = $row['labapenjualan'];
                    $_SESSION['byrkredit'] = $row['byrkredit'];
                    $_SESSION['stokopname'] = $row['stokopname'];
                    $_SESSION['soharian'] = $row['soharian'];
                    $_SESSION['labajenisobat'] = $row['labajenisobat'];
                    $_SESSION['koreksistok'] = $row['koreksistok'];
                    $_SESSION['shiftkerja'] = $row['shiftkerja'];
                    $_SESSION['neraca'] = $row['neraca'];
                    $_SESSION['level'] = $row['akses_level'];
                    $_SESSION['komisi'] = $row['komisi'];
                    $_SESSION['kartustok'] = $row['kartustok'];
                    $_SESSION['catatan'] = $row['catatan'];
                    $_SESSION['cekdarah'] = $row['cekdarah'];
                    $_SESSION['jurnalkas'] = $row['jurnalkas'];
                
                    // session timeout
                    $_SESSION['login'] = 1;
                    timer();
                
                    $sid_lama = session_id();
                
                    session_regenerate_id();
                
                    $sid_baru = session_id();
                  
                
                    insertlogs($row['id_admin'], $row['nama_lengkap'], "Masuk Login");
                    
                    // Catat login time
                    $login_time = date("Y-m-d H:i:s");
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                    $session_id = session_id();
                    $stmt_log = $db->prepare("INSERT INTO user_login_logs (user_id, username, login_time, ip_address, session_id) VALUES (?, ?, ?, ?, ?)");
                    $stmt_log->execute([$row['id_admin'], $row['username'], $login_time, $ip_address, $session_id]);

                    $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
                    $isMobileClient = (bool)preg_match('/Android|iPhone|iPad|iPod|IEMobile|Opera Mini|Mobile/i', $userAgent);

                    if ($isMobileClient) {
                        header('location:media_mobile.php?module=home');
                    } else {
                        header('location:media_admin.php?module=home');
                    }
                } else {
                    echo "<link href=css/style.css rel=stylesheet type=text/css>";
                    echo "<div class='error msg'>Akun Anda diblokir. Hubungi administrator.</div>";
                }
            } else {
                echo "<link href=css/style.css rel=stylesheet type=text/css>";
                echo "<div class='error msg'>Password salah.</div>";
            }
        } else {
            echo "<link href=css/style.css rel=stylesheet type=text/css>";
            echo "<div class='error msg'>Username tidak ditemukan.</div>";
        }

