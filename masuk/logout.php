<?php
  session_start();
  
  // Catat logout time
  if (isset($_SESSION['idadmin'])) {
      include "../configurasi/koneksi.php";
      $logout_time = date("Y-m-d H:i:s");
      $session_id = session_id();
      $stmt = $db->prepare("UPDATE user_login_logs SET logout_time = ? WHERE session_id = ? AND logout_time IS NULL");
      $stmt->execute([$logout_time, $session_id]);
  }
  
  session_destroy();
  header('location:index.php');
?>
