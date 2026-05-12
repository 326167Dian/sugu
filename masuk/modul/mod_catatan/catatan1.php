<?php
session_start();
include "../../../configurasi/koneksi.php";
 if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
  echo "<link href=../css/style.css rel=stylesheet type=text/css>";
  echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
}
else{

$aksi="modul/mod_catatan/aksi_catatan.php";

function normalize_catatan_html_read($html)
{
	$html = trim((string)$html);
	if ($html === '') {
		return '';
	}

	if (function_exists('tidy_repair_string')) {
		$config = [
			'show-body-only' => true,
			'clean' => true,
			'output-html' => true,
			'wrap' => 0,
			'char-encoding' => 'utf8',
		];
		$html = tidy_repair_string($html, $config, 'utf8');
	}

	if (class_exists('DOMDocument')) {
		$internalErrors = libxml_use_internal_errors(true);
		$dom = new DOMDocument('1.0', 'UTF-8');

		$flags = 0;
		if (defined('LIBXML_HTML_NOIMPLIED')) {
			$flags |= LIBXML_HTML_NOIMPLIED;
		}
		if (defined('LIBXML_HTML_NODEFDTD')) {
			$flags |= LIBXML_HTML_NODEFDTD;
		}

		$loaded = $dom->loadHTML('<?xml encoding="utf-8" ?><div id="catatan-root">' . $html . '</div>', $flags);
		if ($loaded) {
			foreach (['script', 'iframe', 'object'] as $tag) {
				$nodes = $dom->getElementsByTagName($tag);
				while ($nodes->length > 0) {
					$node = $nodes->item(0);
					$node->parentNode->removeChild($node);
				}
			}

			$root = $dom->getElementById('catatan-root');
			if ($root) {
				$normalized = '';
				foreach ($root->childNodes as $childNode) {
					$normalized .= $dom->saveHTML($childNode);
				}
				$normalized = trim($normalized);

				libxml_clear_errors();
				libxml_use_internal_errors($internalErrors);
				if ($normalized !== '') {
					return $normalized;
				}
			}
		}

		libxml_clear_errors();
		libxml_use_internal_errors($internalErrors);
	}

	return nl2br(htmlspecialchars($html, ENT_QUOTES, 'UTF-8'));
}

function catatan_preview_text($html, $maxLength = 120)
{
	$safeHtml = normalize_catatan_html_read($html);
	$text = html_entity_decode(strip_tags($safeHtml), ENT_QUOTES, 'UTF-8');
	$text = trim(preg_replace('/\s+/u', ' ', $text));
	if ($text === '') {
		return '-';
	}

	if (function_exists('mb_strlen') && function_exists('mb_substr')) {
		if (mb_strlen($text, 'UTF-8') > $maxLength) {
			return mb_substr($text, 0, $maxLength, 'UTF-8') . '...';
		}
		return $text;
	}

	if (strlen($text) > $maxLength) {
		return substr($text, 0, $maxLength) . '...';
	}
	return $text;
}

function catatan_plain_text($html)
{
	$safeHtml = normalize_catatan_html_read($html);
	$text = html_entity_decode(strip_tags($safeHtml), ENT_QUOTES, 'UTF-8');
	$text = trim(preg_replace('/\s+/u', ' ', $text));
	return $text === '' ? '-' : $text;
}

switch($_GET['act']){
  // tampil catatan
  default:

	  ?>
			
			
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">CATATAN HARIAN</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class="box-body">
					<a  class ='btn  btn-success btn-flat' href='?module=catatan&act=tambah'>TAMBAH</a>
					<br><br>
					
					
					<table id="example1" class="table table-bordered table-striped" >
						<thead>
							<tr>
								<th>No</th>
								<th>tanggal</th>
								<th>Shift</th>
                                <th>Petugas</th>
                                <th>catatan singkat</th>
								<th width="70">Aksi</th>
							</tr>
						</thead>
						<tbody>
						<?php 
								$no=1;
								$tampil_catatan = $db->prepare("SELECT * FROM catatan ORDER BY tgl DESC");
                                $tampil_catatan->execute();
								while ($r = $tampil_catatan->fetch(PDO::FETCH_ASSOC)){
									$preview = catatan_preview_text($r['deskripsi']);
									$previewFull = catatan_plain_text($r['deskripsi']);
									$previewEsc = htmlspecialchars($preview, ENT_QUOTES, 'UTF-8');
									$previewFullEsc = htmlspecialchars($previewFull, ENT_QUOTES, 'UTF-8');
									echo "<tr class='warnabaris' >
											<td>$no</td>           
											 <td>$r[tgl]</td>
											 <td>$r[shift]</td>
											 <td>$r[petugas]</td>
													 <td title='$previewFullEsc' data-toggle='tooltip' data-placement='top'>$previewEsc</td>
											 <td>
											 <a href='?module=catatan&act=edit&id=$r[id_catatan]' title='EDIT' class='btn btn-warning btn-xs'>EDIT</a> 
											 <a href='?module=catatan&act=tampil&id=$r[id_catatan]' title='EDIT' class='btn btn-primary btn-xs'>TAMPIL</a> 
										     <a href=javascript:confirmdelete('$aksi?module=catatan&act=hapus&id=$r[id_catatan]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
											</td>
										</tr>";
								$no++;
								}
						echo "</tbody></table>";
					?>
				</div>
                
			</div>	
             

<?php
    
    break;
	
	case "tambah":
        $tglharini = date('Y-m-d');
        $petugas = $_SESSION['namalengkap'];
        $cekshift = $db->prepare("SELECT * FROM waktukerja WHERE tanggal = ? AND status = 'ON'");
        $cekshift->execute([$tglharini]);
        $hitung = $cekshift->rowCount();
        $sshift = $cekshift->fetch(PDO::FETCH_ASSOC);
        $shift = $sshift['shift'];

        echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>TAMBAH CATATAN</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
				
						<form method=POST action='$aksi?module=catatan&act=input_catatan' enctype='multipart/form-data' class='form-horizontal'>
						 <input type='hidden' name='petugas' id='petugas' value='$petugas'>
						 <input type='hidden' name='shift' id='shift' value='$shift'>
							 
							 <label class='col-sm-4 control-label'>Tanggal </label>
										<div class='col-sm-6'>
											<div class='input-group date'>
												<div class='input-group-addon'>
													<span class='glyphicon glyphicon-th'></span>
												</div>
													<input type='text' class='datepicker' name='tgl' value='$tglharini' autocomplete='off'>
											</div>
										</div>
							  
							<br>
							<label style='text-align:left;'>Catatan</label>
										<div class='col-xs-12'>
											<div>	
													<textarea name='deskripsi' class='ckeditor' id='content'></textarea>
											</div>
										</div>
							
							 <br> 
							  <div class='form-group'>
									<label class='col-sm-2 control-label'></label>       
										<div class='col-sm-5'>
											<input class='btn btn-primary' type=submit value='SIMPAN'>
											<input class='btn btn-danger' type=button value='BATAL' onclick='self.history.back()'>
										</div>
								</div>
								
							  </form>
							  
				</div> 
				
			</div>";
					
	
    break;

  case "edit":
      $petugas = $_SESSION['namalengkap'];
      $edit = $db->prepare("SELECT * FROM catatan WHERE id_catatan = ?");
      $edit->execute([$_GET['id']]);
      $r = $edit->fetch(PDO::FETCH_ASSOC);
      $lupa = $_SESSION['level'];
        

      if (($petugas !== $r['petugas']) && ($lupa !== 'pemilik')) {
          echo "<script type='text/javascript'>alert('catatan hanya bisa di edit oleh orang yang sama!');history.go(-1);</script>";
      } else {

     	echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>EDIT CATATAN</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
						<form method=POST method=POST action=$aksi?module=catatan&act=update_catatan  enctype='multipart/form-data' class='form-horizontal'>
							<input type=hidden name=id value='$r[id_catatan]'>
							 <div class='form-group'>
									
											<div class='col-xs-12'>
											<div >	
													<textarea name='deskripsi' class='ckeditor' id='content'>$r[deskripsi]</textarea>
													
											</div>
										</div>
							  </div>
							 
							 <div class='form-group'>
									<label class='col-sm-2 control-label'></label>       
										<div class='col-sm-5'>
											<input class='btn btn-primary' type=submit value=SIMPAN>
											<input class='btn btn-danger' type=button value=BATAL onclick=self.history.back()>
										</div>
								</div>
							  </form>
							  
				</div> 
				
			</div>";}
	
    break;
    case "tampil" :
        $edit = $db->prepare("SELECT * FROM catatan WHERE id_catatan = ?");
        $edit->execute([$_GET['id']]);
        $r = $edit->fetch(PDO::FETCH_ASSOC);
		$safeDeskripsi = normalize_catatan_html_read($r['deskripsi'] ?? '');
		echo "
		<div class='box box-primary box-solid'>
			<div class='box-header with-border'>
				<h3 class='box-title'>DETAIL CATATAN</h3>
			</div>
			<div class='box-body'>
				<div class='catatan-content'>$safeDeskripsi</div>
				<br>
				<input class='btn btn-primary' type='button' value='KEMBALI' onclick='self.history.back()'>
			</div>
		</div>";
    break ;

}
}
?>
<script type="text/javascript" src="vendors/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
    var editor = CKEDITOR.replace("content", {
        filebrowserBrowseUrl: '',
        filebrowserWindowWidth: 1000,
        filebrowserWindowHeight: 500
    });

	if (window.jQuery) {
		jQuery(document).on('submit', 'form', function() {
			for (var instance in CKEDITOR.instances) {
				if (Object.prototype.hasOwnProperty.call(CKEDITOR.instances, instance)) {
					CKEDITOR.instances[instance].updateElement();
				}
			}
		});
	}
</script>
<script type="text/javascript">
 $(function(){
  $(".datepicker").datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true,
      todayHighlight: true,
  });

	if ($.fn.tooltip) {
			$('[data-toggle="tooltip"]').tooltip();
	}
 });
</script>