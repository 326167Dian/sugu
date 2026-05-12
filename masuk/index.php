<?php
include "../configurasi/koneksi.php";
include "../configurasi/fungsi_indotgl.php";
session_start();
if (!empty($_SESSION['namauser']) and !empty($_SESSION['passuser'])) {
  echo "<script type='text/javascript'>window.location='media_admin.php?module=home'</script>";
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MYSIFA</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/iCheck/square/blue.css">


  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body class="hold-transition login-page">

  <div class="login-box">

    <div class="login-box-body">
      <div class="container-fluid text-center">
        <img align="center" src="images/mysifalogo.png">
        <?php
        $nama_apotek = $db->query("SELECT * from setheader ");
        $r = $nama_apotek->fetch(PDO::FETCH_ASSOC);
        ?>

        <h3 style="text-align:center"><b> <?= $r['satu']  ?> </b></h3>
        <center><h5>Versi e-commerce 2025</h5></center>
      </div>


      <form action="cek_login.php" method="post">
        <div class="form-group has-feedback">
          <input type="text" class="form-control" placeholder="Username" name="username" autofocus>
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
          <input type="password" class="form-control" placeholder="Password" name="password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
          <div class="col-xs-4">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
          </div><!-- /.col -->
          <div class="col-xs-4"></div>
        </div>
      </form>
      <?php
      $saatini = tgl_indo(date('Y-m-d'));
      ?>
      <center>BEKASI, <?= $saatini ?></center>
     


    </div><!-- /.login-box-body -->

  </div><!-- /.login-box -->

  <!-- jQuery 2.1.4 -->
  <script src="../../plugins/jQuery/jQuery-2.1.4.min.js"></script>
  <!-- Bootstrap 3.3.5 -->
  <script src="../../bootstrap/js/bootstrap.min.js"></script>
  <!-- iCheck -->
  <script src="../../plugins/iCheck/icheck.min.js"></script>

  <script>
    $(function() {
      $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
      });
    });
  </script>

</body>


</html>