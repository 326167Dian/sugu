<?php
include "../configurasi/koneksi.php";

if ($_SESSION['leveluser']=='admin'){
  $sql = $db->query("select * from modul where aktif='Y' and type = 'Report' order by urutan");
  while ($m = $sql->fetch(PDO::FETCH_ASSOC)){
    echo "<li class='garisbawah'><a href='$m[link]'><i class='fa fa-circle-o'></i>$m[nama_modul]</a></li>";
  }
}
elseif ($_SESSION['leveluser']=='pengajar'){
  $sql = $db->query("select * from modul where status='pengajar' and aktif='Y' and type = 'Report' order by urutan");
  while ($m = $sql->fetch(PDO::FETCH_ASSOC)){
    echo "<li class='garisbawah'><a href='$m[link]'><i class='fa fa-circle-o'></i>$m[nama_modul]</a></li>";
  }
}
?>
