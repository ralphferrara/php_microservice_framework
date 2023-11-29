<?php
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Syhora :: Core3
//|| EditorGenerator Class
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
//|| Class
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

	class EditorGenerator {

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Column Data
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function column($data) {
			$resp 			   = array();
			$resp['length']    = (Str::exists($data['Type'], '(')) ? Str::fb($data['Type'], '(', ')') : -1;
			$resp['type']      = strtolower(str_replace('('.$resp['length'].')', '', $data['Type']));
			$resp['default']   = $data['Default'];
			$resp['key']   	   = $data['Key'];
			return $resp;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Generate
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function generate($table) {
			$columns = array();
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Database Query
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$data = new Recordset('SHOW COLUMNS FROM '.$table);
			while($data->getnext()) {
				$columns[$data->field('Field')] = self::column($data->toarray());
			}
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Generate Editor
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$rows = array();
			$sql  = array();
			$tab  = '	';
			$primary = '';
			foreach($columns as $key => $data) {
				if ($data['key'] == 'PRI') $primary = $key;
				$sql [] = $tab . '$sql->field("'.$key.'");';
				$rows[] = $tab . '$editor->field("'.self::title($key).'","'.$key.'")->view("'.self::view($key, $data, false).'"'.self::view($key, $data, true).')'.self::edit($key, $data).self::sort($key, $data).';';
			}
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Template
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$template 			= new Template;
			$template->main		= 'editor/generator.html';
			$template->add('PRIMARY', 			$primary);
			$template->add('TABLE', 			$table);
			$template->add('EDITORFIELDS', 		implode(chr(13), $rows));
			$template->add('SQLFIELDS', 		implode(chr(13), $sql));
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//||
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			die('<textarea rows="200" cols="1000">' . $template->compile(false, false) . '</textarea>');
		}


		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Title
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function title($title) {
			if (substr($title, 0, 3) == 'id_') return 'ID#';
			if (substr($title, 0, 4) == 'fid_') return ucwords(substr($title, 4));
			$word = substr($title, strpos($title, '_') + 1);
			return ucwords(str_replace('_', ' ', $word));
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| View
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function view($key, $data, $custom = false) {
			if (!$custom) switch($data['type']) {
				case 'varchar' : return 'plain';
				case 'int'     : return 'integer';
				case 'float'   : return 'float';
				case 'timestamp' :
				case 'datetime' : return 'datetime';
				case 'date'    : return 'date';
				case 'time' : return 'time';
				case 'tinyint' : return 'yesno';
				default : return 'plain';
			}
			if ($custom) switch($data['type']) {
				case 'timestamp' :
				case 'datetime' : 		return ', array("format" => "m/d/Y g:i:s a")';
				case 'date'    : 		return ', array("format" => "m/d/Y")';
				case 'time' : 			return ', array("format" => "g:i:s a")';
				default 	: 			return '';
			}
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Edit
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function edit($key, $data) {
			$word = str_replace('fid_', '', $key);
			$id   = str_replace('fid_', 'id_', $key);
			if (substr($key, 0, 3) == 'id_') return '';
			if (Str::exists($key, '_password')) return '';
			if (substr($key, 0, 4) == 'fid_') return '->edit("select", true, array("type" => "db", "query" => "select id_'.$word.', '.$word.'_name from {{PRE}}'.$word.'s", "fkey" => "'.$id.'", "fvalue" => "'.$word.'_name"))';
			switch($data['type']) {
				case 'varchar' : return '->edit("edit")';
				case 'int'     : return '->edit("integer")';
				case 'float'   : return '->edit("float")';
				case 'timestamp' :
				case 'datetime' : return '->edit("datetime")';
				case 'date'    : return '->edit("date")';
				case 'time' : return '->edit("time")';
				case 'tinyint' : return '->edit("mode")';
				default : return '->edit("edit")';
			}
		}



		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Sort
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function sort($key, $data) {
			if (substr($key, 0, 3) == 'id_') return '';
			switch($data['type']) {
				case 'tinyint' : return '';
				default 	   : return '->sort()';
			}
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| End Class
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

	}