<?php
include "../../../configurasi/koneksi.php";
$kd_barang = $_GET['id'];
$date = date('Y-m-d', time());

if ($_GET['action'] == "table_data") {

    $columns = array(
        0 => 'id_batch',
        1 => 'kd_barang',
        2 => 'nm_barang',
        3 => 'no_batch',
        4 => 'exp_date',
        5 => 'qty',
        6 => 'id_batch',
    );

    if($kd_barang != ''){
        $querycount = $db->prepare("SELECT count(id_batch) as jumlah FROM batch WHERE kd_barang = '$kd_barang' and status = 'masuk'  GROUP BY batch.kd_barang");
        $querycount->execute();
        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
    } else {
        $querycount = $db->prepare("SELECT count(id_batch) as jumlah FROM batch WHERE status = 'masuk'  GROUP BY batch.kd_barang");
        $querycount->execute();
        $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
    }

    $totalData = $datacount['jumlah'];

    $totalFiltered = $totalData;

    $limit = $_POST['length'];
    $start = $_POST['start'];
    $order = $columns[$_POST['order']['0']['column']];
    $dir = $_POST['order']['0']['dir'];

    if (empty($_POST['search']['value'])) {
        if($kd_barang != ''){
            $query = $db->prepare("SELECT batch.*, barang.nm_barang, SUM(batch.qty) as qty
                FROM batch JOIN barang ON batch.kd_barang = barang.kd_barang 
                WHERE batch.kd_barang = '$kd_barang' AND batch.status = 'masuk' GROUP BY batch.no_batch ORDER BY batch.exp_date ASC LIMIT $limit OFFSET $start");
        } else {
            $query = $db->prepare("SELECT batch.*, barang.nm_barang, SUM(batch.qty) as qty
                FROM batch JOIN barang ON batch.kd_barang = barang.kd_barang 
                WHERE batch.status = 'masuk' GROUP BY batch.no_batch ORDER BY batch.exp_date ASC LIMIT $limit OFFSET $start");
            
        }
    } else {
        $search = $_POST['search']['value'];
        if($kd_barang != ''){
            $query = $db->prepare("SELECT batch.*, barang.nm_barang, SUM(batch.qty) as qty
                FROM batch JOIN barang ON batch.kd_barang = barang.kd_barang 
                WHERE batch.status = 'masuk' AND barang.nm_barang LIKE '%$search%'
                            OR batch.no_batch LIKE '%$search%'
                            OR batch.exp_date LIKE '%$search%'
                GROUP BY batch.no_batch
                ORDER BY batch.exp_date ASC LIMIT $limit OFFSET $start");
    
            $querycount = $db->prepare("SELECT count(id_batch) as jumlah 
                FROM batch JOIN barang ON batch.kd_barang = barang.kd_barang 
                WHERE  batch.status = 'masuk' AND barang.nm_barang LIKE '%$search%'
                            OR batch.no_batch LIKE '%$search%'
                            OR batch.exp_date LIKE '%$search%'  
                GROUP BY batch.no_batch");
            
            $querycount->execute();
            $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
            $totalFiltered = $datacount['jumlah'];
        } else {
            $query = $db->prepare("SELECT batch.*, barang.nm_barang, SUM(batch.qty) as qty
                FROM batch JOIN barang ON batch.kd_barang = barang.kd_barang 
                WHERE batch.status = 'masuk' AND barang.nm_barang LIKE '%$search%'
                            OR batch.no_batch LIKE '%$search%'
                            OR batch.exp_date LIKE '%$search%'
                GROUP BY batch.no_batch
                ORDER BY $order $dir LIMIT $limit OFFSET $start");
    
            $querycount = $db->prepare("SELECT count(id_batch) as jumlah 
                FROM batch JOIN barang ON batch.kd_barang = barang.kd_barang 
                WHERE  batch.status = 'masuk' AND barang.nm_barang LIKE '%$search%'
                            OR batch.no_batch LIKE '%$search%'
                            OR batch.exp_date LIKE '%$search%'  
                GROUP BY batch.no_batch");
    
            $querycount->execute();
            $datacount = $querycount->fetch(PDO::FETCH_ASSOC);
            $totalFiltered = $datacount['jumlah'];
            
        }
    }

    $data = array();
    if (!empty($query)) {
        $no = $start + 1;
        $query->execute();
        while ($value = $query->fetch(PDO::FETCH_ASSOC)) {
            if($value['exp_date'] > $date){
                $caribatch = $db->prepare("SELECT SUM(qty) AS jumlBatch FROM batch WHERE 
                                            kd_barang = '$value[kd_barang]'
                                            AND no_batch = '$value[no_batch]' 
                                            AND status = 'keluar'");
                $caribatch->execute();
                $result = $caribatch->fetch(PDO::FETCH_ASSOC);
                $selisihQty = $value['qty'] - $result['jumlBatch'];
                
                if($selisihQty > 0){
                    $nestedData['no']           = $no;
                    $nestedData['kd_barang']    = $value['kd_barang'];
                    $nestedData['nm_barang']    = $value['nm_barang'];
                    $nestedData['no_batch']     = $value['no_batch'];
                    $nestedData['exp_date']     = $value['exp_date'];
                    // $nestedData['qty']          = $value['qty'];
                    $nestedData['qty']          = $selisihQty;
                    
                    // if($value['exp_date'] < $date){
                    //     $nestedData['pilih'] = 'expired';
                        
                    // } else {
                        $nestedData['pilih'] = "<button class='btn btn-xs btn-info' id='pilihbatch'
                        data-id_batch='$value[id_batch]'
                        data-kd_barang='$value[kd_barang]'
                        data-nm_barang='$value[nm_barang]'
                        data-no_batch='$value[no_batch]'
                        data-exp_date='$value[exp_date]'
                        data-qtybatch='$selisihQty'
                        >
                        <i class='fa fa-check'></i>
                        </button>";
                        
                    // }
                    $data[] = $nestedData;
                    $no++;
                }
                
            }
            
        }
    }

    $json_data = [
        "draw"            => intval($_POST['draw']),
        "recordsTotal"    => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data"            => $data
    ];

    echo json_encode($json_data);
}
