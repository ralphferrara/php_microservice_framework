<?php
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Syhora :: Core3
//|| Editor Class
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	if (!EJ::isNew()) {
		global $validator;
		$validator = new Validation;
	}

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
//|| Class
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

	class EJ {

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| isNew
		//|| Determine if we are using CoreTree vs Core3
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function isNew() {
			return (defined('CORE_MAJOR') && CORE_MAJOR >= 3);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Get
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function get($key, $value = null) {
			if (self::isNew()) 	return Core::get($key, $value);
			global $core;
			return ($value === null) ? $core->get->get($key) : $core->get->set($key, $value);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Post
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function post($key, $value = null) {
			if (self::isNew()) return Core::post($key, $value);
			global $core;
			return ($value === null) ? $core->post->get($key) : $core->post->set($key, $value);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Ajax Response
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function ajax($status, $data = null, $error = null) {
			switch($status) {
				case 'error' 	: if (self::isNew()) return Ajax::fail($error); 	else die(json_encode(array('status' => 'FAIL', 'error' => $error, 'data' => $data))); break;
				case 'success' 	: if (self::isNew()) return Ajax::success($data); 	else die(json_encode(array('status' => 'OK',   'data' => $data))); break;
				default 		: die(json_encode(array('status' => 'FAIL', 'error' => 'Junction :: Unknown AJAX Response', 'data' => array()))); break;
			}
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Config
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function config($key) {
			if (self::isNew()) return Core::config($key);
			global $core;
			return $core->config($key);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| User
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function user() {
		    if (Internet::domain('justdomain') == 'affiliates.socialco.media') return -1;
			if (self::isNew()) return Core::user('id');
			global $user;
			return $user->id;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Post
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function hash($value) {
			if (self::isNew()) return Encryption::hash($value);
			global $core;
			return Encryption::mde($core->config('SALT_GENERAL').$value.$core->config('SALT_GENERAL'));
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Root
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function root() {
			if (self::isNew()) return Core::config('root');
			return root;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| DB Query
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function query($sql, $alias = 'main') {
			if (self::isNew()) return Core::db($alias)->query($sql);
			global $core;
			return $core->db->query($sql);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| DB Query
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function id($alias = 'main') {
			if (self::isNew())  return Core::db($alias)->id();
			global $core;
			return $core->db->lastid();
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Memcache
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function media($fid, $size, $ext, $area, $response) {
			if (self::isNew())  return Media::filename($fid, $size, $ext, $area, 'fullpath');
			return Media::fullpath($fid, $size, $ext, $area);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Media Size
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function mediasize($filename, $size) {
			if (self::isNew())  return Media::size($filename, $size);
			$ext = File::extension($filename);
			return str_replace('.'.$ext, '_' . $size.'.'.$ext, $filename);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Memcache
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function cache($key, $value = null, $timeout = 3600) {
			if (self::isNew()) return ($value === null) ? Core::cache()->get($key) : Core::cache()->set($key, $value, $timeout);
			global $core;
			return ($value === null) ? $core->mdb->get($key) : $core->mdb->set($key, $value, $timeout);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Validation Message
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function message($type, $message = null) {
			if (self::isNew()) return Validation::message($type, $message);
			$v = new Validation;
			return $v->message($type, ($message === null) ? '' :$message);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Validation Check
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function check($type, $data, $custom = array()) {
			if (self::isNew()) return Validation::check($type, $data, $custom);
			global $validator;
			return $validator->check($type, $data, Str::ifset($custom, 'field'), Str::ifset($custom, 'message'), array('data' => Str::ifset($custom, 'data')));
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Validation errored
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function errored() {
			if (self::isNew()) return Validation::errored();
			global $validator;
			return $validator->errored;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Safe integer
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function int($value, $check = false) {
			if (self::isNew()) return ($check) ? Safe::check($value) : Safe::int($value);
			return Connection::safeint($value, $check);

		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Directory
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function directory($area = 'upload') {
		 	switch($area) {
			}
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Call
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function call($function, $data = array()) {
			if (self::isNew()) return Core::call($function, $data);
			return Functions::call($function, $data);

		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Safe text
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function text($value) {
			if (self::isNew()) return Safe::text($value);
			return Connection::safetext($value);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| To Plain
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function csv($value) {
			if (self::isNew()) return Str::csv_array($value);
			return Str::csvtoarray($value);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| To Plain
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function plain($value) {
			if (self::isNew()) return HTML::toPlain($value, null, false);
			return TextSave::toPlain($value);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| To Plain
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function html($value) {
			if (self::isNew()) return HTML::toHTML($value);
			return TextSave::toHTML($value);
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Convert to HTML
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function fromTags($tags, $custom = array()) {
			if (trim($tags) == '') return '';
			$list = explode(',',$tags);
			if (!is_array($list) || count($list) == 0) return ','.strtolower(self::html($tags)).',';
			for($i=0;$i<count($list);$i++) 	$list[$i] = strtolower(self::html($list[$i]));
			$list = array_filter($list);
			if (!is_array(Str::ifset($custom, 'safe')))   $new = $list; else { $new = array(); for($i=0;$i<count($list);$i++) if (in_array($list[$i], $custom['safe'])) $new[] = $list[$i]; }
			if (!is_array(Str::ifset($custom, 'remove'))) $new = $list; else { $new = array(); for($i=0;$i<count($list);$i++) if (!in_array($list[$i], $custom['remove'])) $new[] = $list[$i]; }
			if (Str::ifset($custom, 'max', -1) > -1 && count($list) > Str::ifset($custom, 'max')) $list = array_slice($list, 0, Str::ifset($custom, 'max'));
			if (Str::ifset($custom, 'length', -1) > -1) { $temp = ''; $new  = array(); for($i=0;$i<count($list);$i++) { $temp .= $list[$i]; if (strlen($temp) <= $custom['length']) $new[] = $list[$i]; } $list = $new; }
			return ','.implode(',', $list).',';
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Convert to HTML
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function toTags($tags, $custom = array()) {
			if (trim($tags) == '') return '';
			$list = explode(',',$tags);
			if (!is_array($list) || count($list) == 0) return ','.strtolower(self::html($tags)).',';
			for($i=0;$i<count($list);$i++) 	$list[$i] = strtolower(self::html($list[$i]));
			$list = array_filter($list);
			if (!is_array(Str::ifset($custom, 'safe')))   $new = $list; else { $new = array(); for($i=0;$i<count($list);$i++) if (in_array($list[$i], $custom['safe'])) $new[] = $list[$i]; }
			if (!is_array(Str::ifset($custom, 'remove'))) $new = $list; else { $new = array(); for($i=0;$i<count($list);$i++) if (!in_array($list[$i], $custom['remove'])) $new[] = $list[$i]; }
			if (Str::ifset($custom, 'max', -1) > -1 && count($list) > Str::ifset($custom, 'max')) $list = array_slice($list, 0, Str::ifset($custom, 'max'));
			if (Str::ifset($custom, 'length', -1) > -1) { $temp = ''; $new  = array(); for($i=0;$i<count($list);$i++) { $temp .= $list[$i]; if (strlen($temp) <= $custom['length']) $new[] = $list[$i]; } $list = $new; }
			return $list;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Copy
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function copy($key) {
			if (self::isNew()) return Copy::get($key);
			switch($key) {
				case 'GLOBAL_EDITOR_TWICE' : 				return 'Form submitted twice, please re-submit this page.';
				case 'GLOBAL_EDITOR_POSTKEY' : 				return 'Post Key was not specified';
				case 'GLOBAL_EDITOR_DELETED' : 				return 'This item was deleted';
				case 'GLOBAL_EDITOR_INSERTING' :    		return 'Inserting a New ';
				case 'GLOBAL_EDITOR_INSERTED' :    			return 'Successfully saved to database';
				case 'GLOBAL_EDITOR_EDITING' :      		return 'Editing ';
				case 'GLOBAL_EDITOR_DELETING' :     		return 'Deleting ';
				case 'GLOBAL_EDITOR_RECORD' :     			return 'record';
				case 'GLOBAL_VALIDATION_HEADER' : 			return 'Please correct the errors below';
				case 'GLOBAL_VALIDATION_PW_MATCH' : 		return 'Passwords entered do not match';
				case 'GLOBAL_VALIDATION_PW_SHORT' : 		return 'Passwords too short, please enter a password > 6 characters';
				case 'GLOBAL_VALIDATION_EXISTS_EMAIL' : 	return 'This email is already associated with an account';
				case 'GLOBAL_VALIDATION_EXISTS_USERNAME' : 	return 'This username is already taken';
			}
			return 'Copy not specified :: Please notify admin';
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| MakePW
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function password($password) {
			if (self::isNew()) return Encryption::password($password);
			return Login::password($password);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Querystring
		//|| Custom = (string : url [def. URI], array : remove [def. empty]
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function querystring($replace = array(), $custom = array()) {
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| New
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if (self::isNew()) return Internet::querystring($replace, $custom);
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Old
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$precur 			= Internet::domain('uri');
			$precur 			= (Str::ifset($custom, 'url', false) === false)		? $precur : $custom['url'];
			$split				= (Str::exists($precur, '?')) ? explode('?', $precur) : array();
			$precur 			= Str::ifset($split, 1, '');
			$remove 			= (Str::ifset($custom, 'remove', false) === false)	? array() : $custom['remove'];
			$params 		 	= explode('&',$precur);
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Lowercase Replace and Remove
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$temp	  		   	= array();
			foreach($replace as $key => $value) $temp[strtolower($key)] = $value;
			$replace = $temp;
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Lowercase Replace and Remove
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$temp  = array();
			foreach($remove as $key) $temp[] = strtolower($key);
			$remove  = $temp;
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Split all Params / LowerCase
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$temp  = array();
			for($i=0;$i<count($params);$i++) {
				$params[$i] 	= explode('=', $params[$i]);
				if (Str::ifset($params[$i], 1, false) !== false) $temp[strtolower($params[$i][0])] = urldecode($params[$i][1]);
			}
			$params  = $temp;
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Replace All
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			foreach($replace as $key => $value) $params[$key] = $value;
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Remove All
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			foreach($remove as $key) unset($params[$key]);
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Build Querystring
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			return('?' . http_build_query($params));
		}

	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
	//|| End Class
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

	}