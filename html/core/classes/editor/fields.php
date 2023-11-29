<?php
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Syhora :: Core3
//|| EditorView Class
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
//|| Class
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

	class Fields {

	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
	//||
	//|| Init Functions
	//||
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Field
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function field($area, $field, $value) {
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Custom
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			switch($area) {
				case 'search' 	:
					$custom = $field->search['custom'];
					$type   = $field->search['type'];
					break;
				case 'edit' 	:
					$custom = $field->edit['custom'];
					$type   = $field->edit['type'];
					break;
			}
			$custom = self::add($custom, array('area' => $area));
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Values
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if ($value == '') $value = Str::ifset($custom, 'default', $value);
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Handle Types
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$html 			 = '';
			switch($type) {
				case 'view'  	 : $html = '<b>' . $value . '</b>'; break;
				case 'html'  	 : $html = str_replace('{{VALUE}}', $value, Str::ifset($custom, 'html')); break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Standard Texts
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'edit'  	 : $html = self::edit($field->key, $value, 		$custom); break;
				case 'hidden'    : $html = self::hidden($field->key, $value, 	$custom); break;
				case 'textarea'  : $html = self::text($field->key, $value, 		$custom); break;
				case 'password'  : $html = self::password($field->key, $value, 	$custom); break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Custom Texts
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'email'     : $html = self::email($field->key, $value, 	$custom); break;
				case 'phone'     : $html = self::phone($field->key, $value, 	$custom); break;
				case 'url'       : $html = self::url($field->key, $value, 		$custom); break;
				case 'country'   : $html = self::country($field->key, $value, 	$custom); break;
				case 'state'     : $html = self::state($field->key, $value, 	$custom); break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Switches
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'check'     : $html = self::check($field->key, $value, 	$custom); break;
				case 'radio'     : $html = self::radio($field->key, $value, 	$custom); break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Buttons
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'button'    : $html = self::button($field->key, $value, 	$custom); break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Numbers
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'number'   :
				case 'integer'   : $html = self::integer($field->key, 	$value, 	$custom); break;
				case 'float'     : $html = self::float($field->key, 	$value, 	$custom); break;
				case 'money'     : $html = self::money($field->key, 	number_format($value, 2), 	$custom); break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Dates
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'datetime'  : $html = self::datetime($field->key, 	date('Y-m-d G:i:s', strtotime($value)), 	$custom); break;
				case 'date'      : $html = self::date($field->key, 		date('Y-m-d', 		strtotime($value)), 	$custom); break;
				case 'time'      : $html = self::time($field->key, 		date('G:i:s', 		strtotime($value)), 	$custom); break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Slider
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'range'     :
				case 'slider'    : $html = self::slider($field->key, $value, 	$custom); break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Header
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'header'    : $html = self::header($field->title, $custom); break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Yes No Select
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'yesno'     :
					$custom['data'] 	= array('1' => 'Yes', '0' => 'No');
					$html 				= self::select($field->key, $value, 	$custom);
					break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Select
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'changepw'  :
					$custom['data-type'] = 'changepw';
					$custom['data-mode'] = (EJ::get('mode') == 'edit') ? 'edit' : 'insert';
					$html = self::password($field->key, '', 	$custom); break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Select
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'select'    :
					switch($custom['type']) {
						case 'db' 		: $html = self::db($field->key, $value, 	$custom); break;
						case 'array' 	: $html = self::select($field->key, $value, 	$custom); break;
						case 'source' 	:
							if (!$field->has('source')) $html = 'Select field has no source';
							$custom['data'] = $field->source['data'];
							$html = self::select($field->key, $value, $custom);
							break;
					}
					break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Autofill
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'auto'      :
					$input = ($area == 'search') ? $_GET : $_POST;
					$show  = Str::ifset($field->auto['custom'], 'data-show');
					$check = Editor::key('auto', array($value, $show));
					foreach($input as $key => $val) {
						if (substr($key, 0, strlen($field->key . '_check')) == $field->key . '_check') $check = EJ::plain($val);
						if (substr($key, 0, strlen($field->key . '_auto'))  == $field->key . '_auto')  $show = EJ::plain($val);
					}
					if (!Editor::key('auto', array($value, $show), $check)) { $value = ''; $show  = ''; $check = ''; }
					$add 				= array();
					$add['data-min'] 	= Str::ifset($field->auto['custom'], 'data-min', 3);
					$add['data-value'] 	= $value;
					$add['data-show'] 	= $show;
					$add['data-check'] 	= $check;
					$html = self::auto($field->key, $value,	 	self::add($custom, $add));
					break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Tags
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'tags'      : $html = self::tags($field->key, $value,	 	$custom); break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Mode
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'mode'      :
					$add 					= array();
					if(isset($custom['values'])) {
						$add['data-value-on'] 	= Str::ifset($custom['values'], 'on',  '1');
						$add['data-value-off'] 	= Str::ifset($custom['values'], 'off', '0');
						unset($custom['values']);
					}
					if(isset($custom['label'])) {
						$add['data-label-on'] 	= Str::ifset($custom['label'], 'on',  'ON');
						$add['data-label-off'] 	= Str::ifset($custom['label'], 'off', 'OFF');
						unset($custom['label']);
					}
					if(isset($custom['function'])) {
						$add['data-function'] = $custom['function'];
						unset($custom['function']);
					}
					$html = self::mode($field->key, $value,	 	self::add($custom, $add));
					break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Mode
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				case 'upload'    :
					$custom['data-upload-type'] = $field->upload['type'];
					$custom['data-accepted'] 	= '.' . implode(',.', $field->upload['safe']);
					if(isset($field->upload['custom']['function'])) $custom['data-function'] = $field->upload['custom']['function'];
					$html = self::upload($field->key, $value, 		$custom);
					break;
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Fail
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				default 		 : $html = 'Invalid Edit Field Type';break;
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Return
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			return $html;
		}


		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Input
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function edit($name, 		$value = null, $custom = array()){ 				return self::input('text',				$name, $value, $custom); }
		static function password($name,		$value = null, $custom = array()){ 				return self::input('password',			$name, $value, $custom); }
		static function hidden($name, 		$value = null, $custom = array()){ 				return self::input('hidden',			$name, $value, $custom); }

		static function email($name, 		$value = null, $custom = array()){ 				return self::input('email',				$name, $value, self::add($custom, array('data-type' => 'email')));}
		static function phone($name, 		$value = null, $custom = array()){ 				return self::input('phone',				$name, $value, self::add($custom, array('data-type' => 'phone')));}
		static function url($name, 			$value = null, $custom = array()){ 				return self::input('url',				$name, $value, self::add($custom, array('data-type' => 'url')));}

		static function button($name, 		$value = null, $custom = array()){ 				return self::input('button',			$name, $value, $custom); }
		static function submit($name, 		$value = null, $custom = array()){ 				return self::input('submit',			$name, $value, $custom); }

		static function check($name, 		$value = null, $custom = array()){ 				return self::input('checkbox',			$name, $value, $custom); }
		static function radio($name, 		$value = null, $custom = array()){ 				return self::input('radio',				$name, $value, $custom); }

		static function date($name, 		$value = null, $custom = array()){ 				return self::input('date',				$name, $value, self::add($custom, array('data-type' => 'date')));}
		static function time($name, 		$value = null, $custom = array()){ 				return self::input('time',				$name, $value, self::add($custom, array('data-type' => 'time')));}
		static function datetime($name, 	$value = null, $custom = array()){ 				return self::input('datetime-local',	$name, $value, self::add($custom, array('data-type' => 'datetime')));}

		static function integer($name, 		$value = null, $custom = array()){ 				return self::input('number',			$name, $value, self::add($custom, array('data-type' => 'number', 'step' => Str::ifset($custom, 'step', '1'))));}
		static function float($name, 		$value = null, $custom = array()){ 				return self::input('number',			$name, $value, self::add($custom, array('data-type' => 'float','step' => Str::ifset($custom, 'step', '0.01'))));}
		static function money($name, 		$value = null, $custom = array()){ 				return self::input('number',			$name, $value, self::add($custom, array('data-type' => 'money','step' => Str::ifset($custom, 'step', '0.01'))));}

		static function slider($name, 		$value = null, $custom = array()){				return self::input('range',				$name, $value, self::add($custom, array('data-type' => 'slider','min' => Str::ifset($custom, 'min', 0), 'max' => Str::ifset($custom, 'max', 9999))));}
		static function auto($name, 		$value = null, $custom = array()){				return self::input('text',				$name, $value, self::add($custom, array('data-type' => 'auto')));}
		static function tags($name, 		$value = null, $custom = array()){				return self::input('text',				$name, $value, self::add($custom, array('data-type' => 'tags','data-max-length' => Str::ifset($custom, 'max-length', -1), 'data-max-tags' => Str::ifset($custom, 'max-tags', -1))));}

		static function mode($name, 		$value = null, $custom = array()){				return self::input('hidden',			$name, $value, self::add($custom, array('data-type' => 'mode')));}
		static function upload($name, 		$value = null, $custom = array()){				return self::input('hidden',			$name, $value, self::add($custom, array('data-type' => 'upload')));}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Input
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function header($title, $custom = array()) {
			$template 				= new Template;
			$template->main     	= Str::ifset($custom, 'template', 'editor/editorview.html');
			$template				= $template->subtemplate('header');
			$template->add('TITLE', $title);
			if (isset($custom['additional']) && is_array($custom['additional'])) foreach($custom['additional'] as $key => $value) $template->add($key, $value);
			return $template->compile(false, false);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Custom Selects :: Database
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function db($name, $value = null, $custom = array()){
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Validation
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if (Str::ifset($custom, 'query', false) === false || Str::ifset($custom, 'fkey', false) === false || Str::ifset($custom, 'fvalue', false) === false) Editor::error("Fields :: DB Select :: Query, ID or Value was not provided (" . $name .")");
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Pull Records
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$data = new Recordset;
			$data->open($custom['query']);
			$list = array();
			while($data->getnext()) $list[$data->field($custom['fkey'])] = $data->field($custom['fvalue']);
			$custom['data'] = $list;
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Generate HTML
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			return self::select($name, $value, $custom);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Custom Selects :: State
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function state($name, $value = null, $custom = array()){
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Custom Fields
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$custom['data-type'] = 'state';
			if (Str::ifset($custom, 'scgroup', null) !== null) $custom['data-country'] = Str::ifset($custom, 'scgroup');
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Get Country File
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$country = Str::ifset($custom, 'country', 9999);
			$path = (EJ::isNew()) ? 'data/geo/' : '/data';
			$file 	 = File::directory('core', $path) . $country . '.csv';
			if (!is_numeric($country) || !file_exists($file)) return self::input('text', $name, $value, $custom);
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Crate Array
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$csv  = EJ::csv(File::read($file));
			if (count($csv) == 0) return(self::input('text', $name, $value, $custom));
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Create Array
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$data = array();
			foreach($csv as $item) if (isset($item[0]) && isset($item[1])) $data[$item[1]] = $item[1];
			$custom['data'] = $data;
			return self::select($name, $value, $custom);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Custom Selects :: Country
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function country($name, $value = null, $custom=array()){
			$data = array();
			$path = (EJ::isNew()) ? 'data/geo/' : '/data';
			$custom['data-type'] = 'country';
			$csv  = EJ::csv(File::read(File::directory('core', $path) . 'countries.csv'));
			if (Str::ifset($custom, 'scgroup', null) !== null) $custom['data-state'] = Str::ifset($custom, 'scgroup');
			foreach($csv as $item) if (is_numeric(trim($item[0])) && $item[1] != '') $data[$item[0]] = $item[1];
			$custom['data'] = $data;
			return self::select($name, $value, $custom);
		}


	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
	//||
	//|| Sub Functions
	//||
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Input
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function input($type, $name, $value = post, $custom = array()){
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Data Type
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$dt 	= Str::ifset($custom, 'data-type');
			$after  = ($dt == 'range' || $dt == 'slider') ? '<b class="range value">0</b>' : '<i class="fa"></i>';
			$after  = (in_array($dt, array('mode','upload')))  ? '' : $after;
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Input
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$tags  	= array();
			$tags[] = self::pair('id', 			Str::ifset($custom, 'id', 		$name));
			$tags[] = self::pair('class', 		self::cssClass(Str::ifset($custom, 'class'), $type));
			$tags[]	= self::pair('type', 		$type);
			$tags[]	= self::pair('data-area', 	Str::ifset($custom, 'area'));
			$tags[]	= self::pair('name', 		$name);
			$tags[]	= self::pair('value', 		self::value($value));
			$custom = self::ignore($custom);
			foreach($custom as $key => $val) $tags[] = self::pair($key, $val);
			return ($dt == '') ? '<input ' . implode(' ', $tags) .' />' : '<span class="field-wrap '.$dt.'"><input ' . implode(' ', $tags) .' />'.$after.'</span>';
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Text Area
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function text($name, $value='', $custom = array()){
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Do we strip HTML? Default Yes.
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$value = (Str::ifset($custom, 'html', false) === true) ? $value : self::value($value);
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Tags
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$tags  	= array();
			$tags[] = self::pair('id', 			Str::ifset($custom, 'id', 		$name));
			$tags[] = self::pair('class', 		self::cssClass(Str::ifset($custom, 'class'), Str::ifset($custom, 'data-type')));
			$tags[]	= self::pair('data-area', 	Str::ifset($custom, 'area'));
			$tags[]	= self::pair('name', 		$name);
			$custom = self::ignore($custom);
			foreach($custom as $key => $val) $tags[] = self::pair($key, $val);
			return('<textarea '.implode(' ', $tags).'>' . $value . '</textarea>');
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Select
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static function select($name, $value='', $custom = array()){
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Data
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$data 	= self::data(Str::ifset($custom, 'data', array()));
			if (isset($custom['first'])) $data = array('' => $custom['first']) + $data;
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Tags
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$tags  	= array();
			$tags[] = self::pair('id', 			Str::ifset($custom, 'id', 		$name));
			$tags[] = self::pair('class', 		self::cssClass(Str::ifset($custom, 'class'), Str::ifset($custom, 'data-type')));
			$tags[]	= self::pair('data-area', 	Str::ifset($custom, 'area'));
			$tags[]	= self::pair('name', 		$name);
			$custom = self::ignore($custom, array('first', 'area', 'data'));
			foreach($custom as $key => $val) $tags[] = self::pair($key, $val);
			$html = '';
			if (count($data) == 0) $html = '<option>No options available</option>'; else foreach($data as $key => $val) {
				$option = '<option{{SELECTED}} {{KEY}}>{{VALUE}}</option>';
				$option = str_replace('{{SELECTED}}', 		(self::value($value) == $key) ? ' selected="selected"' : '', $option);
				$option = str_replace('{{VALUE}}', 			$val, $option);
				$option = str_replace('{{KEY}}', 			self::pair('value',trim($key)),   $option);
				$html .= $option;
			}
			return '<select '.implode(' ', $tags).'>' . $html . '</select>';
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Private Option
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function option($type, $name, $value='', $custom = array()){
			$dt 	= Str::ifset($custom, 'data-type');
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Input
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$tags  	= array();
			$tags[] = self::pair('id', 			Str::ifset($custom, 'id', 		$name));
			$tags[] = self::pair('class', 		self::cssClass(Str::ifset($custom, 'class'), $type));
			$tags[]	= self::pair('type', 		$type);
			$tags[]	= self::pair('data-area', 	Str::ifset($custom, 'area'));
			$tags[]	= self::pair('name', 		$name);
			$tags[]	= self::pair('value', 		self::value($value));
			if ($value == Str::ifset($custom, 'default')) $tags[] = self::pair('checked','checked');
			$custom = self::ignore($custom);
			foreach($custom as $key => $val) $tags[] = self::pair($key, $val);
			return ($dt == '') ? '<input ' . implode(' ', $tags) .' />' : '<span class="field-wrap '.$dt.'"><input ' . implode(' ', $tags) .' /></span>';
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Value
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		static private function value($data) {
			return EJ::plain($data);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Input
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		private static function pair($key, $value) {
  			return $key . '="'.trim($value).'"';
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Class
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		private static function cssClass($class, $type = null) {
			$classes = array();
			$classes = ($class != '') ? explode(' ', $class) : array();
			$classes[] = 'form-control';
			if ($type != null) $classes[] = $type;
			return trim(str_replace('  ', ' ', implode(' ', $classes)));

		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Handle Select Data
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		private static function data($data) {
			if (!is_array($data)) return array();
			if (array_keys($data) !== range(0, count($data) - 1)) return $data; // Is Associative
			if (count($data) == 0) return $data;
			$new = array();
			for($i=0;$i<count($data);$i++) $new[$data[$i]] = $data[$i];
			return $new;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Add a Custom Field
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		private static function add($custom, $add = array()) {
			foreach($add as $key => $value) $custom[$key] = $value;
			return $custom;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Ignore
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		private static function ignore($custom, $more = array()) {
			$ignore 	= array('invalid', 'query', 'equator', 'fkey', 'fvalue', 'area', 'max-tags', 'max-length', 'data', 'fields', 'scgroup', 'state', 'country', 'default', 'html');
			foreach($ignore as $ignored) if (isset($custom[$ignored])) unset($custom[$ignored]);
			if (count($more) > 0) foreach($more as $ignored) if (isset($custom[$ignored])) unset($custom[$ignored]);
			return $custom;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| End Class
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

	}