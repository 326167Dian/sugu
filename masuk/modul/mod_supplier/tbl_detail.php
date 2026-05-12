<style>
	.table-condensed {
		font-size: 13px;
	}

	.table-akum {
		font-size: 11px;
	}

	.judul-table {

		text-align: center;
		font-weight: bold;
		font-size: 13px;
		background-color: #008000;
		color: white;

	}
</style>
<div class="box-body table-responsive">
	<table id="example1" class="table table-condensed table-bordered table-striped table-hover">
		<thead>
			<tr class="judul-table">
				<th style="vertical-align: middle; background-color: #008000; text-align: center; ">No</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Kode Barang</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Nama Barang</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Satuan</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Hrg Beli</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Hrg Jual</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Aksi</th>
			</tr>
		</thead>
		<tbody>
			<?php
			include "../../../configurasi/koneksi.php";
			include "../../../configurasi/fungsi_rupiah.php";

			$idsupplier = $_POST['id_supplier'];

			$stmt = $db->prepare("SELECT * FROM barang_supplier a
							JOIN supplier b ON a.id_supplier = b.id_supplier
							JOIN barang c ON a.id_barang = c.id_barang
							WHERE a.id_supplier = ?
							ORDER BY a.id_barang ASC");
			$stmt->execute([$idsupplier]);
			$brg_supplier = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$no = 1;
			foreach ($brg_supplier as $r) {

				echo "<tr style='font-size: 13px;'>
											<td align=center>$no</td>           
											 <td align=left>$r[kd_barang]</td>
											 <td>$r[nm_barang]</td>
											 <td align=center>$r[sat_barang]</td>
											 <td align=right>$r[hrgsat_brgsupplier]</td>
											 <td align=right>$r[hrgjual_barang]</td>
											 <td align=center>
											 <button class='btn btn-xs btn-danger' id='hapusdetail' 
												 data-id_brgsup='$r[id_brgsup]'>
												 <i class='glyphicon glyphicon-remove'></i>
												 </button>
												
											</td>
										</tr>";

				$no++;
			}


			?>
		</tbody>
	</table>