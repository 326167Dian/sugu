<?php
session_start();
include "../../../configurasi/koneksi.php";
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

	$aksi = "modul/mod_bundle/aksi_bundle.php";
	$aksi_barang = "masuk/modul/mod_bundle/aksi_bundle.php";
	switch ($_GET['act']) {
			// Tampil barang
		default:

	
?>


			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">Paket Produk (Bundling)</h3>
					<div class="box-tools pull-right">
					    <a class="btn btn-success btn-flat" href="?module=bundle&act=tambah"><i class="fa fa-fw fa-plus-circle"></i> TAMBAH</a>
						<!--<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>-->
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body table-responsive">
					
					
					<table id="bundle" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th style="text-align: center; ">No</th>
								<th>Nama Paket Produk</th>
								<th style="text-align: center; ">Isi Paket</th>
								<th style="text-align: center; ">Harga Jual</th>
								<th style="text-align: center; ">Sisa Kuota</th>
								<th style="text-align: center; white-space:nowrap; width:95px; min-width:95px;">Aksi</th>
								
							</tr>
						</thead>
						<tbody>
						    <?php
						        
						        $stmt_bundle = $db->prepare("SELECT * FROM bundle ORDER BY id_bundle DESC");
						        $stmt_bundle->execute();
						        $no = 1;
						        
						        while($r = $stmt_bundle->fetch(PDO::FETCH_ASSOC)):
						            
						            $stmt_bundle_detail = $db->prepare("SELECT * FROM bundle_detail WHERE kd_bundle = ?");
						            $stmt_bundle_detail->execute([$r['kd_bundle']]);
						    ?>
						    
						    <tr>
						        <td style="text-align: center;"><?=$no?>.</td>    
						        <td><?=$r['nm_bundle']?></td>    
						        <td style="text-align: left">
						            <ul>
						            <?php while($r1 = $stmt_bundle_detail->fetch(PDO::FETCH_ASSOC)):?>
						                <li><?=$r1['nm_barang']?></li>
						            <?php endwhile;?>      
						            </ul>
						        </td>    
						        <td style="text-align:right"><?=format_rupiah($r['hrgjual_bundle'])?></td>    
						        <td style="text-align:center"><?=$r['qty_bundle']?></td>    
						        <td style="text-align: center">
						            <div class='dropdown' style='white-space:nowrap; display:inline-block;'>
                                        <button class='btn btn-default dropdown-toggle' type='button' id='dropdownMenuAksi" . $value[' id_barang '] . "' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true' style='white-space:nowrap;'>
                                            action
                                            <span class='caret'></span>
                                        </button>
                                        <ul class='dropdown-menu' aria-labelledby='dropdownMenuAksi" . $value[' id_barang '] . "' style='min-width:165px; padding:6px 6px; left:0; right:auto;'>
                                            <li style='margin:0 0 4px 0; background:transparent; text-align:left;'><a href='?module=bundle&act=edit&id=<?=$r['id_bundle']?>' style='display:block; width:70%; margin:0; box-sizing:border-box; padding:4px 8px; background-color:yellow; color:#555; white-space:nowrap;'>EDIT</a></li>
                                            <li style='margin:0 0 4px 0; background:transparent; text-align:left;'><a href='?module=bundle&act=detail&id=<?=$r['id_bundle']?>' style='display:block; width:70%; margin:0; box-sizing:border-box; padding:4px 8px; background-color:aqua; color:#555; white-space:nowrap;'>DETAIL</a></li>
                                            <li style='margin:0; background:transparent; text-align:left;'><a href="javascript:confirmdelete('<?=$aksi?>?module=bundle&act=hapus_bundle&id=<?=$r['id_bundle']?>')" style='display:block; width:70%; margin:0; box-sizing:border-box; padding:4px 8px; background-color:red; color:#fff; white-space:nowrap;'>HAPUS</a></li>
                                        </ul>
                                    </div>
						        </td>    
						    </tr>
						    
						    <?php 
						            $no++;
						        endwhile;
						    ?>
						</tbody>

					</table>

					
				</div>
			</div>

            <script>
                $("#bundle").DataTable();
            </script>
<?php

			break;

		case "tambah":

?>			
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">TAMBAH PAKET PRODUK </h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body table-responsive" style="height:100%">
                    <form method="POST" action="<?=$aksi?>?module=bundle&act=input_bundle" enctype="multipart/form-data" class="form-horizontal" id="form_bundle">
            
                        <div class="row">
                            <div class="col-lg-6">
                                <label>Nama Paket Produk</label>
                                <input type="text" name="nm_bundle" id="nm_bundle" class="form-control" aria-label="..." placeholder="Nama Paket Produk" required="">
                                
                                <!-- /input-group -->
                            </div>
                            <!-- /.col-lg-6 -->
                            <div class="col-lg-6">
                                <label>Satuan</label>
                                <div class="input-group">
                                    <select class="form-control" name="sat_bundle" required>
                                        <option>Pilih Satuan</option>
                                        <?php
                                            $stmt = $db->prepare("SELECT * FROM satuan ORDER BY nm_satuan ASC");
                                            $stmt->execute();
                                            while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
                                                echo '<option value="'.$r['nm_satuan'].'">'.$r['nm_satuan'].'</option>';
                                            }
                                        ?>
                                    </select>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="add">Tambah Item</button>
                                    </span>
                                </div>
                                <!-- /input-group -->
                            </div>
                            <!-- /.col-lg-6 -->
                        </div>
                        <!-- /.row -->
                        <hr>
                        
                        <table class="table table-bordered" id="table_produk">
                            <thead>
                                <tr>
                                    <th width="3%" class="text-center">No.</th>
                                    <th>Nama Produk</th>
                                    <th>Quantity</th>
                                    <th>Satuan</th>
                                    <th>Opsi Harga</th>
                                    <th class="text-center">Harga Jual</th>
                                    <th class="text-center">Sub Total</th>
                                </tr>
                            </thead>
                
                            <tbody id="dynamic_field">
                                <tr class='row-obat'>
                                    <td class="text-center">
                                        <div id="nomor">1.</div>
                                    </td>
                                    <td>
                                        <!--<div class='row-obat'>-->
                                            <div class='autocomplete-wrapper'>
                                                <input type='hidden' name='obat_kd[]' class='obat-kd'>
        							            <input type='text' name='obat_nama[]' class='form-control obat-nama' placeholder='Nama obat (ketik lalu Enter)'>
                                                <div class='autocomplete-panel'></div>
                                            </div>
                                        <!--</div>-->
                                        
                                    </td>
                
                                    <td>
                                        <input type="number" name="qty[]" class="form-control qty" min="1" style="width:100px">
                                    </td>
                                    <td>
                                        <input type="text" name="sat_barang[]" class="form-control satuan" readonly>
                                    </td>
                                    <td>
                                        <select name="opsiharga" class="form-control opsiharga">
                                            <option value="harga1">Harga #1</option>
                                            <option value="custom">Custom</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="hrgjual[]" data-hrg class="form-control hrgjual" readonly style="text-align:right">
                                    </td>
                                    <td>
                                        <input type="text" name="subtotal[]" class="form-control subtotal" readonly style="text-align:right">
                                    </td>
                
                                    
                                </tr>
                            </tbody>
                            <tfoot>
                                <td colspan="2">Total :</td>
                                <td colspan="5" style="text-align:left"><div id="total"></div></td>
                            </tfoot>
                        </table>
                        <!-- PANEL BUTTON FIX BOTTOM -->
                        <div class="form-footer-fixed">
                            <div class="col-lg-2">
                                <label>Jumlah Kuota</label>
                            </div>
                            <div class="col-lg-6">
                                <label>Harga Jual Bundling</label>
                            </div>
                            
                            <div class="col-lg-4">
                                <label>&nbsp;</label>
                            </div> 
                            
                            <div class="col-lg-2">
                                <input type="text" name="qty_bundle" id="qty_bundle" class="form-control" aria-label="..." placeholder="Jumlah Kuota" required="">
                            </div>
                            <div class="col-lg-6">
                                <input type="hidden" name="total_item" id="total_item">
                                <input type="text" name="hrgjual_bundle" id="hrgjual_bundle" class="form-control" aria-label="..." placeholder="Harga Jual Bundling" required="">
                            </div>
                            
                            <div class="col-lg-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Simpan
                                </button>
                
                                <button type="button" class="btn btn-default"  onclick="self.history.back()">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </button>
                            </div>    
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
            $(document).ready(function(){
                
                
                var i = 1;
            
                $('#add').click(function(){
            
                    i++;
            
                    $('#dynamic_field').append(`
                        <tr class='row-obat' id="row`+i+`">
                            <td class="text-center">
                                <div id="nomor">`+i+`.</div>
                            </td>
                            <td>
                                <div class='autocomplete-wrapper'>
                                    <input type='hidden' name='obat_kd[]' class='obat-kd'>
        							<input type='text' name='obat_nama[]' class='form-control obat-nama' placeholder='Nama obat (ketik lalu Enter)'>
                                    <div class='autocomplete-panel'></div>
                                </div>
                            </td>
                
                            <td>
                                <input type="number" name="qty[]" class="form-control qty" min="1" style="width:100px">
                            </td>
                            
                            <td>
                                <input type="text" name="sat_barang[]" class="form-control satuan" readonly>
                            </td>
                            <td>
                                <select name="opsiharga" class="form-control opsiharga">
                                    <option value="harga1">Harga #1</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </td>
                            
                            <td>
                                <input type="text" name="hrgjual[]" class="form-control hrgjual" readonly style="text-align:right">
                            </td>
                            
                            <td>
                                <input type="text" name="subtotal[]" class="form-control subtotal" readonly style="text-align:right">
                            </td>
                        </tr>
                        `);
                
                    });
            
                $(document).on('click', '.btn_remove', function(){
                    var button_id = $(this).attr("id");
                    $('#row'+button_id).remove();
                });
            
            });
            </script>
