CREATE TABLE konseling (
  id_konseling int(11) NOT NULL auto_increment,
  id_pelanggan int(11) NOT NULL,
  nm_pelanggan varchar(100) NOT NULL,  
  tgl_konseling date NOT NULL,  
  id_admin int(11) NOT NULL,
  nama_lengkap varchar(100) NOT NULL,
  nama_dokter varchar(100) NOT NULL,
  diagnosa text NOT NULL,    
  riwayat_penyakit text NOT NULL,
  riwayat_alergi text NOT NULL,
  keluhan text NOT NULL,
  visite  varchar(100) NOT NULL,
  tindakan text NOT NULL,  
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_konseling)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
