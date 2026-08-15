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
    <table id="example5" class="table table-condensed table-bordered table-striped table-hover">
        <thead>
            <tr class="judul-table">
                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">No</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: left; ">Kode Barang</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: left; ">Nama Barang</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">Qty Grosir</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Satuan Grosir</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Konversi</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">No. Batch</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Exp. Date</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">Harga Beli</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">Disc(%)</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">Hrg Beli+Disc</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">Hrg Jual Satuan</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">Total</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include "../../../configurasi/koneksi.php";
            include "../../../configurasi/fungsi_rupiah.php";
            include "../../../configurasi/fungsi_indotgl.php";

            $kd_trbmasuk = isset($_POST['kd_trbmasuk']) ? $_POST['kd_trbmasuk'] : '';
            $kd_orders   = isset($_POST['kd_orders'])   ? $_POST['kd_orders']   : '';

            $no = 1;
            $totalharga1 = 0;
            $nilai_batch = 1;

            // 1) Item pesanan yang sudah diterima ke transaksi (draft) ini
            // dibatasi ke kd_trbmasuk milik transaksi berjalan ini sendiri, supaya:
            // - item yang sudah diterima lewat modul trbmasukpbf (kd_trbmasuk berbeda) tidak ikut tampil di sini
            // - tetap tampil walau header trbmasuk belum tersimpan (SIMPAN TRANSAKSI belum ditekan)
            $stmt_trb = $db->prepare("SELECT * FROM trbmasuk_detail WHERE kd_trbmasuk = ? ORDER BY id_dtrbmasuk ASC");
            $stmt_trb->execute([$kd_trbmasuk]);
            while ($trb = $stmt_trb->fetch(PDO::FETCH_ASSOC)) {

                $hrgbelidisc   = $trb['hrgsat_dtrbmasuk'] * (1 - ($trb['diskon'] / 100));
                $baristotal    = round($hrgbelidisc * $trb['qty_grosir']);
                $totalharga1  += $baristotal;
                $nilai_batch   = ($trb['no_batch'] != "") ? ($nilai_batch * 1) : ($nilai_batch * 0);

                $hrgsat_txt    = format_rupiah($trb['hrgsat_dtrbmasuk']);
                $hrgbelidisc_txt = format_rupiah($hrgbelidisc);
                $hrgjual_txt   = format_rupiah($trb['hrgjual_dtrbmasuk']);
                $total_txt     = format_rupiah($baristotal);

                echo "<tr style='font-size: 13px;'>
                                        <td align='center'>$no</td>
                                        <td align='left'>$trb[kd_barang]</td>
                                        <td>$trb[nmbrg_dtrbmasuk]</td>
                                        <td align='right'>
                                            <input type='number' id='dqtygrosir_dtrbmasuk' value='$trb[qty_grosir]' style='width: 60px; text-align: center'
                                                data-id_dtrbmasuk='$trb[id_dtrbmasuk]'
                                                data-kd_barang='$trb[kd_barang]'
                                            >
                                        </td>
                                        <td align='center'>$trb[satgrosir_dtrbmasuk]</td>
                                        <td align='center'>
                                            <input type='number' id='dkonversi' value='$trb[konversi]' style='width: 60px; text-align: center'
                                                data-id_dtrbmasuk='$trb[id_dtrbmasuk]'
                                                data-kd_barang='$trb[kd_barang]'
                                                data-qtygrosir_dtrbmasuk='$trb[qty_grosir]'
                                            >
                                        </td>
                                        <td align='center'>
                                            <input type='text' id='dno_batch' value='$trb[no_batch]' style='width: 100px'
                                                data-id_dtrbmasuk='$trb[id_dtrbmasuk]'
                                                data-kd_barang='$trb[kd_barang]'
                                                data-qtygrosir_dtrbmasuk='$trb[qty_grosir]'
                                            >
                                        </td>
                                        <td align='center'>
                                            <input type='text' class='datepicker' id='dexp_date' value='$trb[exp_date]'
                                                data-id_dtrbmasuk='$trb[id_dtrbmasuk]'
                                                data-kd_barang='$trb[kd_barang]'
                                                data-qtygrosir_dtrbmasuk='$trb[qty_grosir]'
                                            >
                                        </td>
                                        <td align='right'>
                                            <input type='text' id='dhrgsat_dtrbmasuk' value='$hrgsat_txt' style='width: 100px'
                                                data-id_dtrbmasuk='$trb[id_dtrbmasuk]'
                                                data-kd_barang='$trb[kd_barang]'
                                                data-qtygrosir_dtrbmasuk='$trb[qty_grosir]'
                                            >
                                        </td>
                                        <td align='right'>
                                            <input type='text' id='ddiskon' value='$trb[diskon]' style='width: 50px'
                                                data-id_dtrbmasuk='$trb[id_dtrbmasuk]'
                                                data-kd_barang='$trb[kd_barang]'
                                                data-qtygrosir_dtrbmasuk='$trb[qty_grosir]'
                                            >
                                        </td>
                                        <td align='right'>$hrgbelidisc_txt</td>
                                        <td align='right'>
                                            <input type='text' id='dhrgjual_dtrbmasuk' value='$hrgjual_txt' style='width: 100px'
                                                data-id_dtrbmasuk='$trb[id_dtrbmasuk]'
                                                data-kd_barang='$trb[kd_barang]'
                                                data-qtygrosir_dtrbmasuk='$trb[qty_grosir]'
                                            >
                                        </td>
                                        <td align='right'>$total_txt</td>
                                        <td align='center'>
                                            <button class='btn btn-xs btn-danger' id='hapusorder'
                                                data-id_dtrbmasuk='$trb[id_dtrbmasuk]'>
                                                <i class='glyphicon glyphicon-remove'></i>
                                            </button>
                                        </td>
                                    </tr>";

                $no++;
            }

            // 2) Item pesanan yang belum diterima
            $stmt_ord = $db->prepare("SELECT * FROM ordersdetail WHERE kd_trbmasuk = ? AND masuk = '1' ORDER BY id_dtrbmasuk ASC");
            $stmt_ord->execute([$kd_orders]);
            while ($r = $stmt_ord->fetch(PDO::FETCH_ASSOC)) {

                $hrgbelidisc   = $r['hrgsat_dtrbmasuk'] * (1 - ($r['diskon'] / 100));
                $baristotal    = round($hrgbelidisc * $r['qtygrosir_dtrbmasuk']);
                $totalharga1  += $baristotal;
                $nilai_batch   = ($r['no_batch'] != "") ? ($nilai_batch * 1) : ($nilai_batch * 0);

                $hrgsat_txt    = format_rupiah($r['hrgsat_dtrbmasuk']);
                $hrgbelidisc_txt = format_rupiah($hrgbelidisc);
                $hrgjual_txt   = format_rupiah($r['hrgjual_dtrbmasuk']);
                $total_txt     = format_rupiah($baristotal);

                echo "<tr style='font-size: 13px;'>
                                        <td align='center'>$no</td>
                                        <td align='left'>$r[kd_barang]</td>
                                        <td>$r[nmbrg_dtrbmasuk]</td>
                                        <td align='right'>
                                            <input type='number' id='dqtygrosir_dtrbmasuk' value='$r[qtygrosir_dtrbmasuk]' style='width: 60px; text-align: center'
                                                data-id_dtrbmasuk='$r[id_dtrbmasuk]'
                                                data-kd_barang='$r[kd_barang]'
                                            >
                                        </td>
                                        <td align='center'>$r[satgrosir_dtrbmasuk]</td>
                                        <td align='center'>
                                            <input type='number' id='dkonversi' value='$r[konversi]' style='width: 60px; text-align: center'
                                                data-id_dtrbmasuk='$r[id_dtrbmasuk]'
                                                data-kd_barang='$r[kd_barang]'
                                                data-qtygrosir_dtrbmasuk='$r[qtygrosir_dtrbmasuk]'
                                            >
                                        </td>
                                        <td align='center'>
                                            <input type='text' id='dno_batch' value='$r[no_batch]' style='width: 100px' required
                                                data-id_dtrbmasuk='$r[id_dtrbmasuk]'
                                                data-kd_barang='$r[kd_barang]'
                                                data-qtygrosir_dtrbmasuk='$r[qtygrosir_dtrbmasuk]'
                                            >
                                        </td>
                                        <td align='center'>
                                            <input type='text' class='datepicker' id='dexp_date' value='$r[exp_date]' required
                                                data-id_dtrbmasuk='$r[id_dtrbmasuk]'
                                                data-kd_barang='$r[kd_barang]'
                                                data-qtygrosir_dtrbmasuk='$r[qtygrosir_dtrbmasuk]'
                                            >
                                        </td>
                                        <td align='right'>
                                            <input type='text' id='dhrgsat_dtrbmasuk' value='$hrgsat_txt' style='width: 100px'
                                                data-id_dtrbmasuk='$r[id_dtrbmasuk]'
                                                data-kd_barang='$r[kd_barang]'
                                                data-qtygrosir_dtrbmasuk='$r[qtygrosir_dtrbmasuk]'
                                            >
                                        </td>
                                        <td align='right'>
                                            <input type='number' id='ddiskon' value='$r[diskon]' style='width: 50px'
                                                data-id_dtrbmasuk='$r[id_dtrbmasuk]'
                                                data-kd_barang='$r[kd_barang]'
                                                data-qtygrosir_dtrbmasuk='$r[qtygrosir_dtrbmasuk]'
                                            >
                                        </td>
                                        <td align='right'>$hrgbelidisc_txt</td>
                                        <td align='right'>
                                            <input type='text' id='dhrgjual_dtrbmasuk' value='$hrgjual_txt' style='width: 100px'
                                                data-id_dtrbmasuk='$r[id_dtrbmasuk]'
                                                data-kd_barang='$r[kd_barang]'
                                                data-qtygrosir_dtrbmasuk='$r[qtygrosir_dtrbmasuk]'
                                            >
                                        </td>
                                        <td align='right'>$total_txt</td>
                                        <td align='center'>
                                            <button class='btn btn-xs btn-danger' id='hapusorder'
                                                data-id_dtrbmasuk='$r[id_dtrbmasuk]'>
                                                <i class='glyphicon glyphicon-remove'></i>
                                            </button>
                                        </td>
                                    </tr>";

                $no++;
            }

            $grandnya = format_rupiah($totalharga1);

            echo "</tbody>
                        <tr>
                            <td colspan='10'><h4><center>Total Harga </center></h4>  </td>
                            <td colspan='4'><h4><strong> Rp. <span id='ttl_harga_display'>$grandnya</span>  ,- </strong></h4></td>
                        </tr>