<?php
			break;

		case "edit":
			$edit = $db->prepare("SELECT * FROM bundle WHERE id_bundle = ?");
			$edit->execute([$_GET['id']]);
			$r = $edit->fetch(PDO::FETCH_ASSOC);
			
?>

            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">UBAH PAKET PRODUK </h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body table-responsive" style="height:100%">
                    <form method="POST" action="<?=$aksi?>?module=bundle&act=update_bundle" enctype="multipart/form-data" class="form-horizontal" id="form_bundle">
            
                        <div class="row">
                            <div class="col-lg-6">
                                <label>Nama Paket Produk</label>
                                <input type="text" name="nm_bundle" id="nm_bundle" class="form-control" aria-label="..." placeholder="Nama Paket Produk"  value="<?=$r['nm_bundle']?>" required>
                                <input type="hidden" name="kd_bundle" id="kd_bundle" value="<?=$r['kd_bundle']?>" >
                                
                                <!-- /input-group -->
                            </div>
                            <!-- /.col-lg-6 -->
                            <div class="col-lg-6">
                                <label>Satuan</label>
                                <div class="input-group">
                                    <select class="form-control" name="sat_bundle" required>
                                        <option value="<?=$r['sat_bundle']?>"><?=$r['sat_bundle']?></option>
                                        <?php
                                            $stmt = $db->prepare("SELECT * FROM satuan ORDER BY nm_satuan ASC");
                                            $stmt->execute();
                                            while($rs = $stmt->fetch(PDO::FETCH_ASSOC)){
                                                echo '<option value="'.$rs['nm_satuan'].'">'.$rs['nm_satuan'].'</option>';
                                            }
                                        ?>
                                    </select>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="add">Tambah Item</button>
                                    </span>
                                </div>
                                <!-- /input-group -->
                            </div>
                            <!-- /.col-lg-6 -->
                        </div>
                        <!-- /.row -->
                        <hr>
                        
                        <table class="table table-bordered" id="table_produk">
                            <thead>
                                <tr>
                                    <th width="3%" class="text-center">No.</th>
                                    <th>Nama Produk</th>
                                    <th>Quantity</th>
                                    <th>Satuan</th>
                                    <th>Opsi Harga</th>
                                    <th class="text-center">Harga Jual</th>
                                    <th class="text-center">Sub Total</th>
                                </tr>
                            </thead>
                
                            <tbody id="dynamic_field">
                                <?php
                                    $stmt_bundle_detail = $db->prepare("SELECT * FROM bundle_detail WHERE kd_bundle = ?");
                                    $stmt_bundle_detail->execute([$r['kd_bundle']]);
                                    $count_detail = $stmt_bundle_detail->rowCount();
                                    $no = 1;
                                    while($r1 = $stmt_bundle_detail->fetch(PDO::FETCH_ASSOC)):
                                ?>
                                <tr class='row-obat'>
                                    <td class="text-center">
                                        <div id="nomor"><?=$no?>.</div>
                                    </td>
                                    <td>
                                        <div class='autocomplete-wrapper'>
                                            <input type='hidden' name='obat_kd[]' class='obat-kd' value="<?=$r1['kd_barang']?>">
        							        <input type='text' name='obat_nama[]' class='form-control obat-nama' placeholder='Nama obat (ketik lalu Enter)' value="<?=$r1['nm_barang']?>">
                                            <div class='autocomplete-panel'></div>
                                        </div>
                                        
                                    </td>
                
                                    <td>
                                        <input type="number" name="qty[]" class="form-control qty" min="1" style="width:100px" value="<?=$r1['qty_barang']?>">
                                    </td>
                                    <td>
                                        <input type="text" name="sat_barang[]" class="form-control satuan" value="<?=$r1['sat_barang']?>" readonly>
                                    </td>
                                    <td>
                                        <select name="opsiharga" class="form-control opsiharga">
                                            <option value="harga1">Harga #1</option>
                                            <option value="custom">Custom</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="hrgjual[]" data-hargaawal="<?=$r1['hrgjual_barang']?>" class="form-control hrgjual" value="<?=$r1['hrgjual_barang']?>" readonly style="text-align:right">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" name="subtotal[]" class="form-control subtotal" value="<?=format_rupiah($r1['subtotal'])?>" readonly style="text-align:right">
                                            <span class="input-group-btn">
                                                <button class="btn btn-danger" type="button" id="hapus" data-idbundle_detail="<?=$r1['idbundle_detail']?>">
                                                    <i class="fa fa-trash"></i>
                                                    Hapus
                                                </button>
                                            </span>
                                        </div>
                                        
                                    </td>
                
                                    
                                </tr>
                                <?php
                                    $no++;
                                    endwhile;
                                ?>
                            </tbody>
                            <tfoot>
                                <td colspan="2">Total :</td>
                                <td colspan="5" style="text-align:left"><div id="total"></div></td>
                            </tfoot>
                        </table>
                        <!-- PANEL BUTTON FIX BOTTOM -->
                        <div class="form-footer-fixed">
                            <div class="col-lg-2">
                                <label>Jumlah Kuota</label>
                            </div>
                            <div class="col-lg-6">
                                <label>Harga Jual Bundling</label>
                            </div>
                            
                            <div class="col-lg-4">
                                <label>&nbsp;</label>
                            </div> 
                            
                            <div class="col-lg-2">
                                <input type="text" name="qty_bundle" id="qty_bundle" class="form-control" aria-label="..." placeholder="Jumlah Kuota" value="<?=$r['qty_bundle']?>" required="">
                            </div>
                            <div class="col-lg-6">
                                <input type="hidden" name="total_item" id="total_item">
                                <input type="text" name="hrgjual_bundle" id="hrgjual_bundle" class="form-control" aria-label="..." placeholder="Harga Jual Bundling" value="<?=$r['hrgjual_bundle']?>" required="">
                            </div>
                            
                            <div class="col-lg-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Simpan
                                </button>
                
                                <button type="button" class="btn btn-default"  onclick="self.history.back()">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </button>
                            </div>    
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
            $(document).ready(function(){
            
                var i = <?=$count_detail?>;
            
                $('#add').click(function(){
            
                    i++;
            
                    $('#dynamic_field').append(`
                        <tr class='row-obat' id="row`+i+`">
                            <td class="text-center">
                                <div id="nomor">`+i+`.</div>
                            </td>
                            <td>
                                <div class='autocomplete-wrapper'>
                                    <input type='hidden' name='obat_kd[]' class='obat-kd'>
        							<input type='text' name='obat_nama[]' class='form-control obat-nama' placeholder='Nama obat (ketik lalu Enter)'>
                                    <div class='autocomplete-panel'></div>
                                </div>
                            </td>
                
                            <td>
                                <input type="number" name="qty[]" class="form-control qty" min="1" style="width:100px">
                            </td>
                            
                            <td>
                                <input type="text" name="sat_barang[]" class="form-control satuan" readonly>
                            </td>
                            <td>
                                <select name="opsiharga" class="form-control opsiharga">
                                    <option value="harga1">Harga #1</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </td>
                            
                            <td>
                                <input type="text" name="hrgjual[]" class="form-control hrgjual" readonly style="text-align:right">
                            </td>
                            
                            <td>
                                <input type="text" name="subtotal[]" class="form-control subtotal" readonly style="text-align:right">
                            </td>
                        </tr>
                        `);
                
                    
                });
            
                $(document).on('click', '.btn_remove', function(){
                    var button_id = $(this).attr("id");
                    $('#row'+button_id).remove();
                });
            
            });
            </script>
