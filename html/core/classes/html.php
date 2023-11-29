<?php
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Core3 :: Copyright Syhora 2016
//|| HTML
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Class
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	class HTML {

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Safe Tags
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function safetags() {
			return explode('|', 'br|broken|smiley|quote|strike|b|i|p|strong|em|u|ul|li|ol|img|url|link|h1|h2|h3|h4|h5|h6');
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| For Use when not saving any tags/BB Code
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function toDB($text) {
			$text = self::cleanCode($text, false, false);
			$text = strip_tags($text);
			return $text;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| For Use when not saving any tags/BB Code
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function HTMLtoBB($text) {
			$text = self::cleanCode($text, true, true);
			return $text;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Exports the Raw BBCode / Data for Editing in Textarea
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function toEdit($text) {
			$text = self::cleanCode($text, false, false);
			return $text;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| To Line - For displaying non-html Display Results Word Truncates at Maxlength
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function toPlain($text, $maxlength = null, $tidy = true) {
			if ($text == '') return '';
			$text 					= str_replace("\\r", chr(13), $text);
			$text 					= str_replace("\n", chr(10), $text);
			$text 					= str_replace("\\n", chr(10), $text);
			$text 					= str_replace("\n", chr(10), $text);
			$text 					= html_entity_decode($text, ENT_QUOTES, "UTF-8");
			$text 					= self::toHTML($text, false, false, $tidy);
			$text  					= strip_tags($text);
			$text  					= str_replace('`', "'", $text);
			$text  					= str_replace("'", "&#39;", $text);
			if ($maxlength !== null && is_numeric($maxlength)) $text = Str::words($text, $maxlength, true);
			return $text;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| To Line - For displaying non-html Display Results Word Truncates at Maxlength
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function toSearch($text, $search, $maxlength = null) {
			if ($text == '') return '';
			$text = self::toPlain($text);
			$text = str_ireplace($search, '<strong class="search">'.$search.'</strong>', $text);
			$text = '<span class="occurs">'.substr_count($text, $search).' occurrences found</span>' . $text;
			if ($maxlength !== null && is_numeric($maxlength)) $text = (strlen($text) > $maxlength) ? substr($text, 0, $maxlength) . '...' : $text;
			return $text;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| To HTML
		//|| Safe BBCode b,i,u,url,img,br,quote, Strip XSS
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function toHTML($text, $nl2br = true, $broken = true, $tidy = true) {
			if ($text == '') return '';
			$text 					= self::cleanCode($text, $nl2br, $broken);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Fix Old Broken Links/Images
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$replace 				= array();
			$replace['[br]']        = '<br>';
			$replace['[broken]']    = '<img class="linkImage broken" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=">';
			$replace['[smiley]']    = '<span></span><em class="smi ';
			$replace['[/smiley]']   = '">||HACKTIDY||</em>';
			$replace['[quote]']     = '<quote>';
			$replace['[/quote]']    = '</quote>';
			$replace['[strike]']    = '<quote>';
			$replace['[/strike]']   = '</quote>';
			$replace['[/b]']        = '</strong>';
			$replace['[p]']         = '<p>';
			$replace['[/p]']        = '</p>';
			$replace['[strong]']    = '<strong>';
			$replace['[/strong]']   = '</strong>';
			$replace['[b]']         = '<strong>';
			$replace['[/b]']        = '</strong>';
			$replace['[i]']         = '<em>';
			$replace['[/i]']        = '</em>';
			$replace['[em]']        = '<em>';
			$replace['[/em]']       = '</em>';
			$replace['[u]']         = '<u>';
			$replace['[/u]']        = '</u>';
			$replace['[ul]']         = '<ul>';
			$replace['[/ul]']        = '</ul>';
			$replace['[ol]']         = '<ol>';
			$replace['[/ol]']        = '</ol>';
			$replace['[li]']         = '<li>';
			$replace['[/li]']        = '</li>';
			$replace['[img]']       = '<img class="linkImage" src="';
			$replace['[/img]']      = '">';
			$replace['[url][link]'] = '<a target="_blank" href="';
			$replace['[link]'] 		= 'href="';
			$replace['[/link]']     = '">';
			$replace['[url]'] 		= '<a target="_blank" ';
			$replace['[/url]']      = '</a>';
			for($i=1;$i<=6;$i++) {
				$replace['[h'.$i.']'] 	= '<h'.$i.'>';
				$replace['[/h'.$i.']'] 	= '</h'.$i.'>';
			}
			foreach($replace as $key => $value) $text = str_replace($key,$value,$text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Replace Multiple <br>
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$text = str_replace(array("<br>\n", "<br>\r"), "<br>", $text);
			$text = str_replace('<br><br><br>', "<br><br>", $text);
			while(substr($text, 0, 4) == '<br>') $text = substr($text, 4);
			while(substr($text, -4, 4) == '<br>') $text = substr($text, 0, strlen($text)-4);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Replace Brackets with Ascii Equiv
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$text = str_replace(array('[',']','{','}'), array('&#91;', '&#93;', '&#123;', '&#125;'), $text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Strip any HTML that's within a Quote.
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$quotes = Str::fab($text, '<quote>', '</quote>');
			if (count($quotes) > 0) for($i=0;$i<count($quotes);$i++) $text = str_replace('<quote>' . $quotes[$i] . '</quote>', '<quote>' . strip_tags($quotes[$i]) . '</quote>', $text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Validate HTML / Remove Bad Close Tags
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			if (!$tidy) return $text;
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Validate HTML / Remove Bad Close Tags
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$config = array(
				'break-before-br' => false,
				'char-encoding' => 'utf8',
				'output-encoding' => 'utf8',
				'output-html' => true,
				'numeric-entities' => false,
				'fix-uri' => true,
				'tab-size' => 2,
				'wrap-attributes' => true,
				'wrap' => 0,
				'indent-attributes' => false,
				'fix-bad-comments' => true,
				'fix-backslash' => true,
				'replace-color' => false,
				'wrap-asp' => false,
				'wrap-jste' => false,
				'wrap-php' => false,
				'wrap-sections' => false,
				'drop-proprietary-attributes' => false,
				'new-blocklevel-tags'   => 'quote,em',
				'new-empty-tags'   => 'span,em',
				'new-inline-tags'   => 'span,em',
				'hide-comments' => false,
				'hide-endtags' => false,
				'drop-empty-paras' => true,
				'quote-ampersand' => true,
				'quote-marks' => true,
				'quote-nbsp' => true,
				'vertical-space' => true,
				'wrap-script-literals' => false,
				'show-warnings' => false,
				'repeated-attributes' => 'keep-last'
			);
			$tidy = new Tidy();
			$tidy->parseString('<html><body>' . $text . '</body></html>', $config);
			$tidy->cleanRepair();
			$text = trim(Str::fb($tidy, '<body>', '</body>'));
			$text = str_replace('||HACKTIDY||', '', $text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Remove Stupid BR's in the beginning of post
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$text = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Replace!
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			return $text;
		}


		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| CLEAN THE OLD CODE UP
		//|| Safe BBCode b,i,u,url,img,br,quote, Strip XSS
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||


		private static function cleanCode($text, $nl2br = true, $broken = true) {
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Decode to illegal text
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$text 					= html_entity_decode($text, ENT_QUOTES, "UTF-8");
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Allow Certain HTML
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$replace 				= self::safetags();
			foreach($replace as $tag) $text = str_replace(array('<'.$tag.'>','</'.$tag.'>'),array('['.$tag.']','[/'.$tag.']') ,$text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Change Links
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$links 					= Str::fab($text, '<a ', '>');
			for($i=0;$i<count($links); $i++) {
				$href = (Str::exists($links[$i],'href="')) ? Str::fb($links[$i], 'href="', '"') : '';
				if ($href == '' && Str::exists($links[$i], 'href=')) $href = str_replace('"', '', Str::fb($links[$i], 'href=', ']'));
				if (self::isURL($href)) $text = str_replace('<a ' . $links[$i] . '>', '[url][link]'.self::passURL($href).'[/link]', $text); else $text = str_replace('[a ' . $links[$i] . ']', '', $text);
			}
			$text = str_replace('</a>', '[/url]', $text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Strip any other tags
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$text 					= strip_tags($text);
			$text 					= str_replace('>',']',$text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Change Line Breaks to [br]
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$text 					= stripslashes($text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Strip non BBCode Tags / Encode
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$text					= preg_replace('/[^\x0A\x20-\x7E]/','',$text);
			$text 					= utf8_encode($text);
			$text					= preg_replace('/[^\x0A\x20-\x7E]/','',$text);
			$text 					= str_replace('`', "'", $text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Ditch Empty Tags
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$text 					= str_replace('[img][/img]', "", $text);
			$text 					= str_replace('[url][/url]', "", $text);
			$text 					= str_replace('[url][link][/link][/url]', "", $text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Fix Articles
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$links 					= Str::fab($text, '[article', ']');
			for($i=0;$i<count($links); $i++) $text = str_replace('[article' . $links[$i] . ']', '[quote]', $text);
			$replace['[article]']   = '[quote]';
			$replace['[/article]']   = '[/quote]';
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Random Tags
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$text = preg_replace("'\[color[\/\!]*?[^<>]*?\]'si", "", $text);
			$text = preg_replace("'\[span[\/\!]*?[^<>]*?\>'si", "", $text);
			$text = preg_replace("'\[div[\/\!]*?[^<>]*?\>'si", "", $text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Fix Old Broken Links
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$links 					= Str::fab($text, '[a ', ']');
			for($i=0;$i<count($links); $i++) {
				$href = (Str::exists($links[$i],'href="')) ? Str::fb($links[$i], 'href="', '"') : '';
				if ($href == '' && Str::exists($links[$i], 'href=')) $href = str_replace('"', '', Str::fb($links[$i], 'href=', ']'));
				if (self::isURL($href)) $text = str_replace('[a ' . $links[$i] . ']', '[url][link]'.self::passURL($href).'[/link]', $text); else $text = str_replace('[a ' . $links[$i] . ']', '', $text);
			}
			$text = str_replace('[/a]', '[/url]', $text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Fix New Links
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$links 					= Str::fab($text, '[url=', ']');
			for($i=0;$i<count($links); $i++) {
				if (self::isURL($links[$i])) $text = str_replace('[url=' . $links[$i] . ']', '[url][link]'.self::passURL($links[$i]).'[/link]', $text); else $text = str_replace('[url=' . $links[$i] . ']', '', $text);
			}
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Images Step 1 . [img]src[/img]
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$imgs 					= Str::fab($text, '[img]', '[/img]');
			for($i=0;$i<count($imgs); $i++) $text = str_replace('[img]' . $imgs[$i] . '[/img]', self::makeImage($imgs[$i], $broken), $text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Images
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$imgs 					= Str::fab($text, '[img ', ']');
			for($i=0;$i<count($imgs); $i++) {
				$src = (Str::exists($imgs[$i],'src="')) ? Str::fb($imgs[$i], 'src="', '"') : '';
				if ($src == '' && Str::exists($imgs[$i], 'src=')) $src = str_replace('"', '', Str::fb($links[$i], 'src=', ' '));
				$text = str_replace('[img ' . $imgs[$i] . ']', self::makeImage($src, $broken), $text);
			}
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Convert NL2BR
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			if ($nl2br) {
				$text 					= nl2br($text);
				$text 					= str_replace('<br>',   '[br]', $text);
				$text 					= str_replace('<br />', '[br]', $text);
				$text 					= str_replace('[br][br]', '[br]', $text);
			}
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Strip Any Other Tags
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$text 					= strip_tags($text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Remove
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$replace 				= array();
			$replace['[/div]']      = '';
			$replace['[/span]']     = '';
			$replace['[/color]']    = '';
			foreach($replace as $key => $value) $text = str_replace($key,$value,$text);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Remove
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			return $text;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Safe Domain List
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		private static function safeDomain($url, $strip = false) {
			$list 		= array();
			$list['truenudist.com']  = 'truenudist.com';
			$list['truenudists.com']  = 'truenudists.com';
			$list['truenudists.biz']  = 'truenudists.com';
			$list['truenudists.me']   = 'truenudists.com';
			$list['trueswingers.biz'] = 'trueswingers.biz';
			$list['trueswingers.com'] = 'trueswingers.com';
			$list['nude-yoga.org']    = 'nude-yoga.com';
			$list['nude-yoga.com']    = 'nude-yoga.com';
			$temp = $url;
			$temp = str_replace('http://www.', '',   $temp);
			$temp = str_replace('https://www.', '', $temp);
			$temp = str_replace('http://', '',   $temp);
			$temp = str_replace('https://', '', $temp);
			$temp = str_replace('../', '/', $temp);
			$temp = str_replace('//', '/', $temp);
			foreach($list as $check => $correct) {
				if (substr($temp, 0, strlen($check)) == $check) {
					if ($strip == true) return str_replace($check, '', $temp);
					$url = str_replace('http://' . $check, 'https://' . $correct, $url);
					$url = str_replace('https://' . $check, 'https://' . $correct, $url);
					$url = str_replace('http://www.' . $check, 'https://www.' . $correct, $url);
					$url = str_replace('https://www.' . $check, 'https://www.' . $correct, $url);
					return $url;
				}
			}
			return false;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Convert to a URL
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		private static function convertURL($url) {
			$url = str_replace('http://', '[http%%%%%%]',   $url);
			$url = str_replace('https://', '[https%%%%%%]', $url);
			$url = str_replace('../', '/', $url);
			$url = str_replace('//', '/', $url);
			$url = str_replace('[http%%%%%%]',  'http://',  $url);
			$url = str_replace('[https%%%%%%]', 'https://', $url);
			if (substr($url, 0, 4) != 'http') {
				$url  = (substr($url, 0, 1) != '/') ? Internet::domain('domain') . '/' . $url : Internet::domain('domain') . $url;
				$safe = self::safeDomain($url);
				if ($safe !== false) $url = $safe;
			}
			return $url;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Validate URL
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		private static function isURL($url) {
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Blank?
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			if (trim($url) == '') return false;
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| No Self Links to Members Login Etc/
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$ok  = true;
			$url = self::convertURL($url);
			if (filter_var($url, FILTER_VALIDATE_URL) === false) $ok = false;
			if (substr($url,0,7) == 'file://') $ok = false;
			if (Str::exists($url, 'javascript:')) $ok = false;
			if (Str::exists($url, '$')) $ok = false;
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| No Self Links to Members Login Etc/
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$list 		= array();
			$list[]		= '/members';
			$list[]		= '/login';
			$list[]		= '/logout';
			$temp		= self::safeDomain($url, true);
			if ($temp !== false) foreach($list as $check) if (substr($temp, 0, strlen($check)) == $check) $ok = false;
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| No Self Links to Members Login Etc/
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			return($ok);
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Validate URL
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function passURL($url) {
			$url 	= self::convertURL($url);
			$safe   = self::safeDomain($url);
			if ($safe !== false) return $url;
			$url    = base64_encode(substr($url, 0, 1024));
			return('/link/' . substr(Encryption::hash('link' . $url),0,16) . '/' . urlencode($url));
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Validate URL
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		private static function smiley($url) {
			if (Str::exists($url, 'http://') || Str::exists($url, 'https://')) {
				$safe = self::safeDomain($url, true);
				if (!$safe) return false;
			}
			$url  = str_replace('images/emoticons', '', $url);
			$url  = str_replace('.gif', '', $url);
			$url  = str_replace('/', '', $url);
			switch($url) {
				case 'smiley-cool' 		  : 	$smiley = 'smi-cool2'; break;
				case 'smiley-cry' : 			$smiley = 'smi-cry'; break;
				case 'smiley-embarassed' 	: 	$smiley = 'smi-embarrassed'; break;
				case 'smiley-embarrassed' : 	$smiley = 'smi-embarrassed'; break;
				case 'smiley-foot-in-mouth' : 	return ''; break;
				case 'smiley-frown' : 			$smiley = 'smi-sad'; break;
				case 'smiley-innocent' : 		$smiley = 'smi-smile3'; break;
				case 'smiley-kiss' : 			$smiley = 'kiss'; break;
				case 'smiley-laughing' : 		$smiley = 'smi-ecstatic'; break;
				case 'smiley-money-mouth' : 	$smiley = 'money'; break;
				case 'smiley-sealed' : 			return ''; break;
				case 'smiley-smile' : 			$smiley = 'smi-smile'; break;
				case 'smiley-surprised' : 		$smiley = 'smi-shocked'; break;
				case 'smiley-tongue-out' : 		$smiley = 'smi-tongue'; break;
				case 'smiley-undecided' : 		$smiley = 'smi-confused'; break;
				case 'smiley-wink' : 			$smiley = 'smi-wink'; break;
				case 'smiley-yell' : 			$smiley = 'smi-frustrated'; break;
				default : return false;
			}
			return('[smiley]'.$smiley.'[/smiley]');
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Make an Image
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		private static function makeImage($src, $broken) {
			$smiley = self::smiley($src);
			if ($smiley !== false) return $smiley;
			$src	= str_replace('../', '/', $src);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Check for Totally Busted Links
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			if ($src == '') return '';
			$list		 = array();
			$list[]      = 'null';
			$list[]      = 'file://';
			$list[]      = 'file:\\\\';
			foreach($list as $url) if (Str::exists($src, $url)) return '';
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Check for Old Bad Images
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			if ($broken) {
				$list		 = array();
				foreach($list as $url) if (Str::exists($src, $url)) return '[broken]';
			}
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Check for Old Bad Images
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			if (!Str::exists($src, 'http://') && !Str::exists($src, 'https://')) {
				$src = Internet::domain('domain') . $src;
				$src = self::safeDomain($src);
			}
			if (!self::isURL($src)) return '';
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Check for Old Bad Images
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			return '[img]'.$src . '[/img]';
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| End Class
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	}

