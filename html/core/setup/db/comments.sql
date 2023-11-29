CREATE TABLE IF NOT EXISTS {{PRE}}comments (
  id_comment int(11) NOT NULL AUTO_INCREMENT,
  fid_user int(11) NOT NULL,
  fid_area int(11) NOT NULL,
  fid_comment int(11) NOT NULL,
  comment_area varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  comment_type varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  comment_datetime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  comment_comment varchar(512) NOT NULL,
  PRIMARY KEY (id_comment),
  KEY fid_area (fid_area),
  KEY fid_user (fid_user),
  KEY fid_comment (fid_comment)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