<?php
			
			break;
        case "detail" :
            $edit = $db->prepare("SELECT * FROM bundle WHERE id_bundle = ?");
			$edit->execute([$_GET['id']]);
			$r = $edit->fetch(PDO::FETCH_ASSOC);

?>
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">UBAH PAKET PRODUK </h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <div class="box-body table-responsive" style="height:100%">
                    <form method="POST" action="<?=$aksi?>?module=bundle&act=update_bundle" enctype="multipart/form-data" class="form-horizontal" id="form_bundle">
            
                        <div class="row">
                            <div class="col-lg-6">
                                <label>Nama Paket Produk</label>
                                <input type="text" name="nm_bundle" id="nm_bundle" class="form-control" aria-label="..." placeholder="Nama Paket Produk"  value="<?=$r['nm_bundle']?>" required disabled>
                                <input type="hidden" name="kd_bundle" id="kd_bundle" value="<?=$r['kd_bundle']?>" disabled>
                                
                                <!-- /input-group -->
                            </div>
                            <!-- /.col-lg-6 -->
                            <div class="col-lg-6">
                                <label>Satuan</label>
                                <div class="input-group">
                                    <select class="form-control" name="sat_bundle" required disabled>
                                        <option value="<?=$r['sat_bundle']?>"><?=$r['sat_bundle']?></option>
                                        <?php
                                            $stmt = $db->prepare("SELECT * FROM satuan ORDER BY nm_satuan ASC");
                                            $stmt->execute();
                                            while($rs = $stmt->fetch(PDO::FETCH_ASSOC)){
                                                echo '<option value="'.$rs['nm_satuan'].'">'.$rs['nm_satuan'].'</option>';
                                            }
                                        ?>
                                    </select>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="add" disabled>Tambah Item</button>
                                    </span>
                                </div>
                                <!-- /input-group -->
                            </div>
                            <!-- /.col-lg-6 -->
                        </div>
                        <!-- /.row -->
                        <hr>
                        
                        <table class="table table-bordered" id="table_produk">
                            <thead>
                                <tr>
                                    <th width="3%" class="text-center">No.</th>
                                    <th>Nama Produk</th>
                                    <th>Quantity</th>
                                    <th>Satuan</th>
                                    <th>Opsi Harga</th>
                                    <th class="text-center">Harga Jual</th>
                                    <th class="text-center">Sub Total</th>
                                </tr>
                            </thead>
                
                            <tbody id="dynamic_field">
                                <?php
                                    $stmt_bundle_detail = $db->prepare("SELECT * FROM bundle_detail WHERE kd_bundle = ?");
                                    $stmt_bundle_detail->execute([$r['kd_bundle']]);
                                    $count_detail = $stmt_bundle_detail->rowCount();
                                    $no = 1;
                                    while($r1 = $stmt_bundle_detail->fetch(PDO::FETCH_ASSOC)):
                                ?>
                                <tr class='row-obat'>
                                    <td class="text-center">
                                        <div id="nomor"><?=$no?>.</div>
                                    </td>
                                    <td>
                                        <div class='autocomplete-wrapper'>
                                            <input type='hidden' name='obat_kd[]' class='obat-kd' value="<?=$r1['kd_barang']?>" disabled>
        							        <input type='text' name='obat_nama[]' class='form-control obat-nama' placeholder='Nama obat (ketik lalu Enter)' value="<?=$r1['nm_barang']?>" disabled>
                                            <div class='autocomplete-panel'></div>
                                        </div>
                                        
                                    </td>
                
                                    <td>
                                        <input type="number" name="qty[]" class="form-control qty" min="1" style="width:100px" value="<?=$r1['qty_barang']?>" disabled>
                                    </td>
                                    <td>
                                        <input type="text" name="sat_barang[]" class="form-control satuan" value="<?=$r1['sat_barang']?>" readonly disabled>
                                    </td>
                                    <td>
                                        <select name="opsiharga" class="form-control opsiharga" disabled>
                                            <option value="harga1">Harga #1</option>
                                            <option value="custom">Custom</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="hrgjual[]" class="form-control hrgjual" value="<?=$r1['hrgjual_barang']?>" readonly style="text-align:right" disabled>
                                    </td>
                                    <td>
                                        <input type="text" name="subtotal[]" class="form-control subtotal" value="<?=$r1['subtotal']?>" readonly style="text-align:right" disabled>
                                        
                                    </td>
                
                                    
                                </tr>
                                <?php
                                    $no++;
                                    endwhile;
                                ?>
                            </tbody>
                        </table>
                        <!-- PANEL BUTTON FIX BOTTOM -->
                        <div class="form-footer-fixed">
                            <div class="col-lg-2">
                                <label>Jumlah Kuota</label>
                            </div>
                            <div class="col-lg-6">
                                <label>Harga Jual Bundling</label>
                            </div>
                            
                            <div class="col-lg-4">
                                <label>&nbsp;</label>
                            </div> 
                            
                            <div class="col-lg-2">
                                <input type="text" name="qty_bundle" id="qty_bundle" class="form-control" aria-label="..." placeholder="Jumlah Kuota" value="<?=$r['qty_bundle']?>" required="" disabled>
                            </div>
                            <div class="col-lg-6">
                                <input type="hidden" name="total_item" id="total_item">
                                <input type="text" name="hrgjual_bundle" id="hrgjual_bundle" class="form-control" aria-label="..." placeholder="Harga Jual Bundling" value="<?=$r['hrgjual_bundle']?>" required="" disabled>
                            </div>
                            
                            <div class="col-lg-4">
                                
                                <button type="button" class="btn btn-default"  onclick="self.history.back()">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </button>
                            </div>    
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
            $(document).ready(function(){
            
                var i = <?=$count_detail?>;
            
                $('#add').click(function(){
            
                    i++;
            
                    $('#dynamic_field').append(`
                        <tr class='row-obat' id="row`+i+`">
                            <td class="text-center">
                                <div id="nomor">`+i+`.</div>
                            </td>
                            <td>
                                <div class='autocomplete-wrapper'>
                                    <input type='hidden' name='obat_kd[]' class='obat-kd'>
        							<input type='text' name='obat_nama[]' class='form-control obat-nama' placeholder='Nama obat (ketik lalu Enter)'>
                                    <div class='autocomplete-panel'></div>
                                </div>
                            </td>
                
                            <td>
                                <input type="number" name="qty[]" class="form-control qty" min="1" style="width:100px">
                            </td>
                            
                            <td>
                                <input type="text" name="sat_barang[]" class="form-control satuan" readonly>
                            </td>
                            <td>
                                <select name="opsiharga" class="form-control opsiharga">
                                    <option value="harga1">Harga #1</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </td>
                            
                            <td>
                                <input type="text" name="hrgjual[]" class="form-control hrgjual" readonly style="text-align:right">
                            </td>
                            
                            <td>
                                <input type="text" name="subtotal[]" class="form-control subtotal" readonly style="text-align:right">
                            </td>
                        </tr>
                        `);
                
                    
                });
            
                $(document).on('click', '.btn_remove', function(){
                    var button_id = $(this).attr("id");
                    $('#row'+button_id).remove();
                });
            
            });
            </script>        
