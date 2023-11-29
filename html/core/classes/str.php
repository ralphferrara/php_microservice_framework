<?php
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Core3 :: Copyright Syhora 2016
//|| String
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Class
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	class Str {

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Random String Generator
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function random($length = 10){
			$charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
			$random  = '';
			for($i=0;$i<$length;$i++) $random .= substr($charset, mt_rand(0, strlen($charset)), 1);
			return $random;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Minify
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function minify($method, $data) {
			switch($method) {
				case 'html' :
					//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
					//|| Hack to keep from minifying Textarea Content.
					//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
					$pull = Str::fab($data, '<textarea', '</textarea>');
					if (count($pull) > 0) for($i=0;$i<count($pull);$i++) $data = str_replace('<textarea'.$pull[$i].'</textarea>', '[TEXTAREA]['.$i.']', $data);
					//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
					//|| Do the Actual Minification
					//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
					$data = preg_replace(array('/ {2,}/','/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'),array(' ',''),$data);
					//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
					//|| Bring the Text Area's back
					//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
					if (count($pull) > 0) for($i=0;$i<count($pull);$i++) $data = str_replace('[TEXTAREA]['.$i.']', '<textarea'.$pull[$i].'</textarea>', $data);
					return $data;
				  break;
			}
			return($data);
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| IFSet :: Returns Value if set
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function ifset($array, $field, $response = '') {
			if ($array == '') return($field != '') ? $field : $response;
			return(isset($array[$field]) && $array[$field] != '') ? $array[$field] : $response;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Fetch Between
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function fb($haystack, $needle1, $needle2) {
			$a			= strlen($needle1);
			$x			= stripos($haystack,$needle1);
			if (!($x === false)) {
				$y		= stripos($haystack, $needle2, $x + $a);
				if (!($y === false)) return substr($haystack, $x + $a,($y - $a) - $x);
			}
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Fetch All Between
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function fab($haystack, $needle1, $needle2) {
			$matches 	= array();
			$delimiter 	= '#';
			$regex 		= $delimiter . preg_quote($needle1, $delimiter) . '(.*?)' . preg_quote($needle2, $delimiter) . $delimiter . 's';
			preg_match_all($regex, $haystack, $matches);
			return(isset($matches[1]) ? $matches[1] : array());
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Exists
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function exists($haystack, $needle) {
			if($needle == '') return false;
			return(strpos(' ' . $haystack, $needle) > 0);
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| CSV To Array
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function csv_array($data, $field = '#', $line = null) {
			$line 	= ($line == null) ? chr(10) : $line;
			$data 	= str_replace(chr(13), chr(10), $data);
			$data 	= str_replace(chr(10).chr(10), chr(10), $data);
			$lines	= explode($line, $data);
			$ary	= array();
			if (is_array($lines)) foreach($lines as $item) {
				$tmp = explode($field, $item);
				$ary[] = $tmp;
			}
			return($ary);
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Truncate
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function truncate($html, $length) {
			$html = utf8_encode($html);
			$html = html_entity_decode($html, ENT_COMPAT, 'UTF-8');
			$html = str_replace("`", "'", $html);
			$html = str_replace(array(chr(10),chr(13)), array('',''), $html);
			$html = strip_tags($html);
			$html = trim($html);
			return htmlentities(substr($html, 0, $length));
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Word Truncate
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function words($text, $length, $ellipses = true) {
			$text = str_replace(array(chr(10),chr(13)), array('',''), $text);
			$text = str_replace('`', "'", $text);
			$text = strip_tags($text);
			$text = trim($text);
			if (strlen($text) < $length) return $text;
			for($i=0;$i<$length;$i++) if (ctype_space(substr($text, $length-$i, 1))) break;
			$e = ($ellipses) ? '...' : '';
			return substr($text, 0, ($length - $i)) . $e;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Clean a URL
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function url($data) {
			$data = urldecode($data);
			$data = html_entity_decode($data);
			$data = str_replace('  ', ' ', $data);
			$data = str_replace(' ', '-', $data);
			$data = preg_replace('/[^a-zA-Z0-9\-\_]/s', '', $data);
			$data = str_replace('---', '--', $data);
			$data = str_replace('--', '-', $data);
			$data = strtolower($data);
			return($data);
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Clean a URL
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function safetag($data) {
			$data = str_replace('"', '', $data);
			$data = str_replace('  ', ' ', $data);
			$data = str_replace('---', '--', $data);
			$data = str_replace('--', '-', $data);
			$data = htmlentities($data);
			return($data);
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Pretties up big numbers
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function number($integer, $short = false) {
			$integer = floor($integer);
			$spots   = ($short) ? 0 : 1;
			if ($integer > 100000000) return number_format($integer / 1000000, 0) . 'm';
			if ($integer > 10000000) return number_format($integer / 1000000, $spots) . 'm';
			if ($integer > 1000000) return number_format($integer / 1000000, $spots) . 'm';
			if ($integer > 10000) return number_format($integer / 1000, 0) . 'k';
			if ($integer > 1000) return number_format(($integer / 1000), $spots) . 'k';
			return($integer);
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Pretties up big filesizes
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function filesize($integer, $short = false) {
			$integer = floor($integer);
			$spots   = ($short) ? 0 : 2;
			if ($integer > 1099511627776) return number_format($integer / 1099511627776, $short) . 'TB';
			if ($integer > 1073741824) return number_format($integer / 1073741824, $short) . 'GB';
			if ($integer > 1048576) return number_format($integer / 1048576, $short) . 'MB';
			if ($integer > 1024) return number_format($integer / 1024, 0) . 'KB';
			return number_format(($integer / 1000), 0) . 'bytes';
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Convert from Tags
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function fromTags($tags, $custom = array()) {
			if (trim($tags) == '') return '';
			$list = explode(',',$tags);
			if (!is_array($list) || count($list) == 0) return ','.strtolower(self::fromHTML($tags)).',';
			for($i=0;$i<count($list);$i++) 	$list[$i] = strtolower(Str::fromHTML($list[$i]));
			$list = array_filter($list);
			if (!is_array(Str::ifset($custom, 'safe')))   $new = $list; else { $new = array(); for($i=0;$i<count($list);$i++) if (in_array($list[$i], $custom['safe'])) $new[] = $list[$i]; }
			if (!is_array(Str::ifset($custom, 'remove'))) $new = $list; else { $new = array(); for($i=0;$i<count($list);$i++) if (!in_array($list[$i], $custom['remove'])) $new[] = $list[$i]; }
			if (Str::ifset($custom, 'max', -1) > -1 && count($list) > Str::ifset($custom, 'max')) $list = array_slice($list, 0, Str::ifset($custom, 'max'));
			if (Str::ifset($custom, 'length', -1) > -1) { $temp = ''; $new  = array(); for($i=0;$i<count($list);$i++) { $temp .= $list[$i]; if (strlen($temp) <= $custom['length']) $new[] = $list[$i]; } $list = $new; }
			return ','.implode(',', $list).',';
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Convert to Tags
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function toTags($tags, $custom = array()) {
			if (trim($tags) == '') return '';
			$list = explode(',',$tags);
			if (!is_array($list) || count($list) == 0) return ','.strtolower(self::toHTML($tags)).',';
			for($i=0;$i<count($list);$i++) 	$list[$i] = strtolower(Str::toHTML($list[$i]));
			$list = array_filter($list);
			if (!is_array(Str::ifset($custom, 'safe')))   $new = $list; else { $new = array(); for($i=0;$i<count($list);$i++) if (in_array($list[$i], $custom['safe'])) $new[] = $list[$i]; }
			if (!is_array(Str::ifset($custom, 'remove'))) $new = $list; else { $new = array(); for($i=0;$i<count($list);$i++) if (!in_array($list[$i], $custom['remove'])) $new[] = $list[$i]; }
			if (Str::ifset($custom, 'max', -1) > -1 && count($list) > Str::ifset($custom, 'max')) $list = array_slice($list, 0, Str::ifset($custom, 'max'));
			if (Str::ifset($custom, 'length', -1) > -1) { $temp = ''; $new  = array(); for($i=0;$i<count($list);$i++) { $temp .= $list[$i]; if (strlen($temp) <= $custom['length']) $new[] = $list[$i]; } $list = $new; }
			return $list;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Convert to HTML
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function acszc($address = '', $city = '', $state = '', $zip = '', $country = '', $custom = array()) {
			$csz  = '';
			$csz .= $address;
			$csz .= (($address != '') && ($city != '' || $state != '' || $zip != '')) ? ' ' : '';
			$csz .= ($city != '') ? $city : '';
			$csz .= ($city != '' && ($state != '' || $zip != '')) ? ', ' : '';
			$csz .= $state;
			$csz .= ($state != '' && $zip != '') ? ' ' : '';
			$csz .= $zip;
			$csz .= ($country != '') ? ' ' . GeoData::country($country, 'short') : '';
			return $csz;
		}

			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| End Class
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	}
