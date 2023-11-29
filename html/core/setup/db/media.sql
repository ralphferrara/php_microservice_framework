CREATE TABLE IF NOT EXISTS {{PRE}}media (
  id_media int(11) NOT NULL AUTO_INCREMENT,
  fid_user int(11) NOT NULL,
  fid_area int(11) NOT NULL,
  media_area varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  media_type varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  media_datetime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  media_caption varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  media_status varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'P',
  media_tags varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id_media),
  KEY fid_area (fid_area),
  KEY fid_user (fid_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