<?php
            break ;
	}
}
?>

<script type="text/javascript" src="vendors/ckeditor/ckeditor.js"></script>
<style>
	.modal-fullscreen {
		width: 98%;
		margin: 10px auto;
	}
	.modal-fullscreen .modal-content {
		height: calc(100vh - 20px);
		overflow: hidden;
	}
	.modal-fullscreen .modal-body {
		height: calc(100% - 120px);
		overflow: auto;
	}
	#indikasi_modal_editor {
		width: 100%;
		height: 100%;
	}
	#zataktif_modal_editor {
		width: 100%;
		height: 100%;
	}
	#indikasiModal.is-open {
		display: block;
		position: fixed;
		inset: 0;
		background: rgba(0, 0, 0, 0.5);
		z-index: 1050;
		overflow: auto;
	}
	#indikasiModal.is-open.fade {
		opacity: 1;
	}
	#indikasiModal.is-open .modal-content {
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
	}
	#indikasiModal.is-open .modal-dialog {
		margin: 10px auto;
	}
	#zataktifModal.is-open {
		display: block;
		position: fixed;
		inset: 0;
		background: rgba(0, 0, 0, 0.5);
		z-index: 1050;
		overflow: auto;
	}
	#zataktifModal.is-open.fade {
		opacity: 1;
	}
	#zataktifModal.is-open .modal-content {
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
	}
	#zataktifModal.is-open .modal-dialog {
		margin: 10px auto;
	}
