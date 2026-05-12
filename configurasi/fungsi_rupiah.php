<?php
function format_rupiah($angka)
{
  if ($angka === null || $angka === '') {
    return '0';
  }
  $rupiah = number_format((float)$angka, 0, ",", ".");
  return $rupiah;
}

function teks($nilai) {
		$nilai = abs($nilai);
		$huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = teks($nilai - 10). " Belas";
		} else if ($nilai < 100) {
			$temp = teks($nilai/10)." Puluh". teks($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " Seratus" . teks($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = teks($nilai/100) . " Ratus" . teks($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " Seribu" . teks($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = teks($nilai/1000) . " Ribu" . teks($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = teks($nilai/1000000) . " Juta" . teks($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = teks($nilai/1000000000) . " Milyar" . teks(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = teks($nilai/1000000000000) . " Trilyun" . teks(fmod($nilai,1000000000000));
		}     
		return $temp;
	}
 
	function terbilang($nilai) {

		//Jika nilai minus - secara default langsung menambahkan teks minus
		if($nilai<0) {
			$hasil = "minus ". trim(teks($nilai));
		} else {
			$hasil = trim(teks($nilai));
		}     		
		return $hasil;
	}
	?>