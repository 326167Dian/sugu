
CREATE TABLE orders (
  id_trbmasuk int(11) NOT NULL,
  id_resto varchar(11) NOT NULL,
  kd_trbmasuk varchar(100) NOT NULL,
  tgl_trbmasuk date NOT NULL,
  id_supplier int(11) NOT NULL,
  nm_supplier varchar(50) NOT NULL,
  tlp_supplier varchar(50) NOT NULL,
  alamat_trbmasuk text NOT NULL,
  ttl_trbmasuk double NOT NULL,
  dp_bayar double NOT NULL,
  sisa_bayar double NOT NULL,
  ket_trbmasuk text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