</style>
<script type="text/javascript">
    
    $(document).ready(function(){
        var totalItem = hitungTotal();
        $("#total_item").val(totalItem);
        document.getElementById("total").innerHTML = formatRupiah(totalItem);
    })
    
	// Inisialisasi CKEditor untuk form tambah
	if (document.getElementById('content')) {
		CKEDITOR.replace('content', {
			filebrowserBrowseUrl: '',
			filebrowserWindowWidth: 1000,
			filebrowserWindowHeight: 500
		});
	}
	if (document.getElementById('content_ket')) {
		CKEDITOR.replace('content_ket', {
			filebrowserBrowseUrl: '',
			filebrowserWindowWidth: 1000,
			filebrowserWindowHeight: 500
		});
	}
	
	// Inisialisasi CKEditor untuk form edit
	if (document.getElementById('edit_zataktif')) {
		CKEDITOR.replace('edit_zataktif', {
			filebrowserBrowseUrl: '',
			filebrowserWindowWidth: 1000,
			filebrowserWindowHeight: 500
		});
	}
	if (document.getElementById('edit_indikasi')) {
		CKEDITOR.replace('edit_indikasi', {
			filebrowserBrowseUrl: '',
			filebrowserWindowWidth: 1000,
			filebrowserWindowHeight: 500
		});
	}
	if (document.getElementById('edit_ket_barang')) {
		CKEDITOR.replace('edit_ket_barang', {
			filebrowserBrowseUrl: '',
			filebrowserWindowWidth: 1000,
			filebrowserWindowHeight: 500
		});
	}
	if (document.getElementById('edit_dosis')) {
		CKEDITOR.replace('edit_dosis', {
			filebrowserBrowseUrl: '',
			filebrowserWindowWidth: 1000,
			filebrowserWindowHeight: 500
		});
	}
	
	var selectedIndex = -1;
    var delayTimer;
    
    
    /* =========================
       AUTOCOMPLETE SEARCH
    ========================= */
    
    $(document).on("keyup", ".obat-nama", function(e){
    
        // skip navigation keys
        if(e.keyCode == 38 || e.keyCode == 40 || e.keyCode == 13){
            return;
        }
    
        clearTimeout(delayTimer);
    
        var input = this;
    
        delayTimer = setTimeout(function(){
    
            var $row = $(input).closest(".row-obat");
            var $wrapper = $row.find(".autocomplete-wrapper");
    
            var keyword = $(input).val().trim();
            var panel = $wrapper.find(".autocomplete-panel");
    
            if(keyword.length < 2){
                panel.hide();
                return;
            }
    
            $.ajax({
                url: "modul/mod_bundle/autonamabarang.php",
                type: "POST",
                dataType: "json",
                data:{
                    query: keyword
                },
                success:function(data){
    
                    selectedIndex = -1;
    
                    panel.empty();
    
                    if(!data || data.length === 0){
    
                        panel.append('<div class="autocomplete-empty">Obat tidak ditemukan</div>');
                        panel.show();
                        return;
    
                    }
    
                    panel.append('<div class="autocomplete-header">Hasil Pencarian</div>');
    
                    data.forEach(function(item){
                        // console.log(item)});
                        var html = `
                        <div class="autocomplete-item"
                             data-kode="${item.kd_barang}"
                             data-nama="${item.nm_barang}"
                             data-satuan="${item.sat_barang}"
                             data-hrgjual="${item.hrgjual_barang}">
                             💊 ${item.nm_barang}
                        </div>`;
    
                        panel.append(html);
    
                    });
    
                    panel.show();
    
                }
            });
    
        },300);
    
    });



    /* =========================
       KEYBOARD NAVIGATION
    ========================= */
    
    $(document).on("keydown", ".obat-nama", function(e){
    
        var $row = $(this).closest(".row-obat");
        var panel = $row.find(".autocomplete-panel");
        var items = panel.find(".autocomplete-item");
    
        if(!panel.is(":visible")) return;
    
    
        // ARROW DOWN
        if(e.keyCode == 40){
    
            e.preventDefault();
    
            selectedIndex++;
    
            if(selectedIndex >= items.length){
                selectedIndex = 0;
            }
    
            items.removeClass("active");
    
            var activeItem = items.eq(selectedIndex);
    
            activeItem.addClass("active");
    
            panel.scrollTop(
                activeItem.position().top + panel.scrollTop()
            );
    
        }
    
    
        // ARROW UP
        if(e.keyCode == 38){
    
            e.preventDefault();
    
            selectedIndex--;
    
            if(selectedIndex < 0){
                selectedIndex = items.length - 1;
            }
    
            items.removeClass("active");
    
            var activeItem = items.eq(selectedIndex);
    
            activeItem.addClass("active");
    
            panel.scrollTop(
                activeItem.position().top + panel.scrollTop()
            );
    
        }
    
    
        // ENTER
        if(e.keyCode == 13){
    
            e.preventDefault();
    
            if(selectedIndex >= 0){
    
                items.eq(selectedIndex).click();
    
            }else if(items.length > 0){
    
                items.eq(0).click();
    
            }
    
            selectedIndex = -1;
        }
    
    });



    /* =========================
       CLICK RESULT
    ========================= */
    
    $(document).on("click",".autocomplete-item",function(){
    
        var nama    = $(this).data("nama");
        var kode    = $(this).data("kode");
        var satuan  = $(this).data("satuan");
        var hrgjual = $(this).data("hrgjual");
    
        var $row = $(this).closest(".row-obat");
    
        $row.find(".obat-nama").val(nama);
        $row.find(".obat-kd").val(kode);
    
        $row.find(".autocomplete-panel").hide();
    
        var $rrow = $(this).closest("tr");
        $rrow.find(".satuan").val(satuan);
        $rrow.find(".qty").val("1");
        $rrow.find(".hrgjual").val(formatRupiah(hrgjual));
        $rrow.find(".hrgjual").attr('data-hargaawal', hrgjual);
        $rrow.find(".subtotal").val(formatRupiah(hrgjual));
        
        var totalItem = hitungTotal();
        $("#total_item").val(totalItem);
        document.getElementById("total").innerHTML = formatRupiah(totalItem);
    });



    /* =========================
       CLOSE PANEL IF CLICK OUTSIDE
    ========================= */

    $(document).click(function(e){
    
        if(!$(e.target).closest(".autocomplete-wrapper").length){
            $(".autocomplete-panel").hide();
        }
    
    });
    
    $(document).on("change",".opsiharga",function(){
    
        var opsi = $(this).val();
        var $rrow = $(this).closest("tr");
        if(opsi === "custom"){
            $rrow.find(".hrgjual").removeAttr("readonly");
        } else {
            // var hrglama = $(".autocomplete-item").data("hrgjual");
            var hrglama = $rrow.find(".hrgjual").data('hargaawal');
            $rrow.find(".hrgjual").attr("readonly","true");
            $rrow.find(".hrgjual").val(formatRupiah(hrglama));
            
            var qty         = $rrow.find(".qty").val();
            var subtotal    = hrglama * qty;
            $rrow.find(".subtotal").val(formatRupiah(subtotal));
            
            var totalItem = hitungTotal();
            $("#total_item").val(totalItem);
            document.getElementById("total").innerHTML = formatRupiah(totalItem);
            
        }
    });


    $(document).on("keydown","#nm_bundle",function(e){
        if(e.keyCode == 13){
            e.preventDefault();
        }
    });
    
    $(document).on("keydown","#qty_bundle",function(e){
        if(e.keyCode == 13){
            e.preventDefault();
        }
    });
    
