CREATE TABLE IF NOT EXISTS {{PRE}}payments (
  id_payment int(11) NOT NULL AUTO_INCREMENT,
  fid_user int(11) NOT NULL,
  fid_address_billing int(11) NOT NULL,
  fid_address_shipping int(11) NOT NULL,
  payment_status varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'P',
  payment_amount float NOT NULL,
  payment_merchant varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  payment_method varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  payment_ip varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  payment_response varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  payment_reference varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  payment_subscription varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  payment_lastfour varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  payment_started datetime NOT NULL,
  payment_completed timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  payment_source varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  payment_comment varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_payment),
  KEY fid_user (fid_user),
  KEY fid_address_billing (fid_address_billing),
  KEY fid_address_shipping (fid_address_shipping),
  KEY payment_status (payment_status),
  KEY payment_amount (payment_amount),
  KEY payment_merchant (payment_merchant),
  KEY payment_method (payment_method),
  KEY payment_ip (payment_ip),
  KEY payment_reference (payment_reference),
  KEY payment_subscription (payment_subscription),
  KEY payment_completed (payment_completed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;