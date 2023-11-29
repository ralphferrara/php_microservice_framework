CREATE TABLE IF NOT EXISTS {{PRE}}addresses (
  id_address int(11) NOT NULL AUTO_INCREMENT,
  address_fname varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  address_lname varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  address_address1 varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  address_address2 varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  address_city varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  address_state varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  address_zip varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  address_country varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  address_phone varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