// Start Harga jual
    $(document).ready(function () {
        $('.hrgjual').mask('000.000.000', {reverse: true});
        $('#hrgjual_bundle').mask('000.000.000', {reverse: true});
        $('#qty_bundle').mask('000.000.000', {reverse: true});
    });
    
    $(document).on("keyup","#hrgjual_bundle",function(){
        var totalItem = hitungTotal();
        $("#total_item").val(totalItem);
        document.getElementById("total").innerHTML = formatRupiah(totalItem);
    });
    
    $(document).on("keydown","#hrgjual_bundle",function(e){
        if(e.keyCode == 13){
            e.preventDefault();
        }
    });
    
    $(document).on("change",".hrgjual",function(){
        var $rrow       = $(this).closest("tr");
        
        var hrgjual     = unformatRupiah($(this).val());
        var qty         = $rrow.find(".qty").val();
        var subtotal    = hrgjual * qty;
        $rrow.find(".subtotal").val(formatRupiah(subtotal));
        
        var totalItem = hitungTotal();
        $("#total_item").val(totalItem);
        document.getElementById("total").innerHTML = formatRupiah(totalItem);
    });
    
    $(document).on("keydown",".hrgjual",function(e){
        if(e.keyCode == 13){
            e.preventDefault();
        }
        var $rrow       = $(this).closest("tr");
        
        var hrgjual     = unformatRupiah($(this).val());
        var qty         = $rrow.find(".qty").val();
        var subtotal    = hrgjual * qty;
        $rrow.find(".subtotal").val(formatRupiah(subtotal));
        
        var totalItem = hitungTotal();
        $("#total_item").val(totalItem);
        document.getElementById("total").innerHTML = formatRupiah(totalItem);
    });
