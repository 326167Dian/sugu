<script>
    function confirmdelete(delUrl) {
        if (confirm("Anda yakin ingin menghapus?")) {
            const params = new URLSearchParams(window.location.search);
            const module = params.get("module");
            document.location = delUrl+"&module2="+module;
        }
    }
</script>
<?php if ($_GET['module'] == 'home') { ?>
<script type="text/javascript">
    window.onload = function() {
        jam();
    }

    function jam() {
        var e = document.getElementById('jam'),
            d = new Date(),
            h, m, s;
        h = d.getHours();
        m = set(d.getMinutes());
        s = set(d.getSeconds());

        e.innerHTML = h + ':' + m + ':' + s;

        setTimeout('jam()', 1000);
    }

    function set(e) {
        e = e < 10 ? '0' + e : e;
        return e;
    }
</script>
<?php } ?>
<?php
include "../configurasi/koneksi.php";
include "../configurasi/library.php";
include "../configurasi/fungsi_indotgl.php";
include "../configurasi/fungsi_rupiah.php";
include "../configurasi/fungsi_combobox.php";
include "../configurasi/fungsi_logs.php";
include "../configurasi/class_paging.php";
$tgl_awal = date('d-m-Y');


// Bagian Home
if ($_GET['module'] == 'home') {

?>
    <!-- Small boxes (Stat box) -->

    <div class="box-body">
        <h1>SISTEM INVENTORY FOR APOTEK</h1>
        <div class="row">
            <div class="callout callout-info" style="margin:20px 20px 20px 20px">
                <h4><?php echo "Hai $_SESSION[namalengkap]"; ?></h4>
                <p><?php echo "Selamat datang di halaman SMART INVENTORY FOR APOTEK "; ?>
                    <BR>
                    Silahkan klik menu pilihan yang berada di sebelah kiri untuk mengelola aplikasi
                </p>
            </div>


            <div class="col-md-12">
                <div class="box box-primary" style="margin:20px;">
                    <div class="box-header with-border">
                        <h3 class="box-title" id="sales-chart-title">Penjualan <?php echo date('F Y'); ?></h3>
                        <div class="box-tools pull-right">
                            <select id="sales-filter-month" class="form-control input-sm" style="display:inline-block; width:auto; margin-right:6px;">
                                <?php for ($m = 1; $m <= 12; $m++) { ?>
                                    <option value="<?php echo $m; ?>" <?php echo ((int)date('n') == $m) ? 'selected' : ''; ?>><?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?></option>
                                <?php } ?>
                            </select>
                            <select id="sales-filter-year" class="form-control input-sm" style="display:inline-block; width:auto; margin-right:8px;">
                                <?php
                                $tahunSekarang = (int)date('Y');
                                for ($y = $tahunSekarang; $y >= $tahunSekarang - 5; $y--) {
                                ?>
                                    <option value="<?php echo $y; ?>" <?php echo ($tahunSekarang == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                <?php } ?>
                            </select>
                            <small id="sales-chart-updated" class="text-muted">Memuat data...</small>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row" style="margin-bottom:10px;">
                            <div class="col-sm-4">
                                <div class="small-box bg-aqua" style="margin-bottom:10px;">
                                    <div class="inner">
                                        <p style="margin:0;">Total Bulan Dipilih</p>
                                        <h4 id="sales-total-current" style="margin:4px 0 0 0;">0</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="small-box bg-yellow" style="margin-bottom:10px;">
                                    <div class="inner">
                                        <p style="margin:0;">Total Bulan Lalu</p>
                                        <h4 id="sales-total-previous" style="margin:4px 0 0 0;">0</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="small-box bg-green" style="margin-bottom:10px;">
                                    <div class="inner">
                                        <p style="margin:0;">Perubahan</p>
                                        <h4 id="sales-total-growth" style="margin:4px 0 0 0;">0%</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="sales-chart" style="width:100%;height:320px;"></div>
                    </div>
                </div>
            </div>


            <script type="text/javascript">
                var salesChartItems = [];
                var salesChartPreviousItems = [];
                var salesChartPreviousLabel = 'Bulan Lalu';
                var salesChartResizeTimer = null;

                function formatRupiahChart(angka) {
                    var nilai = parseFloat(angka || 0).toFixed(0).toString();
                    return nilai.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }

                function updateSalesSummary(currentItems, previousItems) {
                    var totalCurrent = 0;
                    var totalPrevious = 0;

                    for (var i = 0; i < currentItems.length; i++) {
                        totalCurrent += parseFloat(currentItems[i].total_penjualan || 0);
                    }

                    for (var j = 0; j < previousItems.length; j++) {
                        totalPrevious += parseFloat(previousItems[j].total_penjualan || 0);
                    }

                    var growth = 0;
                    if (totalPrevious > 0) {
                        growth = ((totalCurrent - totalPrevious) / totalPrevious) * 100;
                    } else if (totalCurrent > 0) {
                        growth = 100;
                    }

                    var growthLabel = (growth >= 0 ? '+' : '') + growth.toFixed(2) + '%';
                    var growthClass = 'bg-green';
                    if (growth < 0) {
                        growthClass = 'bg-red';
                    } else if (growth === 0) {
                        growthClass = 'bg-yellow';
                    }

                    $('#sales-total-current').text(formatRupiahChart(totalCurrent));
                    $('#sales-total-previous').text(formatRupiahChart(totalPrevious));
                    $('#sales-total-growth').text(growthLabel);
                    $('#sales-total-growth').closest('.small-box').removeClass('bg-green bg-red bg-yellow').addClass(growthClass);
                }

                function renderSalesChart(items, previousItems, previousLabel) {
                    if (!items || items.length === 0) {
                        $('#sales-chart').html('<div class="text-warning">Belum ada data penjualan pada tabel trkasir.</div>');
                        return;
                    }

                    var isMobileWidth = $(window).width() < 992;

                    var chartData = [];
                    var chartDataPrevious = [];
                    var ticks = [];

                    for (var i = 0; i < items.length; i++) {
                        var total = parseFloat(items[i].total_penjualan || 0);
                        var totalPrevious = 0;
                        if (previousItems && previousItems[i]) {
                            totalPrevious = parseFloat(previousItems[i].total_penjualan || 0);
                        }

                        var tanggalPenuh = items[i].tgl_trkasir || '';
                        var labelHari = tanggalPenuh;

                        if (tanggalPenuh.indexOf('-') > -1) {
                            var bagianTanggal = tanggalPenuh.split('-');
                            if (bagianTanggal.length === 3) {
                                labelHari = bagianTanggal[2];
                            }
                        }

                        chartData.push([i, total]);
                        chartDataPrevious.push([i, totalPrevious]);
                        ticks.push([i, labelHari]);
                    }

                    var plot = $.plot('#sales-chart', [{
                        label: 'Bulan Dipilih',
                        data: chartData,
                        lines: {
                            show: true,
                            lineWidth: 2,
                            fill: 0.15
                        },
                        points: {
                            show: true,
                            radius: 3
                        },
                        color: '#3c8dbc'
                    }, {
                        label: previousLabel || 'Bulan Lalu',
                        data: chartDataPrevious,
                        lines: {
                            show: true,
                            lineWidth: 2,
                            fill: false
                        },
                        points: {
                            show: true,
                            radius: 2
                        },
                        color: '#f39c12'
                    }], {
                        legend: {
                            show: true,
                            position: 'ne'
                        },
                        grid: {
                            hoverable: true,
                            borderWidth: 1,
                            borderColor: '#e5e5e5'
                        },
                        xaxis: {
                            ticks: ticks,
                            tickLength: 0
                        },
                        yaxis: {
                            min: 0,
                            tickFormatter: function(value) {
                                return formatRupiahChart(value);
                            }
                        }
                    });

                    var $chart = $('#sales-chart');
                    $chart.css('position', 'relative');
                    $chart.find('.point-value-label').remove();

                    if (isMobileWidth) {
                        return;
                    }

                    for (var j = 0; j < chartData.length; j++) {
                        var point = chartData[j];
                        var offset = plot.pointOffset({ x: point[0], y: point[1] });
                        var labelHtml = '<div class="point-value-label" style="position:absolute;left:' + (offset.left - 18) + 'px;top:' + (offset.top - 22) + 'px;font-size:11px;color:#333;background:#fff;padding:1px 4px;border:1px solid #d2d6de;border-radius:3px;white-space:nowrap;">' + formatRupiahChart(point[1]) + '</div>';
                        $chart.append(labelHtml);
                    }
                }

                function loadSalesChart() {
                    if (typeof $.plot !== 'function') {
                        $('#sales-chart').html('<div class="text-warning">Library grafik belum termuat.</div>');
                        return;
                    }

                    var selectedMonth = $('#sales-filter-month').val();
                    var selectedYear = $('#sales-filter-year').val();

                    $.ajax({
                        url: 'ajax_penjualan_realtime.php',
                        method: 'GET',
                        data: {
                            bulan: selectedMonth,
                            tahun: selectedYear
                        },
                        dataType: 'json',
                        cache: false,
                        success: function(response) {
                            if (!response.status) {
                                $('#sales-chart').html('<div class="text-danger">Data penjualan tidak tersedia.</div>');
                                $('#sales-chart-updated').text(response.message || 'Gagal membaca data');
                                return;
                            }

                            salesChartItems = response.data || [];
                            salesChartPreviousItems = response.data_bulan_lalu || [];
                            salesChartPreviousLabel = response.periode_sebelumnya_label || 'Bulan Lalu';
                            renderSalesChart(salesChartItems, salesChartPreviousItems, salesChartPreviousLabel);
                            updateSalesSummary(salesChartItems, salesChartPreviousItems);
                            if (response.periode_label) {
                                var title = 'Penjualan ' + response.periode_label;
                                if (response.periode_sebelumnya_label) {
                                    title += ' vs ' + response.periode_sebelumnya_label;
                                }
                                $('#sales-chart-title').text(title);
                            }
                            $('#sales-chart-updated').text('Update terakhir: ' + (response.updated_at || '-'));
                        },
                        error: function() {
                            $('#sales-chart').html('<div class="text-danger">Gagal memuat data grafik.</div>');
                            $('#sales-total-current').text('0');
                            $('#sales-total-previous').text('0');
                            $('#sales-total-growth').text('0%');
                        }
                    });
                }

                function bindSalesChartResize() {
                    $(window).off('resize.salesChart').on('resize.salesChart', function() {
                        clearTimeout(salesChartResizeTimer);
                        salesChartResizeTimer = setTimeout(function() {
                            if (salesChartItems && salesChartItems.length > 0) {
                                renderSalesChart(salesChartItems, salesChartPreviousItems, salesChartPreviousLabel);
                            }
                        }, 200);
                    });
                }

                $(function() {
                    bindSalesChartResize();

                    $('#sales-filter-month, #sales-filter-year').on('change', function() {
                        loadSalesChart();
                    });

                    if (typeof $.plot === 'function') {
                        loadSalesChart();
                        return;
                    }

                    $.getScript('plugins/flot/jquery.flot.min.js')
                        .done(function() {
                            loadSalesChart();
                        })
                        .fail(function() {
                            $('#sales-chart').html('<div class="text-danger">Gagal memuat library grafik (Flot).</div>');
                        });
                });
            </script>



        </div>

    <?php
    echo "<p align=right>Login : $hari_ini, $tgl_awal <br>
   <b><span id=\"jam\" style=\"font-size:24\"></span></b></p>
  <span id='date'></span>, <span id='clock'></span></p>
  </div>
 
 ";
}
// Bagian user admin
elseif ($_GET['module'] == 'admin') {
    include "modul/mod_admin/admin.php";
}

// Bagian setheader
elseif ($_GET['module'] == 'setheader') {
    include "modul/mod_setheader/setheader.php";
}

// Bagian satuan
elseif ($_GET['module'] == 'satuan') {
    include "modul/mod_satuan/satuan.php";
}

// Bagian jenisobat
elseif ($_GET['module'] == 'jenisobat') {
    include "modul/mod_jenisobat/jenisobat.php";
}
// Bagian profil
elseif ($_GET['module'] == 'profil') {
    include "modul/mod_profil/profil.php";
}

// Bagian pelanggan
elseif ($_GET['module'] == 'pelanggan') {
    include "modul/mod_pelanggan/pelanggan.php";
}

// Bagian konseling
elseif ($_GET['module'] == 'konseling') {
    include "modul/mod_konseling/konseling.php";
}

// Bagian meso
elseif ($_GET['module'] == 'meso') {
    include "modul/mod_meso/meso.php";
}

// Bagian pio
elseif ($_GET['module'] == 'pio') {
    include "modul/mod_pio/pio.php";
}

// Bagian pto
elseif ($_GET['module'] == 'pto') {
    include "modul/mod_pemantauan_terapi_obat/pto.php";
}

// Bagian cpp
elseif ($_GET['module'] == 'cpp') {
    include "modul/mod_cpp/cpp.php";
}

// Bagian homecare
elseif ($_GET['module'] == 'homecare') {
    include "modul/mod_homecare/homecare.php";
}

// Bagian swamedikasi
elseif ($_GET['module'] == 'swamedikasi') {
    include "modul/mod_swamedikasi/swamedikasi.php";
}

// Bagian supplier
elseif ($_GET['module'] == 'supplier') {
    include "modul/mod_supplier/supplier.php";
}

// Bagian carabayar
elseif ($_GET['module'] == 'carabayar') {
    include "modul/mod_carabayar/carabayar.php";
}

// Bagian barang
elseif ($_GET['module'] == 'barang') {
    include "modul/mod_barang/barang.php";
}

// Bagian zataktif
elseif ($_GET['module'] == 'zataktif') {
    include "modul/mod_zataktif/zataktif.php";
}

// Bagian trbmasuk
elseif ($_GET['module'] == 'trbmasuk') {
    include "modul/mod_trbmasuk/trbmasuk.php";
}
// Bagian trbmasuk
elseif ($_GET['module'] == 'trbmasukpbf') {
    include "modul/mod_trbmasukpbf/trbmasukpbf.php";
}

// Bagian trkasir
elseif ($_GET['module'] == 'trkasir') {
    include "modul/mod_trkasir/trkasir.php";
}

// Bagian lapbarang
elseif ($_GET['module'] == 'lapbarang') {
    include "modul/mod_lapbarang/lapbarang.php";
}

// Bagian lappenjualan
elseif ($_GET['module'] == 'lappenjualan') {
    include "modul/mod_lappenjualan/lappenjualan.php";
}

// Bagian lap brg masuk
elseif ($_GET['module'] == 'lapbrgmasuk') {
    include "modul/mod_lapbrgmasuk/lapbrgmasuk.php";
}

// Bagian lap Stok Opname
elseif ($_GET['module'] == 'lapstokopname') {
    include "modul/mod_laporan/laporan_stokopname.php";
}
// Bagian Stok Opname Harian

// Bagian nilai stok barang
elseif ($_GET['module'] == 'lapstok') {
    include "modul/mod_lapstok/lapstok.php";
} // Bagian nilai stok kritis
elseif ($_GET['module'] == 'stok_kritis') {
    include "modul/mod_lapstok/stok_kritis.php";
}// Bagian Kartu Stok
elseif ($_GET['module'] == 'kartustok') {
    include "modul/mod_kartustok/kartu_stok.php";
}

// Bagian orders
elseif ($_GET['module'] == 'orders') {
    include "modul/mod_orders/orders.php";
} // Penjualan sebelumnya
elseif ($_GET['module'] == 'penjualansebelumnya') {
    include "modul/mod_trkasir/trkasir_tes.php";
}
//Laba Penjualan
elseif ($_GET['module'] == 'labapenjualan') {
    include "modul/mod_lappenjualan/labapenjualan.php";
}
//Terima Barang Masuk Oleh Manager
elseif ($_GET['module'] == 'byrkredit') {
    include "modul/mod_trbmasuk/byrkredit.php";
} elseif ($_GET['module'] == 'byrkreditpbf') {
    include "modul/mod_trbmasukpbf/byrkredit.php";
}

//Stok Opname
// elseif ($_GET['module'] == 'stokopname') {
//     include "modul/mod_stokopname/stokopname_harian.php";
// }
elseif ($_GET['module'] == 'stokopname') {
    include "modul/mod_lapstok/stokopname.php";
} elseif ($_GET['module'] == 'soharian') {
    // include "modul/mod_lapstok/soharian.php";
    include "modul/mod_stokopname/stokopname_harian.php";
} elseif ($_GET['module'] == 'labajenisobat') {
    include "modul/mod_lappenjualan/labapenjualanjenisobat.php";
}
//koreksi stok karena sistem
elseif ($_GET['module'] == 'koreksistok') {
    include "modul/mod_lapstok/koreksistok.php";
} //input shiftkerja
elseif ($_GET['module'] == 'shiftkerja') {
    include "modul/mod_shiftkerja/shiftkerja.php";
}

// neraca
elseif ($_GET['module'] == 'neraca') {
    include "modul/mod_laporan/neraca.php";
}

// komisi
elseif ($_GET['module'] == 'komisi') {
    include "modul/mod_komisi/komisi.php";
} 
elseif ($_GET['module'] == 'lapkomisi') {
    include "modul/mod_komisi/lapkomisi.php";
}

// catatan
elseif ($_GET['module'] == 'catatan') {
    include "modul/mod_catatan/catatan1.php";
} 
// cek darah
elseif ($_GET['module'] == 'cekdarah') {
    include "modul/mod_cekdarah/cekdarah.php";

// Jurnal Kas
} elseif ($_GET['module'] == 'jurnalkas') {
    include "modul/mod_jurnalkas/jurnalkas.php";
}
// evaluasi pegawai
elseif ($_GET['module'] == 'evaluasi') {
    include "modul/mod_evaluasi/lapevaluasi.php";
}
// Bagian Bundling
elseif ($_GET['module'] == 'bundle') {
    include "modul/mod_bundle/bundle.php";
}
// Laporan Batch
elseif ($_GET['module'] == 'lapbatch') {
    include "modul/mod_batch/laporan_batch.php";
}

// $getkdon = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT kd_trkasir FROM kdtk WHERE id_admin = '$_SESSION[idadmin]' AND stt_kdtk ='ON' ORDER BY id_kdtk DESC");
// $kdon = mysqli_fetch_array($getkdon);

// if ($_GET['module'] != 'trkasir') {
//     $getkasirdetail = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT kd_trkasir FROM trkasir_detail WHERE kd_trkasir = '$kdon[kd_trkasir]'");
//     $trkasir_detail = mysqli_num_rows($getkasirdetail);

//     if ($trkasir_detail > 0) {
//         $gettrkasir = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT kd_trkasir FROM trkasir WHERE kd_trkasir = '$kdon[kd_trkasir]'");
//         $trkasir = mysqli_num_rows($gettrkasir);

//         if ($trkasir <= 0) {
//             echo "<script type='text/javascript'>alert('Harap untuk mengklik simpan transaksi !');window.location='?module=trkasir&act=tambah'</script>";
//         }
//     }
// }

    ?>