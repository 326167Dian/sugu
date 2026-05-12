<?php
session_start();
error_reporting(0);
include "timeout.php";
include "../configurasi/koneksi.php";

//$request = mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT stt_trpermintaan FROM trpermintaan WHERE stt_trpermintaan != 'Selesai'"));


if ($_SESSION['login'] == 1) {
	if (!cek_login()) {
		$_SESSION['login'] = 0;
	}
}
if ($_SESSION['login'] == 0) {
	header('location:logout.php');
} else {
	if (empty($_SESSION['username']) and empty($_SESSION['passuser']) and $_SESSION['login'] == 0) {
		echo "<link href=css/style.css rel=stylesheet type=text/css>";
		echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
	} else {

?>
		<html>
		<head>
			<title>SMART INVENTORY FOR APOTEK</title>

			<!-- Bootstrap -->
			<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
			<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
			<link rel="stylesheet" href="dist/css/AdminLTE.min.css">

			<link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
			<link rel="stylesheet" href="plugins/iCheck/flat/blue.css">
			<link rel="stylesheet" href="plugins/morris/morris.css">
			<link rel="stylesheet" href="plugins/jvectormap/jquery-jvectormap-1.2.2.css">
			<link rel="stylesheet" href="plugins/datepicker/datepicker3.css">
			<link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
			<link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
			<link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
			<style>
			    .button__badge {
                  background-color: #fa3e3e;
                  border-radius: 2px;
                  color: white;
                 
                  padding: 1px 3px;
                  font-size: 12px;
                  
                  position: absolute; /* Position the badge within the relatively positioned button */
                  top: 0;
                  right: 0;
                }
                
                .form-footer-fixed{
                    position: fixed;
                    bottom: 0;
                    left: 230px; /* jika pakai sidebar adminLTE */
                    right: 0;
                    background: #fff;
                    padding: 15px;
                    border-top: 1px solid #ddd;
                    z-index: 999;
                    box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
                }
                
                .autocomplete-wrapper{
                    position:relative;
                }
                
                .autocomplete-panel{
                    position:absolute;
                    top:100%;
                    left:0;
                    width:100%;
                    background:#fff;
                    border:1px solid #ddd;
                    border-radius:6px;
                    box-shadow:0 4px 10px rgba(0,0,0,0.15);
                    z-index:9999;
                    max-height:250px;
                    overflow-y:auto;
                    display:none;
                }
                
                .autocomplete-item{
                    padding:10px;
                    cursor:pointer;
                    border-bottom:1px solid #f1f1f1;
                }
                
                .autocomplete-item:hover{
                    background:#9fc5e8;
                }
                
                .autocomplete-item.active{
                    background:#3c8dbc;
                    color:#fff;
                }
                
                .autocomplete-header{
                    padding:8px;
                    font-weight:bold;
                    background:#f5f5f5;
                }
                
                .autocomplete-empty{
                    padding:10px;
                    text-align:center;
                    color:#888;
                }
                
                /*Text Berjalan*/
                .marquee {
                  width: 100%;
                  overflow: hidden;
                  white-space: nowrap;
                  box-sizing: border-box;
                }
                
                .marquee span {
                  display: inline-block;
                  padding-left: 100%;
                  animation: marquee 20s linear infinite;
                  font-weight: bold;
                }
                
                @keyframes marquee {
                  0%   { transform: translateX(0); }
                  100% { transform: translateX(-100%); }
                }
			</style>

			<script src="vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
			<script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
			<!-- DataTables -->
			<script src="plugins/datatables/jquery.dataTables.min.js"></script>
			<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
			<script src="bootstrap/js/bootstrap.min.js"></script>
			<script src="assets/scripts.js"></script>
			<script>
				var IMAGE_URL = 'images/';
			</script>

			<script type="text/javascript" src="js/jquery.validate.min.js"></script>
			<script type="text/javascript" src="js/superfish.js"></script>
			<script type="text/javascript" src="js/cufon-yui.js"></script>
			<script type="text/javascript" src="js/Delicious_500.font.js"></script>
			<script type="text/javascript" src="js/jquery.flot.min.js"></script>
			<script type="text/javascript" src="js/jquery.cookie.js"></script>
			<script type="text/javascript" src="js/switcher.js"></script>
			<script type="text/javascript" src="js/jquery-ui.js"></script>
			<script type="text/javascript" src="js/tabcontent.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<script>
				$(function() {
					$("#tabs").tabs();
				});
			</script>

			<link rel="shortcut icon" type="image/x-icon" href="images/lp3i.png">
		</head>

		<body class="hold-transition skin-blue-light sidebar-mini">
		<?php
    		$nama_apotek = $db->query("SELECT * from setheader ");
            $r = $nama_apotek->fetch(PDO::FETCH_ASSOC);
        ?>
			<div class="wrapper">

				<header class="main-header">
					<!-- Logo -->
					<a href="#" class="logo">

						<!-- mini logo for sidebar mini 50x50 pixels -->
						<span class="logo-mini"><marquee><b><?= $r['satu']  ?></b></marquee></span>

						<!-- logo for regular state and mobile devices -->
						<span class="logo-lg"><marquee><b><?= $r['satu']  ?></b></marquee></span>
					</a>

					<!-- Header Navbar: style can be found in header.less -->
					<nav class="navbar navbar-static-top" role="navigation">

						<!-- Sidebar toggle button-->
						<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
							<span class="sr-only">Toggle navigation</span>
						</a>

						<div class="navbar-custom-menu">
							<ul class="nav navbar-nav">
								<!-- Messages: style can be found in dropdown.less-->

								<!-- Notifications: style can be found in dropdown.less -->


								<!-- Tasks: style can be found in dropdown.less -->

								<!-- User Account: style can be found in dropdown.less -->
								<li class="dropdown user user-menu">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" id="showicon">
										<i class="fa-solid fa-bell"></i>
										<span class="button__badge"><div id="count"></div></span>
									</a>
									<ul class="dropdown-menu" id="show-dropdown"></ul>
								</li>
								<li class="dropdown user user-menu">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">
										<img src="dist/img/mysifalogokecil.png" class="user-image" alt="User Image">
										<span class="hidden-xs">
											<?php echo $_SESSION['namauser']; ?>
										</span>
									</a>
									<ul class="dropdown-menu">
										<!-- User image -->
										<li class="user-header">
											<img src="dist/img/mysifalogokecil.png" class="img-circle" alt="User Image">

										</li>
										<!-- Menu Body -->

										<!-- Menu Footer-->
										<li class="user-footer">
											<div class="pull-left">
												<a href="?module=profil&act=editprofil&id=<?php echo $_SESSION['idadmin']; ?>" class="btn btn-default btn-flat">Profile</a>
											</div>
											<div class="pull-right">
												<a href="logout.php" class="btn btn-default btn-flat">Sign out</a>
											</div>
										</li>
									</ul>
								</li>
								<!-- Control Sidebar Toggle Button -->

							</ul>
						</div>
					</nav>

				</header>
				<!-- Left side column. contains the logo and sidebar -->
				<aside class="main-sidebar">
					<!-- sidebar: style can be found in sidebar.less -->
					<section class="sidebar">
						<!-- Sidebar user panel -->
						<div class="user-panel">
							<div class="pull-left image">
								<img src="dist/img/mysifalogokecil.png" class="img-circle" alt="User Image">
							</div>
							<div class="pull-left info">
								<marquee><p><?php echo $_SESSION['namalengkap'] ?> </p></marquee>
								<a href="#"><i class="fa fa-circle text-success"></i> Online</a>
							</div>
						</div>


						<!-- /.search form -->
						<!-- sidebar menu: : style can be found in sidebar.less -->
						<ul class="sidebar-menu">

							<li>
								<a href="?module=home">
									<i class="fa fa-th"></i> <span>Dashboard</span>
								</a>
							</li>

							<li class="treeview">
								<a href="#">
									<i class="glyphicon glyphicon-list"></i> <span>Data Master</span> <i class="fa fa-angle-left pull-right"></i>
								</a>
								<ul class="treeview-menu">
									<?php if ($_SESSION['mpengguna'] == "Y") { ?><li><a href="?module=admin"><i class="glyphicon glyphicon-user"></i> Operator</a></li><?php } ?>
									<?php if ($_SESSION['mheader'] == "Y") { ?><li><a href="?module=setheader"><i class="glyphicon glyphicon-align-center"></i> Header Struk</a></li><?php } ?>
									<?php if ($_SESSION['mjenisbayar'] == "Y") { ?><li><a href="?module=carabayar"><i class="glyphicon glyphicon-barcode"></i> Jenis Pembayaran</a></li><?php } ?>
									<?php if ($_SESSION['mpelanggan'] == "Y") { ?><li><a href="?module=pelanggan"><i class="fa fa-users"></i> Pelanggan</a></li><?php } ?>
									<?php if ($_SESSION['msupplier'] == "Y") { ?><li><a href="?module=supplier"><i class="fa fa-truck"></i> Supplier</a></li><?php } ?>
									<?php if ($_SESSION['msatuan'] == "Y") { ?><li><a href="?module=satuan"><i class="glyphicon glyphicon-flag"></i> Satuan</a></li><?php } ?>
									<?php if ($_SESSION['mjenisobat'] == "Y") { ?><li><a href="?module=jenisobat"><i class="glyphicon glyphicon-tags"></i> Jenis Obat & Rak Obat</a></li><?php } ?>
									<?php if ($_SESSION['mbarang'] == "Y") { ?><li><a href="?module=barang"><i class="glyphicon glyphicon-book"></i> Item Barang</a></li><?php } ?>
									<?php if ($_SESSION['komisi'] == "Y") { ?><li><a href="?module=komisi"><i class="glyphicon glyphicon-usd"></i> Komisi Pegawai</a></li><?php } ?>
								</ul>
							</li>
							<li class="treeview">
								<a href="#">
									<i class="glyphicon glyphicon-shopping-cart"></i> <span>Inventory</span> <i class="fa fa-angle-left pull-right"></i>

								</a>
								<ul class="treeview-menu">
									<?php if ($_SESSION['mstok'] == "Y") { ?><li><a href="?module=lapstok"><i class="glyphicon glyphicon-usd"></i> Nilai Stok & Traffic Barang </a></li><?php } ?>
									<?php if ($_SESSION['stok_kritis'] == "Y") { ?><li><a href="?module=stok_kritis"><i class="glyphicon glyphicon-log-in"></i> Stok Kritis </a></li><?php } ?>
									<?php if ($_SESSION['stokopname'] == "Y") { ?><li><a href="?module=stokopname"><i class="glyphicon glyphicon-share"></i> Stok Opname Bulanan </a></li><?php } ?>
									<?php if ($_SESSION['soharian'] == "Y") { ?><li><a href="?module=soharian"><i class='glyphicon glyphicon-print'></i> Stok Opname Harian</a></li><?php } ?>
									<?php //if ($_SESSION['koreksistok'] == "Y") { ?>
									<!--<li><a href="?module=koreksistok"><i class='glyphicon glyphicon-pencil'></i> Koreksi Stok</a></li>-->
									<?php //} ?>
								    <?php if ($_SESSION['kartustok'] == "Y") { ?><li><a href="?module=kartustok"><i class='glyphicon glyphicon-transfer'></i> Kartu Stok</a></li><?php } ?>
								    <?php if ($_SESSION['jurnalkas'] == "Y") { ?><li><a href="?module=jurnalkas"><i class='glyphicon glyphicon-usd'></i> Jurnal Kas</a></li><?php } ?>
                                </ul>
							</li>
							
							<li class="treeview">
								<a href="#">
									<i class="fa fa-percent"></i> <span>Program Promo</span><i class="fa fa-angle-left pull-right"></i>
								</a>
								<ul class="treeview-menu">
									<li><a href="?module=bundle"><i class='glyphicon glyphicon-tags'></i> Bundle/Paket Produk</a></li>
								</ul>
							</li>

							<li class="treeview">
								<a href="#">
									<i class="glyphicon glyphicon-compressed"></i> <span>Transaksi</span><i class="fa fa-angle-left pull-right"></i>
								</a>
								<ul class="treeview-menu">
									<?php if ($_SESSION['orders'] == "Y") { ?><li><a href="?module=orders"><i class="glyphicon glyphicon-share"></i> Pesan Barang </a></li><?php } ?>
									<?php if ($_SESSION['tbm'] == "Y") { ?><li><a href="?module=trbmasuk"><i class="glyphicon glyphicon-download-alt"></i> Barang Masuk non PBF</a></li><?php } ?>
									<?php if ($_SESSION['tbmpbf'] == "Y") { ?><li><a href="?module=trbmasukpbf"><i class="glyphicon glyphicon-download-alt"></i> Barang Masuk dari PBF</a></li><?php } ?>
									<?php if ($_SESSION['byrkredit'] == "Y") { ?><li><a href="?module=byrkredit"><i class="glyphicon glyphicon-check"></i> Edit/Retur/Hapus Pembelian</a></li><?php } ?>
                                    <?php if ($_SESSION['cekdarah'] == "Y") { ?><li><a href="?module=cekdarah"><i class="glyphicon glyphicon-tint"></i> Cek Darah</a></li><?php } ?>
                                    <?php if ($_SESSION['shiftkerja'] == "Y") { ?><li><a href="?module=shiftkerja"><i class="glyphicon glyphicon-level-up"></i> OPEN/TUTUP KASIR</a></li><?php } ?>
									<?php if ($_SESSION['tpk'] == "Y") { ?><li><a href="?module=trkasir"><i class="glyphicon glyphicon-inbox"></i> Penjualan/Kasir</a></li><?php } ?>
									<?php if ($_SESSION['penjualansebelum'] == "Y") { ?><li><a href="?module=penjualansebelumnya"><i class="glyphicon glyphicon-inbox"></i> Edit/Retur/Hapus Penjualan</a></li><?php } ?>
									<?php if ($_SESSION['catatan'] == "Y") { ?><li><a href="?module=catatan"><i class='glyphicon glyphicon-pencil'></i> Catatan</a></li><?php } ?>
								</ul>
							</li>

                            
							<li class="treeview">
								<a href="#">
									<i class="glyphicon glyphicon-file"></i> <span>Laporan</span> <i class="fa fa-angle-left pull-right"></i>
								</a>
								<ul class="treeview-menu">
								    <li><a href="?module=lapbatch" ><i class='glyphicon glyphicon-print'></i> Laporan Dekat Expired</a></li>
									<?php if ($_SESSION['lpitem'] == "Y") { ?><li><a href="modul/mod_laporan/cetak_barang.php" target="_blank"><i class='glyphicon glyphicon-print'></i> Item Barang</a></li><?php } ?>
									<?php if ($_SESSION['lpbrgmasuk'] == "Y") { ?><li><a href="?module=lapbrgmasuk"><i class='glyphicon glyphicon-print'></i> Barang Masuk</a></li><?php } ?>
									<?php if ($_SESSION['lpkasir'] == "Y") { ?><li><a href="?module=lappenjualan"><i class='glyphicon glyphicon-print'></i> Penjualan</a></li><?php } ?>
									<?php if ($_SESSION['labapenjualan'] == "Y") { ?><li><a href="?module=labapenjualan"><i class='glyphicon glyphicon-print'></i> Laba Penjualan</a></li><?php } ?>
									<?php if ($_SESSION['labajenisobat'] == "Y") { ?><li><a href="?module=labajenisobat"><i class='glyphicon glyphicon-print'></i> Jenis Penjualan </a></li><?php } ?>
									<?php if ($_SESSION['lpsupplier'] == "Y") { ?><li><a href="modul/mod_laporan/cetak_supplier.php" target="_blank"><i class='glyphicon glyphicon-print'></i> Data Supplier</a></li><?php } ?>
									<?php if ($_SESSION['lppelanggan'] == "Y") { ?><li><a href="modul/mod_laporan/cetak_pelanggan.php" target="_blank"><i class='glyphicon glyphicon-print'></i> Data Pelanggan</a></li><?php } ?>
									<?php if ($_SESSION['neraca'] == "Y") { ?><li><a href="?module=neraca"><i class='glyphicon glyphicon-print'></i> Neraca Laba Rugi</a></li><?php } ?>
									<?php if ($_SESSION['level'] == "pemilik" and $_SESSION['komisi'] == "Y") { ?><li><a href="?module=lapkomisi"><i class='glyphicon glyphicon-print'></i> Komisi Pegawai</a></li><?php } ?>
									<?php if ($_SESSION['level'] == "pemilik") { ?><li><a href="?module=evaluasi"><i class='glyphicon glyphicon-print'></i> Evaluasi Pegawai</a></li><?php } ?>
									<?php if ($_SESSION['level'] == "pemilik") { ?><li><a href="?module=lapstokopname"><i class='glyphicon glyphicon-print'></i> Stok Opname</a></li><?php } ?>
								</ul>
							</li>

						</ul>
					</section>
					<!-- /.sidebar -->
				</aside>
				<div class="content-wrapper">
				    <?php if(date('m',time()) == '03'): ?>
				    <div class="marquee bg-warning">
                      <span>Segenap Keluarga Besar MySIFA Mengucapkan Selamat Idul Fitri  1447 H. Taqoballahu minna wa minkum. Mohon maaf Lahir dan Batin.</span>
                    </div>
                    <?php endif; ?>
                    
					<section class="content-header">
						<h1>
							Dashboard
							<small>Control panel</small>
						</h1>
						<ol class="breadcrumb">
							<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
							<li class="active">Dashboard</li>
						</ol>
					</section>

					<section class="content">
						<div class="box box-default color-palette-box">
							<div class="box-body">
								<?php include "content_admin.php"; ?>
							</div>
						</div>
					</section>
				</div>
			</div>


		</body>

		</html>
<?php

	}
}
?>
<!--/.fluid-container-->
<!-- jQuery 2.1.4 -->
<script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.5 -->
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
	$.widget.bridge('uibutton', $.ui.button);
