<?php
    include "koneksi.php";
    
    function get_batch_fifo($qty, $kd_barang){
        global $db;
        
        $stmt = $db->prepare("SELECT 
                              no_batch, 
                              exp_date, 
                              MIN(tgl_transaksi) AS tgl_awal, 
                              SUM(
                                CASE WHEN status = 'masuk' THEN qty ELSE 0 END
                              ) AS total_masuk, 
                              SUM(
                                CASE WHEN status = 'keluar' THEN qty ELSE 0 END
                              ) AS total_keluar, 
                              SUM(
                                CASE WHEN status = 'masuk' THEN qty ELSE 0 END
                              ) - SUM(
                                CASE WHEN status = 'keluar' THEN qty ELSE 0 END
                              ) AS sisa_qty 
                            FROM 
                              batch 
                            WHERE 
                              kd_barang = ?  
                            GROUP BY 
                              no_batch 
                            HAVING 
                              sisa_qty > 0 
                            ORDER BY 
                              CASE WHEN exp_date = '0000-00-00' THEN '9999-12-31' ELSE exp_date END ASC, 
                              exp_date ASC");
        $stmt->execute([$kd_barang]);
        
        // $stmt = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT 
        //                       no_batch, 
        //                       exp_date, 
        //                       MIN(tgl_transaksi) AS tgl_awal, 
        //                       SUM(
        //                         CASE WHEN status = 'masuk' THEN qty ELSE 0 END
        //                       ) AS total_masuk, 
        //                       SUM(
        //                         CASE WHEN status = 'keluar' THEN qty ELSE 0 END
        //                       ) AS total_keluar, 
        //                       SUM(
        //                         CASE WHEN status = 'masuk' THEN qty ELSE 0 END
        //                       ) - SUM(
        //                         CASE WHEN status = 'keluar' THEN qty ELSE 0 END
        //                       ) AS sisa_qty 
        //                     FROM 
        //                       batch 
        //                     WHERE 
        //                       kd_barang = '$kd_barang'  
        //                     GROUP BY 
        //                       no_batch 
        //                     HAVING 
        //                       sisa_qty > 0 
        //                     ORDER BY 
        //                       CASE WHEN exp_date = '0000-00-00' THEN '9999-12-31' ELSE exp_date END ASC, 
        //                       exp_date ASC;");
        
        
        // $cek = mysqli_num_rows($stmt);
        $cek = $stmt->rowCount();;
        
        
        $data = array();
        if ($cek > 0) {
            $kebutuhan = $qty; // qty yang ingin diambil
            $batch_first="";
            $sisa = 0;
            
            // while ($r1 = mysqli_fetch_assoc($stmt)) {
            while ($r1 = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
                if ($kebutuhan <= 0) {
                    break; // sudah cukup → stop
                }
            
                if ($r1['sisa_qty'] <= $kebutuhan) {
                    // ambil semua batch ini
                    $ambil = $r1['sisa_qty'];
                } else {
                    // ambil sebagian
                    $ambil = $kebutuhan;
                }
            
                $batch_first = $r1['no_batch'];
                $sisa = $r1['sisa_qty'] - $ambil;
                $data[] = array(
                    'no_batch'   => $r1['no_batch'],
                    'exp_date'   => $r1['exp_date'],
                    'qty_ambil'  => $ambil
                );
            
                // kurangi kebutuhan
                $kebutuhan -= $ambil;
            }
            
            if ($kebutuhan > 0 && $sisa == 0) {
                
                $data[] = array(
                            'no_batch'   => '',
                            'exp_date'   => '0000-00-00',
                            'qty_ambil'  => $kebutuhan
                        );
            }
             
        } else {
            $kode = 0;
            $data[] = array(
                            'no_batch'   => '',
                            'exp_date'   => '0000-00-00',
                            'qty_ambil'  => $qty
                        );
        }
        
        
        return json_encode($data);
    }
    
?>