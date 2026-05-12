

CREATE TABLE ordersdetail (
  id_dtrbmasuk int(11) NOT NULL,
  kd_trbmasuk varchar(100) NOT NULL,
  id_barang int(11) NOT NULL,
  kd_barang varchar(50) NOT NULL,
  nmbrg_dtrbmasuk varchar(100) NOT NULL,
  qty_dtrbmasuk double NOT NULL,
  sat_dtrbmasuk varchar(30) NOT NULL,
  hrgsat_dtrbmasuk double NOT NULL,
  hrgttl_dtrbmasuk double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

