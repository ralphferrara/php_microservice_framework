<?php
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Core3 :: Copyright Syhora 2016
//|| Core Autoload
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Core AutoLoad
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	function __autoload_core($class) {
		switch($class) {
			case 'Account' :
			case 'Ajax' :
			case 'AWS' :
			case 'Bitmap' :
			case 'Cache' :
			case 'CAPTCHA' :
			case 'Connection' :
			case 'Copy' :
			case 'Core' :
			case 'Cron' :
			case 'Encryption' :
			case 'Errors' :
			case 'Facebook' :
			case 'File' :
			case 'Geo' :
			case 'Globe' :
			case 'Handler' :
			case 'HTML' :
			case 'Image' :
			case 'Increment' :
			case 'Instance' :
			case 'Internet' :
			case 'Mail' :
			case 'Maps' :
			case 'Math' :
			case 'MemoryCache' :
			case 'Merchants' :
			case 'MicroService' :
			case 'Modal' :
			case 'Pagination' :
			case 'Payments' :
			case 'PHPMail' :
			case 'Recordset' :
			case 'SEO' :
			case 'Server' :
			case 'SPAM' :
			case 'Specials' :
			case 'SQLRead' :
			case 'SQLWrite' :
			case 'Str' :
			case 'Template' :
			case 'Tickets' :
			case 'Safe'  :
			case 'Staff'  :
			case 'Validation' :
			case 'Video' :
				require_once(CORE_ROOT . 'core/classes/'.strtolower($class).'.php');
				break;
			case 'DT' : require_once(CORE_ROOT . 'core/classes/datetime.php'); break;
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Editor
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			case 'Editor'				:
			case 'EditorView'			:
			case 'Field'				:
			case 'Fields'				:
			case 'ListSearch'			:
			case 'Multiple'				:
			case 'ListView'				: require_once(CORE_ROOT . 'core/classes/editor/'.strtolower($class).'.php');
			case 'EJ'					: require_once(CORE_ROOT . 'core/classes/editor/junction.php'); 	break;
			case 'EditorGenerator' 		: require_once(CORE_ROOT . 'core/classes/editor/generator.php'); 	break;
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| 3rd Party
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			case 'Mandrill'				: require_once(CORE_ROOT . 'core/classes/mail/mandrill.php'); 	break;
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Merchants
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			case 'Stripe'				: require_once(CORE_ROOT . 'core/classes/merchants/stripe.php'); 	break;
		}
	}

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Register
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	spl_autoload_register('__autoload_core');