</script>
<!-- <script src="bootstrap/js/bootstrap.min.js"></script>-->
<!-- Morris.js charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="plugins/morris/morris.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/knob/jquery.knob.js"></script>
<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="plugins/ckeditor/ckeditor.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            
<!-- page script -->
<script>
	$(function() {
		$("#example1").DataTable();
		$("#example3").DataTable();
		$("#example4").DataTable();
		$('#example2').DataTable({
			"paging": true,
			"lengthChange": true,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": true
		});
	});

	function formatRupiah(num) {
		let rupiahFormat = new Intl.NumberFormat('id-ID', {
			currency: 'IDR',
		}).format(num);

		return rupiahFormat;
	}

    function unformatRupiah(angka){
        return angka.replace(/[^0-9]/g, '');
    }
// 	setInterval(function() {
// 		$.ajax({
// 			type: 'post',
// 			url: "cek_notification.php?act=icon",
// 			success: function(data) {
// 			    // 1. Mendapatkan elemen dengan ID "paragrafSaya"
//                 const elemen = document.getElementById("count");
                
//                 // 2. Mengubah konten menggunakan innerHTML
//                 elemen.innerHTML = data;

// 			}
// 		});
// 	}, 300000);
	
// 	setTimeout(function() {
// 		$.ajax({
// 			type: 'post',
// 			url: "cek_notification.php?act=icon",
// 			success: function(data) {
//                 const elemen = document.getElementById("count");
//                 elemen.innerHTML = data;
// 			}
// 		});
// 	}, 500);
	
// 	$(document).on('click', '#showicon', function(e){
//         $.ajax({
// 			type: 'post',
// 			url: "cek_notification.php?act=data",
// 			success: function(data) {
// 			    data = $.parseJSON(data);
// 			    let teksGabungan = "";
// 			    for (let i = 0; i < data.length; i++) {
//                     teksGabungan += '<li class="dropdown-item" ><a href="#" style="color:black; font-size:13px; margin-bottom:5px;">Kode Pesanan: '+data[i]['kode']+'<br><span style="font-size:11px">Created at '+data[i]['date']+'</span></a><hr style="margin-bottom:0px; margin-top:0px"></li>';
//                 }
//                 document.getElementById("show-dropdown").innerHTML = teksGabungan;
// 			 //   console.log(teksGabungan);
// 			}
// 		});
// 	});
</script>