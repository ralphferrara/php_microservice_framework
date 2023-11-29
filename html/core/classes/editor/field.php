<?php
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Syhora :: Core3
//|| Field Class
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
//|| Class
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

	class Field {

		var $title;
		var $sqlfield;
		var $key;
		var $view;
		var $search;
		var $edit;
		var $delete;
		var $sort;
		var $source;
		var $validate;
		var $error;

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Construct
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function __construct($title, $sqlfield) {
			$this->title 	= $title;
			$this->sqlfield = $sqlfield;
			$this->sort 	= false;
			$this->search	= false;
			$this->edit		= false;
			$this->delete	= true;
			$this->view		= false;
			$this->source 	= false;
			$this->validate	= false;
			$this->error 	= false;
			$this->auto 	= false;
			$this->upload 	= false;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| View - ListView
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function view($type = 'plain', $custom = array()) {
			$this->view 	= array('view' => true, 'type' => $type, 'order' => Str::ifset($custom, 'order', -1), 'custom' => $custom);
			return $this;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Search
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function search($type = 'edit', $integer = false, $custom = array()) {
			$equator = Str::ifset($custom, 'equator', 'like');
			if (!in_array($equator, array('>','>=','<','<=','<>','=','like'))) die('Invalid Equator');
			$this->search 	= array('search' => true, 'integer' => $integer, 'type' => $type, 'equator' => $equator, 'order' => Str::ifset($custom, 'order', -1), 'custom' => $custom);
			return $this;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Edit
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function edit($type = 'edit', $integer = false, $custom = array()) {
			if ($integer !== false && $integer !== true) Editor::error('Field :: Edit :: Integer Value is not a boolean ('.$this->sqlfield.')');
			$this->edit 	= array('edit' => true, 'type' => $type, 'group' => Str::ifset($custom, 'group', 'default'), 'integer' => $integer, 'order' => Str::ifset($custom, 'order', -1), 'custom' => $custom);
			return $this;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Sort
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function sort($custom = array()) {
			$this->sort 	= array('sort' => true, 'custom' => $custom);
			return $this;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Data Source
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function source($type = 'array', $custom = array()) {
			$this->source 	= array('source' => true, 'type' => $type, 'data' => Str::ifset($custom, 'data', array()), 'custom' => $custom);
			return $this;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Validate
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function validate($type = 'blank', $custom = array()) {
			$type = ($type == '') ? 'blank' : $type;
			$this->validate 	= array('validate' => true, 'type' => $type, 'message' => Str::ifset($custom, 'message', EJ::message($type)), 'custom' => $custom);
			return $this;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Error
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function error($message, $custom = array()) {
			$this->error 	= array('error' => true, 'message' => $message, 'custom' => $custom);
			return $this;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| AutoFill
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function auto($type = 'array', $integer = false, $custom = array()) {
			$custom['data-min'] = Str::ifset($custom, 'min', 4);
			if (isset($custom['min'])) unset($custom['min']);
			$this->auto 	= array('auto' => true, 'type' => $type, 'integer' => $integer, 'custom' => $custom, 'data' => Str::ifset($custom, 'data', array()));
			return $this;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Validate Upload/Autofill
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function checkup() {
			if ($this->edit['type'] != 'upload' && $this->edit['type'] != 'auto') return true;
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Validate Upload
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if ($this->edit['type'] == 'upload') {
				if (!$this->has('upload')) Editor::error($this->field->title . ' does not have additional upload data $field->upload()');
				switch($this->upload['save']) {
					case 'table' :
						if (!isset($this->upload['custom']['table'])) Editor::error($this->title . ' does not have a table specified for table insert');
						if (!isset($this->upload['custom']['fields']) || !is_array($this->upload['custom']['fields'])) Editor::error($this->field->title . ' does not have a fields specified for table insert');
						break;
					case 'field' : break;
					default 	 : Editor::error($this->field->title . ' Has an Invalid Save Type ('.$this->upload['save'].')');
				}
				if ($this->upload['filename'] == '[MEDIA]' && !isset($this->upload['custom']['area'])) Editor::error($this->title . ' requires an area for [MEDIA] filename');
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Validate AutoFill
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			return true;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Upload
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function upload($save = 'field', $type = 'image', $filename = '[DEFAULT]', $custom = array()) {
			$custom['watermark'] = Str::ifset($custom, 'watermark', array());
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Validation
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$safe = Str::ifset($custom, 'safe', null);
			if ($safe === null || !is_array($safe)) switch($type) {
				case 'any'      : $safe = array('gif','jpg','png','bmp','jpeg','wbmp','mov','mp4','3gp','wmv','avi','pdf','xls','xlsx','doc','docx','txt','psd','dot','log','wps','rtf','pub'); break;
				case 'image'    : $safe = array('gif','jpg','png','bmp','jpeg','wbmp'); break;
				case 'video'    : $safe = array('mov','mp4','3gp','wmv','avi'); break;
				case 'document' : $safe = array('pdf','xls','xlsx','doc','docx','txt','psd','dot','log','wps','rtf','pub'); break;
				default 		: Editor::error($this->field->title . ' is an invalid Upload Type ('.$type.')');
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Validation
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$this->upload 	= array('upload' => true, 'save' => $save, 'type' => $type, 'filename' => $filename, 'safe' => $safe, 'custom' => $custom);
			return $this;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Order
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function order($a, $b) {
			if ($a['order'] == -1 && $b['order'] == -1) return 0;
			return ($a['order'] < $b['order']) ? -1 : 1;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Has
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function has($area) {
			switch($area) {
				case 'view' 	: return(isset($this->view['view']) && $this->view['view'] == true); break;
				case 'search' 	: return(isset($this->search['search']) && $this->search['search'] == true); break;
				case 'edit' 	: return(isset($this->edit['edit']) && $this->edit['edit'] === true); break;
				case 'sort' 	: return(isset($this->sort['sort']) && $this->sort['sort'] === true); break;
				case 'source' 	: return(isset($this->source['source']) && $this->source['source'] === true); break;
				case 'validate' : return(isset($this->validate['validate']) && $this->validate['validate'] === true); break;
				case 'error'    : return(isset($this->error['error']) && $this->error['error'] === true); break;
				case 'auto'  	: return(isset($this->auto['auto']) && $this->auto['auto'] === true); break;
				case 'upload'  	: return(isset($this->upload['upload']) && $this->upload['upload'] === true); break;
				default : return false;
			}
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Template Marker
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function marker($area) {
			return strtoupper($area . '_' . $this->sqlfield);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Process Uploads
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function files() {
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Check Field has Upload
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if (!$this->has('upload')) 										return EJ::ajax('error', array(), 'Field ('.$this->sqlfield.')does not have required upload data specified');
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Check Minimum Size
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			foreach($_FILES as $file)  if ($file['size'] < 1) 				return EJ::ajax('error', array(), 'An empty file was uploaded');
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Check if Multiple Files Allowed
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if ($this->upload['save'] == 'field' && count($_FILES) > 1) 	return EJ::ajax('error', array(), 'Multiple file uploads are not allowed');
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Check File Type
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			foreach($_FILES as $file) {
				$ext = File::extension($file['name']);
				if (!in_array($ext, $this->upload['safe']))  				return EJ::ajax('error', array(), 'An file with an invalid type was uploaded('.EJ::plain($ext).')');
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Check Maximum Size
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if (isset($this->upload['maxsize'])) foreach($_FILES as $file)  {
				if ($file['size'] > $this->upload['maxsize']) 				return EJ::ajax('error', array(), 'An empty file was uploaded');
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Get the File List
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$files = $_FILES;
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Move the File to Temporary Location
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			foreach($files as $name => $data) {
				$files[$name]['temporary']  = File::directory('site', 'uploads/') . '_tmp_' . substr(EJ::hash(microtime() . mt_rand()),0,16) . '.' . File::extension($file['name']);
				$files[$name]['status'] 	= (@move_uploaded_file($data['tmp_name'], $files[$name]['temporary']));
				if ($files[$name]['status'] === false) $files[$name]['error'] = 'Could not move file from temporary location';
				if (!file_exists($files[$name]['temporary'])) {
					return EJ::ajax('error', array(), 'Could not move file from temporary location (Probably a permissions issue on the uploads directory)');
				}
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Do We Insert into Database?
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if ($this->upload['save'] == 'table') {
				foreach($files as $name => $data) {
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					//|| Skip Bad Files
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					if ($files[$name]['status'] === false) continue;
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					//|| Create the SQL
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					$sql = new SQLWrite;
					foreach($this->upload['custom']['fields'] as $field => $value) {
						switch($value) {
							case '[ORIGINAL]' 	: $sql->text($field, $data['name']); break;
							case '[EXT]' 		: $sql->text($field, ($this->upload['type'] == 'image') ? Image::type($data['temporary']) : File::extension($data['temporary'])); break;
							case '[DATETIME]' 	: $sql->text($field, date('Y-m-d G:i:s')); break;
							case '[DATE]' 		: $sql->text($field, date('Y-m-d')); break;
							case '[TIME]' 		: $sql->text($field, date('G:i:s')); break;
							case '[SIZE]' 		: $sql->text($field, $data['size']); break;
							case '[SIZES]' 		: if (is_array(Str::ifset($this->upload['custom'], 'sizes', null)) && $this->upload['type'] == 'image') { $sql->text($field, implode(',',Str::ifset($this->upload['custom'], 'sizes'))); } else $sql->text($field, 'NOSIZES'); break;
							default 			: if (EJ::int($value, true)) $sql->int($field, $value); else $sql->text($field, $value); break;
						}
					}
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					//|| Insert and Get ID
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					EJ::query($sql->insert($this->upload['custom']['table']));
					$files[$name]['id'] = EJ::id();
				}
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Generate Filename
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			foreach($files as $name => $data) {
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Skip Bad Files
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				if ($files[$name]['status'] === false) { $files[$name]['filename'] = 'bad-upload.fail'; continue; }
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Add Filename for the Rest
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				$ext = ($this->upload['type'] == 'image') ? Image::type($data['temporary']) : File::extension($data['temporary']);
				switch($this->upload['filename']) {
					case '[DEFAULT]'  : $filename = File::directory('site', 'uploads/') . EJ::hash(microtime() . mt_rand()) . '.' . $ext; break;
					case '[MEDIA]'    : $filename = EJ::media($data['id'], 9999, $ext, $this->upload['custom']['area'], 'fullpath'); break;
					default 		  : $filename = $this->upload['filename'] . '.' . $ext; break;
				}
				$files[$name]['filename'] = $filename;
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Move the File to Temporary Location
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			foreach($files as $name => $data) {
				if ($files[$name]['status'] === false) continue;
				File::move($files[$name]['temporary'], $files[$name]['filename']);
				unset($files[$name]['temporary']);
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Check EXIF Data
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if ($this->upload['type'] == 'image') foreach($files as $name => $data) {
				if ($files[$name]['status'] === false) continue;
				$ext  = Image::type($data['filename']);
				$fail = false;
				if (!in_array($ext, $this->upload['safe']))  { $fail = true; File::delete($data['filename']); }
				if ($fail) return EJ::ajax('error', array(), 'An image with an invalid type was uploaded');
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Handle Resizing
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if (is_array(Str::ifset($this->upload['custom'], 'sizes', null)) && $this->upload['type'] == 'image') {
				foreach($files as $name => $data) {
					if ($files[$name]['status'] === false) continue;
					foreach($this->upload['custom']['sizes'] as $size) {
						//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
						//|| Get Resize Filename
						//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
						$ext = ($this->upload['type'] == 'image') ? Image::type($data['filename']) : File::extension($file['name']);
						switch($this->upload['filename']) {
							case '[DEFAULT]'  : $filename = EJ::mediasize($data['filename'], $size); break;
							case '[MEDIA]'    : $filename = EJ::media($data['id'], $size, $ext, $this->upload['area'], 'fullpath'); break;
							default 		  : $filename = $this->upload['filename'] . '_' . $size . '.' . $ext; break;
						}
						//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
						//|| Resize
						//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
						if (Str::exists($size, 'x')) {
							$temp 	= explode('x', $size);
							$width 	= $temp[0];
							$height = $temp[1];
						} else {
							$width  = $size;
							$height = -1;
						}
						$ok = Image::resize($data['filename'], $filename, $width, $height, Str::ifset($this->upload['custom']['watermark'], 'file', false), Str::ifset($this->upload['custom']['watermark'], 'percent', -1), Str::ifset($this->upload['custom']['watermark'], 'x', -1), Str::ifset($this->upload['custom']['watermark'], 'y', false));
						$files[$name]['status'] 			= $ok;
						$files[$name]['sizes'][$size] 		= $filename;
						if (!$ok) $files[$name]['error'] 	= 'Failed Resizing Image . ' . $size;
					}
				}
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Handle Custom After Function
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if (isset($this->upload['custom']['after'])) {
				foreach($files as $name => $data) {
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					//|| Function($this->id, $this->fields, $this->post);
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					if (Str::ifset($this->upload['custom']['after'], 'type') == 'function') EJ::call($this->upload['custom']['after']['action'], $data);
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					//|| Query
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					if (Str::ifset($this->upload['custom']['after'], 'type') == 'query') {
						$sql = Str::ifset($this->upload['custom']['after'], 'query');
						if ($sql == '') return false;
						$sql = Editor::marker($sql, 'ID', 			$data['id']);
						$sql = Editor::marker($sql, 'FILENAME', 	$data['filename']);
						EJ::query($sql);
					}
				}
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Make All Paths Relative / Retrieve Type
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			foreach($files as $name => $data) {
				$files[$name]['filename'] = str_replace(EJ::root(), '/', $files[$name]['filename']);
				$files[$name]['type'] 	  = $this->upload['type'];
				if (isset($data['sizes'])) {
					foreach($data['sizes'] as $size => $value) {
						$files[$name]['sizes'][$size] = str_replace(EJ::root(), '/', $files[$name]['sizes'][$size]);
					}
				}
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Return Proper Data
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			return $files;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| AutoFill Results
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function results($value) {
			if (!$this->has('auto')) EJ::ajax('error', array(), 'Field :: Autofill :: Autofill Data was not specified');
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Handle Types
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			switch($this->auto['type']) {
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Database
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'db' :
					if (!isset($this->auto['custom']['query']) || !isset($this->auto['custom']['fkey']) || !isset($this->auto['custom']['fvalue'])) EJ::ajax('error', array(), 'Field :: Autofill :: DB Source is missing an query, id or value');
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					//|| Ensure we're not loading too large a query
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					$sql = $this->auto['custom']['query'];
					$sql = str_replace(Str::fb($sql, 'select ', ' from '), 'count(*) as maxRecords', $sql);
					$sql = Editor::marker($sql, 'VALUE', ($this->auto['integer']) ? EJ::int($value) : EJ::text($value));
					$max = Recordset::single($sql, 'maxRecords');
					if ($max > 10) return $max;
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					//|| Load the Query
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					$sql  	 = $this->auto['custom']['query'];
					$sql  	 = Editor::marker($sql, 'VALUE', ($this->auto['integer']) ? EJ::int($value) : EJ::text($value));
					$data 	 = new Recordset($sql);
					$array   = array();
					while($data->getnext()) $array[$data->field($this->auto['custom']['fkey'])] = array('show' => $data->field($this->auto['custom']['fvalue']), 'check' => Editor::key('auto', array($data->field($this->auto['custom']['fkey']), $data->field($this->auto['custom']['fvalue']))));
					unset($data);
					return $array;
					break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Source
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'array' :
					if (!isset($this->auto['data'])) EJ::ajax('error', array(), 'Field :: Autofill :: Array ('.$this->title.') was not set');
					$source = $this->auto['data'];
					$array   = array();
					foreach($source as $key => $show) if (Str::exists(strtolower($show), strtolower($value))) $array[$key] = array('show' => $show, 'check' => Editor::key('auto', array($key, $show)));
					return $array;
					break;
			}
			EJ::ajax('error', array(), 'Field :: Autofill :: Field ('.$this->title.') has an Invalid Autofill type('.$this->auto['type'].')');
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Finalize
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function finalize($first = true) {
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Generate Key
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$this->key = substr(md5($this->title . $this->sqlfield . $this->has('view') . $this->has('search') . $this->has('edit') . $this->has('sort') . $this->has('source')) , 0,12);
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Validate Upload
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$this->checkup();
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Step 1 Completed
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if ($first == true) return true;
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Get Source
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if (!$this->has('source')) return true;
			switch($this->source['type']) {
				case 'db' :
					if (!isset($this->source['custom']['query']) || !isset($this->source['custom']['fkey']) || !isset($this->source['custom']['fvalue'])) Editor::error('Field :: DB Source ('.$this->sqlfield.') is missing an query, id or value');
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					//|| Ensure we're not loading too large a query
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					$sql = $this->source['custom']['query'];
					$sql = str_replace(Str::fb($sql, 'select ', ' from '), 'count(*) as maxRecords', $sql);
					$max = Recordset::single($sql, 'maxRecords');
					if ($max > 500) Editor::error('Field :: DB Source call has > 500 records (' . $this->sqlfield . ' / '.$max.' records)');
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					//|| Load the Query
					//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
					$data 	 = new Recordset($this->source['custom']['query']);
					$array   = array();
					while($data->getnext()) $array[strtolower($data->field($this->source['custom']['fkey']))] = $data->field($this->source['custom']['fvalue']);
					unset($data);
					$this->source['data'] = $array;
					break;
				case 'json' :
					$this->source['data'] = json_decode($this->source['data'], true);
					if ($this->source['data'] === false) die('JSON Source ('.$this->title.') was not valid');
					break;
				case 'array' :
					if (!is_array($this->source['data'])) die('Array Source ('.$this->title.') was not valid');
					break;
				default : die('Invalid Source Type('.$this->title.')'); break;
			}
		}


	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
	//|| End Class
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

	}