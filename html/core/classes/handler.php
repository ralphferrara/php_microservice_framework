<?php
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Core3 :: Copyright Syhora 2016
//|| Handler
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Class
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	class Handler {

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Process
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function process($type, $key, $value = null, $custom = array()) {
			global $global;
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Load Users
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			if ($type == 'user' && !isset($global->user['loaded'])) {
				$global->user['loaded'] = 'OK';
				Users::populate(Login::get());
			}
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Read
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			if ($value == null) {
				switch($type) {
					case request  	: return (isset($_REQUEST[$key])) 			? $_REQUEST[$key] 		 : false; break;
					case post  	 	: return (isset($_POST[$key])) 				? $_POST[$key] 			 : false; break;
					case get    	: return (isset($_GET[$key])) 				? $_GET[$key] 	 		 : false; break;
					case session   	: return (isset($_SESSION[$key])) 			? $_SESSION[$key] 		 : false; break;
					case server 	: return (isset($_SERVER[$key])) 			? $_SERVER[$key] 		 : false; break;
					case config 	: return (isset($global->config[$key]))  	? $global->config[$key]  : false; break;
					case common 	: return (isset($global->common[$key]))  	? $global->common[$key]  : false; break;
					case cookie 	: return (isset($_COOKIE[$key]))  			? $_COOKIE[$key]  		 : false; break;
					case copy 		: return ($global->copy[$key]); break;
					case user 		: return (isset($global->user[$key]))  		? $global->user[$key]  	 : false; break;
					case data 		: return (isset($global->data[$key]))  		? $global->data[$key]  	 : false; break;
				}
			}
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Write
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			if ($value != null) {
				switch($type) {
					case post    	: $_POST[$key] 				= $value; 		break;
					case request   	: $_REQUEST[$key] 			= $value; 		break;
					case get     	: $_GET[$key] 				= $value; 		break;
					case session 	: $_SESSION[$key] 			= $value; 		break;
					case server 	: $_SERVER[$key] 			= $value; 		break;
					case config 	: $global->config[$key] 	= $value; 		break;
					case common 	: $global->common[$key] 	= $value; 		break;
					case cookie  	: setcookie($key, $value, self::timeout($custom['timeout']), '/'); break;
					case copy		: $global->copy[$key] = $value; break;
					case user 	 	: $global->user[$key] 		= $value; 		break;
					case data 	 	: $global->data[$key] 		= $value; 		break;
				}
				return $value;
			}
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Timeout
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function timeout($timeout) {
			$default  = (60 * 60 * 24 * 30);
			$timeout  = ($timeout == '') 		? $default : $timeout;
			$timeout  = (is_numeric($timeout))  ? $timeout 	: $default;
			$timeout  = (is_numeric($timeout) && $timeout > -1 && $timeout < 31622400)  ? $timeout : $default;
			return time() + $timeout;
		}

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| End Class
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	}