</table>

                <p>
            <legend class='scheduler-border'></legend>
                <div class='col-md-6'>

                </div>


                <div class='col-lg-6'>

                    <div class='text-right'>
                        <label class='col-sm-6 control-label'>SUB TOTAL</label>
                         <div class='col-sm-6'>
                            <input type='text' name='ttl_trkasir' id='ttl_trkasir' value='$grandnya' class='form-control input-validation-error' style='font-size: 18px; color: #fff; font-weight: bold; text-align: right; background: #000000;' autocomplete='off' readonly>
                            <input type='hidden' name='nilai_batch' id='nilai_batch' value='$nilai_batch'>
                         </div>
                    </div>

                    <div class='text-right'>
                        <label class='col-sm-6 control-label'>DISKON % & Nominal</label>
                        <div class='col-sm-6'>
                             <div class='btn-group btn-group-justified' role='group' aria-label='...'>
                                <div class='btn-group' role='group'>
                                    <input type='text' name='diskon2' id='diskon2' value='' class='form-control'  style='font-size: 18px; color: #000000; font-weight: bold; text-align: right;' autocomplete='off'>
                                </div>
                                <div class='btn-group' role='group'>
                                    <input type='text' name='dp_bayar' id='dp_bayar' value='' class='form-control'  style='font-size: 18px; color: #000000; font-weight: bold; text-align: right;' autocomplete='off'>
                                </div>
                                <div class='btn-group' role='group'>
                                    <button type='button' class='btn btn-primary' id='diskon_enter'>Enter</button>
                                  </div>
                            </div>
                        </div>

                    </div>

                    <div class='text-right'>
                        <label class='col-sm-6 control-label'>Total Tagihan</label>
                         <div class='col-sm-6'>
                            <input type='text' name='sisa_bayar' id='sisa_bayar' class='form-control' style='font-size: 18px; color: #fff; font-weight: bold; text-align: right; background: #000000;' autocomplete='off' readonly>
                         </div>
                    </div>

                    <div class='text-right'>
                        <label class='col-sm-6 control-label'>CARA BAYAR</label>
                         <div class='col-sm-6'>
                            <select name='carabayar' id='carabayar' class='form-control'
                            style='font-size: 13px; color: #000000; font-weight: bold;'>
                                <option value='KREDIT'>KREDIT</option>
                                <option value='LUNAS'>TUNAI</option>
                                <option value='KONSINYASI'>KONSINYASI</option>
                             </select>

                         </div>
                    </div>
                </div>

        </div>";
            ?>
            <script>
                // Update sel-sel yang terkena dampak pada baris yang diedit, tanpa reload seluruh tabel
                // (supaya baris lain / posisi halaman DataTable tidak ikut berubah)
                function applyRowUpdateOrder($input, resp) {
                    var $row = $input.closest('tr');

                    $row.find('[data-id_dtrbmasuk]').each(function() {
                        $(this).data('id_dtrbmasuk', resp.id_dtrbmasuk);
                        $(this).attr('data-id_dtrbmasuk', resp.id_dtrbmasuk);
                    });

                    if (typeof resp.qty_grosir !== 'undefined') {
                        $row.find('[data-qtygrosir_dtrbmasuk]').each(function() {
                            $(this).data('qtygrosir_dtrbmasuk', resp.qty_grosir);
                            $(this).attr('data-qtygrosir_dtrbmasuk', resp.qty_grosir);
                        });
                    }

                    if (typeof resp.hrgbelidisc_text !== 'undefined') {
                        $row.find('td').eq(10).text(resp.hrgbelidisc_text);
                    }
                    if (typeof resp.total_text !== 'undefined') {
                        $row.find('td').eq(12).text(resp.total_text);
                    }

                    if (typeof resp.subtotal !== 'undefined') {
                        document.getElementById('ttl_trkasir').value = resp.subtotal;
                        document.getElementById('ttl_harga_display').textContent = resp.subtotal;
                        HitungDP();
                    }
                }

                function tampilkanErrorOrder(xhr, $input, originalValue) {
                    var msg = 'Gagal menyimpan perubahan';
                    try {
                        var parsed = JSON.parse(xhr.responseText);
                        if (parsed && parsed.message) {
                            msg = parsed.message;
                        }
                    } catch (e) {}
                    alert(msg);
                    if ($input && typeof originalValue !== 'undefined') {
                        $input.val(originalValue);
                    }
                }

                function hitungSelisihBulanOrder(date1, date2) {
                    const selisihMilidetik = Math.abs(date2 - date1);
                    const satuHari = 1000 * 60 * 60 * 24;
                    return Math.floor(selisihMilidetik / satuHari);
                }

                $(document).ready(function() {
                    HitungDP();
                    $("#example5").DataTable();

                    // simpan nilai awal saat fokus, untuk rollback jika gagal / tidak valid
                    $('#example5 tbody').on('focus', 'input', function() {
                        $(this).data('original-value', $(this).val());
                    });

                    $('#example5 tbody').on('change', '#dqtygrosir_dtrbmasuk', function() {
                        var $input = $(this);
                        var originalValue = $input.data('original-value');
                        var id_dtrbmasuk = $input.data('id_dtrbmasuk');
                        var kd_barang = $input.data('kd_barang');
                        var qtygrosir_dtrbmasuk = $input.val();
                        var kd_orders = $('#kd_orders').val();
                        var kd_trbmasuk = $('#kd_trbmasuk').val();

                        if (qtygrosir_dtrbmasuk === '' || isNaN(qtygrosir_dtrbmasuk) || parseFloat(qtygrosir_dtrbmasuk) <= 0) {
                            alert('Qty Grosir harus diisi angka lebih dari 0');
                            $input.val(originalValue);
                            return;
                        }

                        $.ajax({
                            url: 'modul/mod_trbmasuk/simpandetail_qtygrosir_order.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'id_dtrbmasuk': id_dtrbmasuk,
                                'kd_trbmasuk': kd_trbmasuk,
                                'kd_orders': kd_orders,
                                'kd_barang': kd_barang,
                                'qtygrosir_dtrbmasuk': qtygrosir_dtrbmasuk,
                            },
                            success: function(resp) {
                                if (resp.status !== 'ok') {
                                    alert(resp.message || 'Gagal menyimpan perubahan');
                                    $input.val(originalValue);
                                    return;
                                }
                                applyRowUpdateOrder($input, resp);
                            },
                            error: function(xhr) {
                                tampilkanErrorOrder(xhr, $input, originalValue);
                            }
                        });
                    });

                    $('#example5 tbody').on('change', '#dhrgsat_dtrbmasuk', function() {
                        var $input = $(this);
                        var originalValue = $input.data('original-value');
                        var id_dtrbmasuk = $input.data('id_dtrbmasuk');
                        var kd_barang = $input.data('kd_barang');
                        var qtygrosir_dtrbmasuk = $input.data('qtygrosir_dtrbmasuk');
                        var hrgsat_dtrbmasuk = $input.val();
                        var kd_orders = $('#kd_orders').val();
                        var kd_trbmasuk = $('#kd_trbmasuk').val();

                        $.ajax({
                            url: 'modul/mod_trbmasuk/simpandetail_hrgbeli_order.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'id_dtrbmasuk': id_dtrbmasuk,
                                'kd_trbmasuk': kd_trbmasuk,
                                'kd_orders': kd_orders,
                                'kd_barang': kd_barang,
                                'hrgsat_dtrbmasuk': hrgsat_dtrbmasuk,
                                'qtygrosir_dtrbmasuk': qtygrosir_dtrbmasuk
                            },
                            success: function(resp) {
                                if (resp.status !== 'ok') {
                                    alert(resp.message || 'Gagal menyimpan perubahan');
                                    $input.val(originalValue);
                                    return;
                                }
                                applyRowUpdateOrder($input, resp);
                            },
                            error: function(xhr) {
                                tampilkanErrorOrder(xhr, $input, originalValue);
                            }
                        });
                    });

                    $('#example5 tbody').on('change', '#dkonversi', function() {
                        var $input = $(this);
                        var originalValue = $input.data('original-value');
                        var id_dtrbmasuk = $input.data('id_dtrbmasuk');
                        var kd_barang = $input.data('kd_barang');
                        var konversi = $input.val();
                        var qtygrosir_dtrbmasuk = $input.data('qtygrosir_dtrbmasuk');
                        var kd_orders = $('#kd_orders').val();
                        var kd_trbmasuk = $('#kd_trbmasuk').val();

                        if (konversi === '' || isNaN(konversi) || parseFloat(konversi) <= 0) {
                            alert('Konversi harus diisi angka lebih dari 0');
                            $input.val(originalValue);
                            return;
                        }

                        $.ajax({
                            url: 'modul/mod_trbmasuk/simpandetail_konversi_order.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'id_dtrbmasuk': id_dtrbmasuk,
                                'kd_trbmasuk': kd_trbmasuk,
                                'kd_orders': kd_orders,
                                'kd_barang': kd_barang,
                                'konversi': konversi,
                                'qtygrosir_dtrbmasuk': qtygrosir_dtrbmasuk
                            },
                            success: function(resp) {
                                if (resp.status !== 'ok') {
                                    alert(resp.message || 'Gagal menyimpan perubahan');
                                    $input.val(originalValue);
                                    return;
                                }
                                applyRowUpdateOrder($input, resp);
                            },
                            error: function(xhr) {
                                tampilkanErrorOrder(xhr, $input, originalValue);
                            }
                        });
                    });

                    $('#example5 tbody').on('change', '#dno_batch', function() {
                        var $input = $(this);
                        var originalValue = $input.data('original-value');
                        var id_dtrbmasuk = $input.data('id_dtrbmasuk');
                        var kd_barang = $input.data('kd_barang');
                        var no_batch = $input.val();
                        var qtygrosir_dtrbmasuk = $input.data('qtygrosir_dtrbmasuk');
                        var kd_orders = $('#kd_orders').val();
                        var kd_trbmasuk = $('#kd_trbmasuk').val();

                        $.ajax({
                            url: 'modul/mod_trbmasuk/simpandetail_batch_order.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'id_dtrbmasuk': id_dtrbmasuk,
                                'kd_trbmasuk': kd_trbmasuk,
                                'kd_orders': kd_orders,
                                'kd_barang': kd_barang,
                                'no_batch': no_batch,
                                'qtygrosir_dtrbmasuk': qtygrosir_dtrbmasuk,
                            },
                            success: function(resp) {
                                if (resp.status !== 'ok') {
                                    alert(resp.message || 'Gagal menyimpan perubahan');
                                    $input.val(originalValue);
                                    return;
                                }
                                applyRowUpdateOrder($input, resp);
                            },
                            error: function(xhr) {
                                tampilkanErrorOrder(xhr, $input, originalValue);
                            }
                        });
                    });

                    $('#example5 tbody').on('change', '#dexp_date', function() {
                        var $input = $(this);
                        var originalValue = $input.data('original-value');
                        var id_dtrbmasuk = $input.data('id_dtrbmasuk');
                        var kd_barang = $input.data('kd_barang');
                        var qtygrosir_dtrbmasuk = $input.data('qtygrosir_dtrbmasuk');
                        var exp_date = $input.val();
                        var kd_orders = $('#kd_orders').val();
                        var kd_trbmasuk = $('#kd_trbmasuk').val();
                        var tgl_trbmasuk = document.getElementById('tgl_trbmasuk').value;
                        var min_exp_date = document.getElementById('min_exp_date').value;

                        const tglAwal = new Date(tgl_trbmasuk);
                        const tglAkhir = new Date(exp_date);
                        const selisih = hitungSelisihBulanOrder(tglAwal, tglAkhir);

                        if (parseInt(selisih) < parseInt(min_exp_date)) {
                            alert('Minimum Expired Date ' + min_exp_date + ' Hari dari Hari Ini!');
                            $input.val(originalValue);
                            return false;
                        }

                        $.ajax({
                            url: 'modul/mod_trbmasuk/simpandetail_expdate_order.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'id_dtrbmasuk': id_dtrbmasuk,
                                'kd_trbmasuk': kd_trbmasuk,
                                'kd_orders': kd_orders,
                                'kd_barang': kd_barang,
                                'exp_date': exp_date,
                                'qtygrosir_dtrbmasuk': qtygrosir_dtrbmasuk
                            },
                            success: function(resp) {
                                if (resp.status !== 'ok') {
                                    alert(resp.message || 'Gagal menyimpan perubahan');
                                    $input.val(originalValue);
                                    return;
                                }
                                applyRowUpdateOrder($input, resp);
                            },
                            error: function(xhr) {
                                tampilkanErrorOrder(xhr, $input, originalValue);
                            }
                        });
                    });

                    $('#example5 tbody').on('change', '#dhrgjual_dtrbmasuk', function() {
                        var $input = $(this);
                        var originalValue = $input.data('original-value');
                        var id_dtrbmasuk = $input.data('id_dtrbmasuk');
                        var kd_barang = $input.data('kd_barang');
                        var qtygrosir_dtrbmasuk = $input.data('qtygrosir_dtrbmasuk');
                        var hrgjual_dtrbmasuk = $input.val();
                        var kd_orders = $('#kd_orders').val();
                        var kd_trbmasuk = $('#kd_trbmasuk').val();

                        $.ajax({
                            url: 'modul/mod_trbmasuk/simpandetail_hrgjual_order.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'id_dtrbmasuk': id_dtrbmasuk,
                                'kd_trbmasuk': kd_trbmasuk,
                                'kd_orders': kd_orders,
                                'kd_barang': kd_barang,
                                'hrgjual_dtrbmasuk': hrgjual_dtrbmasuk,
                                'qtygrosir_dtrbmasuk': qtygrosir_dtrbmasuk
                            },
                            success: function(resp) {
                                if (resp.status !== 'ok') {
                                    alert(resp.message || 'Gagal menyimpan perubahan');
                                    $input.val(originalValue);
                                    return;
                                }
                                applyRowUpdateOrder($input, resp);
                            },
                            error: function(xhr) {
                                tampilkanErrorOrder(xhr, $input, originalValue);
                            }
                        });
                    });

                    $('#example5 tbody').on('change', '#ddiskon', function() {
                        var $input = $(this);
                        var originalValue = $input.data('original-value');
                        var id_dtrbmasuk = $input.data('id_dtrbmasuk');
                        var kd_barang = $input.data('kd_barang');
                        var qtygrosir_dtrbmasuk = $input.data('qtygrosir_dtrbmasuk');
                        var diskon = $input.val();
                        var kd_orders = $('#kd_orders').val();
                        var kd_trbmasuk = $('#kd_trbmasuk').val();

                        $.ajax({
                            url: 'modul/mod_trbmasuk/simpandetail_diskon_order.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'id_dtrbmasuk': id_dtrbmasuk,
                                'kd_trbmasuk': kd_trbmasuk,
                                'kd_orders': kd_orders,
                                'kd_barang': kd_barang,
                                'diskon': diskon,
                                'qtygrosir_dtrbmasuk': qtygrosir_dtrbmasuk,
                            },
                            success: function(resp) {
                                if (resp.status !== 'ok') {
                                    alert(resp.message || 'Gagal menyimpan perubahan');
                                    $input.val(originalValue);
                                    return;
                                }
                                applyRowUpdateOrder($input, resp);
                            },
                            error: function(xhr) {
                                tampilkanErrorOrder(xhr, $input, originalValue);
                            }
                        });
                    });
                });

                // hapus baris tanpa reload tabel_detail1(), supaya halaman DataTable tidak reset ke halaman 1
                $(document).on('click', '#hapusorder', function() {

                    var $btn = $(this);
                    var id_dtrbmasuk = $btn.data('id_dtrbmasuk');
                    var kd_orders = $('#kd_orders').val();
                    var kd_trbmasuk = $('#kd_trbmasuk').val();

                    $.ajax({
                        type: 'post',
                        url: "modul/mod_trbmasuk/hapusdetail_order.php",
                        dataType: 'json',
                        data: {
                            id_dtrbmasuk: id_dtrbmasuk,
                            kd_orders: kd_orders,
                            kd_trbmasuk: kd_trbmasuk
                        },

                        success: function(resp) {
                            if (resp.status !== 'ok') {
                                alert(resp.message || 'Gagal menghapus data');
                                return;
                            }

                            var $row = $btn.closest('tr');
                            var table = $('#example5').DataTable();
                            table.row($row).remove().draw(false); // false = jangan reset ke halaman 1

                            document.getElementById('ttl_trkasir').value = resp.subtotal;
                            document.getElementById('ttl_harga_display').textContent = resp.subtotal;
                            HitungDP();
                        },
                        error: function(xhr) {
                            var msg = 'Gagal menghapus data';
                            try {
                                var parsed = JSON.parse(xhr.responseText);
                                if (parsed && parsed.message) {
                                    msg = parsed.message;
                                }
                            } catch (e) {}
                            alert(msg);
                        }
                    });

                });

                //hitung dp
                $('#dp_bayar').keydown(function(e) {
                    if (e.which == 13) {
                        HitungDP();
                    }
                });

                //rubah format rupiah
                function formatRupiah(angka) {
                    var reverse = angka.toString().split('').reverse().join(''),
                        ribuan = reverse.match(/\d{1,3}/g);
                    ribuan = ribuan.join('.').split('').reverse().join('');
                    return ribuan;
                }

                function HitungDP() {

                    var ttl_trkasir = document.getElementById('ttl_trkasir').value;
                    var dp_bayar = document.getElementById('dp_bayar').value;

                    if (ttl_trkasir == "") {
                        var ttl_trkasir = "0";
                    } else {}

                    if (dp_bayar == "") {
                        var dp_bayar = "0";
                    } else {}

                    var res1 = ttl_trkasir.replace(".", "");
                    var res2 = dp_bayar.replace(".", "");

                    var res1x = res1.replace(".", "");
                    var res2x = res2.replace(".", "");

                    var total2 = parseInt(res1x) - parseInt(res2x);

                    document.getElementById("dp_bayar").value = formatRupiah(dp_bayar);
                    document.getElementById("sisa_bayar").value = formatRupiah(total2);

                }

                //hitung diskon2
                $('#diskon2').keydown(function(e) {
                    if (e.which == 13) {
                        hitungdiskon();
                    }
                });

                function hitungdiskon() {

                    var sisa_bayar = document.getElementById('sisa_bayar').value;
                    var diskon2 = document.getElementById('diskon2').value;

                    if (diskon2 == "") {
                        var diskon2 = "0";
                    } else {}

                    var res1 = sisa_bayar.replace(".", "");
                    var res4 = diskon2.replace(".", "");

                    var res1x = res1.replace(".", "");
                    var res4x = res4.replace(".", "");

                    var total5 = Math.ceil(parseInt(res1x) * (1 - (parseInt(res4x) / 100)));

                    document.getElementById("diskon2").value = formatRupiah(diskon2);
                    document.getElementById("sisa_bayar").value = formatRupiah(total5);

                }

                $('#diskon_enter').on('click', function() {
                    let diskon = $('#dp_bayar').val();
                    let diskon2 = $('#diskon2').val();

                    if (diskon > 0 && diskon2 == 0) {
                        HitungDP();
                        $('#dp_bayar').attr('disabled', true);
                        $('#diskon2').attr('disabled', true);
                    } else if (diskon == 0 && diskon2 > 0) {
                        hitungdiskon();
                        $('#dp_bayar').attr('disabled', true);
                        $('#diskon2').attr('disabled', true);
                    } else {
                        alert('Hanya dibolehkan 1 opsi diskon !!!')
                    }
                })
            </script>