// End Harga jual
    
// Start qty
    $(document).on("change",".qty",function(){
        var $rrow       = $(this).closest("tr");
        
        var qty         = $(this).val();
        var hrgjual     = unformatRupiah($rrow.find(".hrgjual").val());
        var subtotal    = hrgjual * qty;
        $rrow.find(".subtotal").val(formatRupiah(subtotal));
        
        var totalItem = hitungTotal();
        $("#total_item").val(totalItem);
        document.getElementById("total").innerHTML = formatRupiah(totalItem);
    });
    
    $(document).on("keydown",".qty",function(e){
        if(e.keyCode == 13){
            e.preventDefault();
        }
        var $rrow       = $(this).closest("tr");
        
        var qty         = $(this).val();
        var hrgjual     = unformatRupiah($rrow.find(".hrgjual").val());
        var subtotal    = hrgjual * qty;
        $rrow.find(".subtotal").val(formatRupiah(subtotal));
        
        var totalItem = hitungTotal();
        $("#total_item").val(totalItem);
        document.getElementById("total").innerHTML = formatRupiah(totalItem);
    });
// End Qty

    function hitungTotal(){
        var total = 0;
        $("#dynamic_field tr").each(function(){
            var subtotal = parseInt(unformatRupiah($(this).find(".subtotal").val()));
            total += subtotal;
        });
    
        return total;
    }
    
    $("#form_bundle").on("submit", function(e){
        var totalItem       = $("#total_item").val();
        var hrgjualbundle   = unformatRupiah($("#hrgjual_bundle").val());
        if(totalItem != hrgjualbundle){
            e.preventDefault(); // hentikan POST
            alert('Harga Jual Tidak Sesuai dengan Sub Total Item!');
        }
    });
    
    $(document).on("click","#hapus",function(e){
        var idbundle_detail = $(this).data('idbundle_detail');
        $.ajax({
            url: "modul/mod_bundle/hapusdetail_bundle.php",
            type: "POST",
            data:{
                'idbundle_detail': idbundle_detail
            },
            success:function(data){
                var result = JSON.parse(data);
                if(result.status === 'success'){
                    window.location.reload();
                } 
            }
        });
    });
</script>
<script>
var userLevel = '<?= $_SESSION['level']; ?>';
</script>
<?php
$barang_table_config_path = __DIR__ . '/barang_table_config.js';
$barang_table_config_ver = file_exists($barang_table_config_path) ? filemtime($barang_table_config_path) : time();
?>
<script src="modul/mod_barang/barang_table_config.js?v=<?= $barang_table_config_ver ?>"></script>