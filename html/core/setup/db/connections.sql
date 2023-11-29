CREATE TABLE IF NOT EXISTS {{PRE}}connections (
  id_connection int(11) NOT NULL,
  fid_master int(11) NOT NULL,
  fid_slave int(11) NOT NULL,
  connection_type varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  connection_datetime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_connection),
  KEY fid_master (fid_master),
  KEY fid_slave (fid_slave),
  KEY connection_type (connection_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
