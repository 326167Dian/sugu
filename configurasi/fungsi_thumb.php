<?php

// UPLOAD FILE KONTRAK
function UploadFile_Kontrak($fupload_name){
  //direktori gambar
  $vdir_upload = "../../../dok_kontrak/";
  $vfile_upload = $vdir_upload . $fupload_name;
  move_uploaded_file($_FILES["fupload1"]["tmp_name"], $vfile_upload);
}

// UPLOAD FILE GARANSI
function UploadFile_Garansi($fupload_name){
  //direktori gambar
  $vdir_upload = "../../../dok_garansi/";
  $vfile_upload = $vdir_upload . $fupload_name;
  move_uploaded_file($_FILES["fupload1"]["tmp_name"], $vfile_upload);
}

// UPLOAD FILE SERTIFIKAT
function UploadFile_Sertifikat($fupload_name){
  //direktori gambar
  $vdir_upload = "../../../dok_sertifikat/";
  $vfile_upload = $vdir_upload . $fupload_name;
  move_uploaded_file($_FILES["fupload1"]["tmp_name"], $vfile_upload);
}

// UPLOAD FILE KALIBRASI
function UploadFile_Kalibrasi($fupload_name){
  //direktori gambar
  $vdir_upload = "../../../dok_kalibrasi/";
  $vfile_upload = $vdir_upload . $fupload_name;
  move_uploaded_file($_FILES["fupload1"]["tmp_name"], $vfile_upload);
}

// UPLOAD LOGO HEADER STRUK
function UploadLogo_Struk($fupload_name){
  //direktori gambar
  $vdir_upload = "../../images/";
  $vfile_upload = $vdir_upload . $fupload_name;
  move_uploaded_file($_FILES["fupload1"]["tmp_name"], $vfile_upload);
}

// UPLOAD TANDA TANGAN STRUK
function UploadTandaTangan_Struk($fupload_name){
  //direktori gambar
  $vdir_upload = "../../images/";
  $vfile_upload = $vdir_upload . $fupload_name;
  move_uploaded_file($_FILES["fupload2"]["tmp_name"], $vfile_upload);
}


?>
