<?php
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Core3 :: Copyright Syhora 2016
//|| Date Time
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Class
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	class DT {

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Date
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function date($format = '', $timestamp = '', $timeago = true) {
			$timestamp = ($timestamp == '') ? time() : $timestamp;
			$timestamp = (strtotime($timestamp) === false) ? $timestamp : strtotime($timestamp);
			if ($timeago) {
				$between   = time() - $timestamp;
				if ($between > 5 && $between < 15778800 /* 6 months */) return DT::timeago(date('Y-m-d G:i:s', $timestamp));
			}
			switch($format) {
				case 'LDMDY' : $format = '%A, %B %d, %Y'; break;		// Monday, December 31, 2013
				case 'd'     : $format = '%d'; break;					// 31
				case 'M'     : $format = '%b'; break;					// Dec.
				case 'Y'     : $format = '%Y'; break;					// 2013
				case 'MY'    : $format = '%b %Y'; break;				// Dec 2013
				case 'LMDY'  : $format = '%B %d, %Y'; break;		    // December 31, 2013
				case 'HISA'  : $format = '%I:%l:%S %p'; break;		    // 2:01:01 pm
				case 'mdy'   : $format = '%m/%d/%y'; break;		    	// 12/31/2012
				default : $format = '%y/%m/%d'; 					    // 2013/12/31
			}
			return strftime($format, $timestamp);
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Age
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function age($date) {
			$a = floor( (strtotime(date('Y-m-d')) - strtotime($date)) / 31557600);
			return ($a < 18 || $a > 120) ? 'N/A' : $a;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Time Ago
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function timeago($from, $to=''){
			if ($from == '0000-00-00 00:00:00' || $from == '0000-00-00') return 'N/A';
			if (strtotime($from) < strtotime('2000-01-01')) return $from;
			$to  = ($to == '') ? date('Y-m-d G:i:s') : $to;
			$x = 0;
			$x = (strtotime($to) - strtotime($from));
			$measurement = '';
			if ($x > 157680000) return(self::date('MY', $from, false)); else {
				if ($x >= 31557600) 					$measurement = 'y';
				if ($x >= 2592000 && $x < 31557600) 	$measurement = 'n';
				if ($x >= 604800   && $x < 2592000) 	$measurement = 'w';
				if ($x >= 86400    && $x < 604800) 	$measurement = 'd';
				if ($x >= 3600     && $x < 86400) 	$measurement = 'h';
				if ($x >= 60       && $x < 3600)  	$measurement = 'm';
				if ($x >= 0      && $x < 60)  		$measurement = 's';
				$w = '';
				switch($measurement){
					case "y" : $w = floor($x / (60  * 60 * 24 * 365)); break;
					case "n" : $w = floor($x / (60  * 60 * 24 * 30)); break;
					case "w" : $w = floor($x / (60  * 60 * 24 * 7)); break;
					case "d" : $w = floor($x / (60  * 60 * 24)); break;
					case "h" : $w = floor($x / (60  * 60)); break;
					case "m" : $w = floor($x / 60); break;
					case "s" : $w = floor($x); break;
				}
				$y = '';
				switch($measurement){
					case "y" : $y = 'GLOBAL_TIME_YEAR'; break;
					case "n" : $y = 'GLOBAL_TIME_MONTH'; break;
					case "w" : $y = 'GLOBAL_TIME_WEEK'; break;
					case "d" : $y = 'GLOBAL_TIME_DAY'; break;
					case "h" : $y = 'GLOBAL_TIME_HOUR'; break;
					case "m" : $y = 'GLOBAL_TIME_MINUTE'; break;
					case "s" : $y = 'GLOBAL_TIME_SECOND'; break;
				}
				$word = ($w > 1) ? $y . 'S' : $y;
				$z  = $w . ' ' . 			Copy::get($word);
				$z .= ' ' . 				Copy::get('GLOBAL_TIME_AGO');
				return(($x <= 0) ? 			Copy::get('GLOBAL_TIME_NOW') : $z );
			}
		}


		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| End Class
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	}

