<?php
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Syhora :: Core3
//|| ListView Class
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
//|| Class
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

	class ListView {

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Data
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		var $fields;
		var $sql;
		var $template;
		var $sort;
		var $page;
		var $perpage;
		var $ulimit;
		var $dir;
		var $actions;


		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Construct
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function __construct($sql = null, $fields = null) {
			$this->sql 		= $sql;
			$this->fields 	= $fields;
			$this->actions	= array('view' => false, 'insert' => false, 'edit' => false, 'delete' => false);
			$this->ulimit 	= 0;
			$this->perpage  = false;
			$this->custom 	= array();
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| QueryString
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function querystring() {
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Get QueryString Data
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$this->page 	= (is_numeric(EJ::get('page'))) ? EJ::get('page') : 1;
			if ($this->perpage === false) $this->perpage	= (in_array(EJ::get('perpage'), array(10,25,50,100))) ? EJ::get('perpage') : 25;
			$this->sort 	= EJ::get('sort');
			$this->dir 		= EJ::get('dir');
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Compile
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function compile($custom = array()) {
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| View
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if ($this->actions['view'] == false) return 'ListView :: List Results are not available';
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Validate Data
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if (!is_object($this->sql)) return 'ListView :: SQL not specified';
			if (!is_array($this->fields) || count($this->fields) == 0) return 'ListView :: Fields not specified';
			if ($this->sql->id == '' && ($this->actions['edit'] || $this->actions['delete'])) return 'ListView :: SQL :: ID Field not specified. Required for Edit/Delete';
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Custom Settings
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$this->perpage  = Str::ifset($custom, 'perpage', false);
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Get QueryString Data
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$this->querystring();
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Paging
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$this->sql->limit = 1000;
			if (Str::ifset($custom, 'paging', true) === true) {
				$this->sql->limit = $this->perpage;
				$this->sql->start = ($this->page - 1) * $this->perpage;
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Sort
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$sortfield 	= $this->fetch('sort', $this->sort);
			if (is_object($sortfield)) {
				if (isset($sortfield->sort['sort']['custom'])) $this->sort = $sortfield->sort['sort']['custom']; else $this->sort = $sortfield->sqlfield;
				$this->sql->order = array($this->sort . ' ' . $this->dir);
			} else {
				$this->sort = '';
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Sort Fields
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if ($this->fetch('order') === true) usort($this->fields, function($a, $b) {
				if ($a->view === false) return 0;
				if ($a->view['order'] ==  $b->view['order']) return 0;
				return ($a->view['order'] < $b->view['order']) ? -1 : 1;
			});
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Get the Template
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$this->template 		= new Template;
			$this->template->main	= Str::ifset($custom, 'template', 'editor/listview.html');
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Container Template
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$template = $this->container($custom);
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| PerPage Class
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$template->add('PERPAGECLASS', 			(Str::ifset($custom, 'perpage', false) == false) ? 'perpage' : '');
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Create Headers
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$template->add('EDITOR_CAPTION',		$this->caption($custom));
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Create Headers
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$template->add('EDITOR_HEADERS',		$this->headers($custom));
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Create Results
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$template->add('EDITOR_RESULTS',		$this->results($custom));
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Create Paging
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$template->add('EDITOR_PAGING',			$this->paging($custom));
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Return HTML
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			return $template->compile(false);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Container
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		private function container($custom = array()) {
			$sub = $this->template->subtemplate('container');
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Manage Classes
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$actions   	 = ($this->actions['view']) 			? ' view' 	: '';
			$actions   	.= ($this->actions['insert']) 			? ' insert' 	: '';
			$actions	.= ($this->actions['edit']) 			? ' edit' 		: '';
			$actions	.= ($this->actions['delete']) 			? ' delete' 	: '';
			$sub->add('ACTIONCLASS', 			$actions);
			return $sub;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Caption
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		private function caption($custom = array()) {
			if (Str::ifset($custom, 'caption', false) == false) return '';
			$sub = $this->template->subtemplate('caption');
			$sub->add('CAPTION', 	Str::ifset($custom, 'caption'));
			return $sub->compile(false);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Headers
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		private function headers($custom = array()) {
			$html = '';
			foreach($this->fields as $field) {
				if ($field->view['view'] !== true) continue;
				$toggle = ($this->dir == 'asc') ? 'desc' : 'asc';
				$class  = ($this->dir == 'asc') ? ' fa-caret-down' : ' fa-caret-up';
				$sub 	= ($field->has('sort')) ? $this->template->subtemplate('head-sort') : $this->template->subtemplate('head-plain');
				$sub->add('FIELDNAME',			$field->title);
				$sub->add('SORTKEY',			$field->key);
				$sub->add('SORTDIR',			($this->sort == $field->sqlfield) ? $toggle : 'asc');
				$sub->add('TAGS',				$this->tags(true, $field, $field->title, false));
				$html .= $sub->compile();
			}
			$html .= $this->template->subtemplate('head-actions')->compile();
			return $html;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Results
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		private function results($custom = array()) {
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Spit out the Results
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$data = new Recordset($this->sql->compile(true));
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Empty Recordset
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if ($data->rowcount() == 0) {
				$sub = $this->template->subtemplate('empty');
				$sub->add('COLSPAN',		$this->fetch('count.viewable'));
				$sub->add('TERM',			Str::ifset($custom, 'term', 'result(s)'));
				return $sub->compile();
			}
			$rows = '';
			while($data->getnext()) {
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Limit for Paging
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				$this->ulimit = $data->field('ulimit');
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Loop Through Fields
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				$cells = '';
				foreach($this->fields as $field) {
					if ($field->view['view'] !== true) continue;
					$sub   = $this->template->subtemplate('cell');
					$sub->add('TAGS',				$this->tags(false, $field, $data->field($field->sqlfield), $data));
					$sub->add('CELLDATA',			$this->cell($field, $data));
					$cells .= $sub->compile();
				}
				$cells .= $this->template->subtemplate('cell-actions')->compile();
				$rows  .= $this->row($cells, $data, $field);
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| All Done
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			}
			return $rows;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Headers
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		private function paging($custom = array()) {
			if (!Str::ifset($custom, 'paging', true)) return '';
			$paging 		= new Pagination();
			$paging->main	= File::directory('program','views/editor/') . 'paging.json';
			return $paging->compile('page', $this->page, $this->ulimit, $this->perpage, '', Str::ifset($custom, 'term', 'result(s)'));
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Row
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		private function row($cells, $data, $custom = array()) {
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Tags
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$tags 				= array();
			if ($this->sql->id != '' && $data->field($this->sql->id) != '') $tags['data-id'] 	= $data->field($this->sql->id);
			if ($this->sql->id != '' && $data->field($this->sql->id) != '' && ($this->actions['edit'] || $this->actions['delete'])) $tags['data-key'] 	= Editor::key('modify', array($data->field($this->sql->id)));
			$html 				= ' ';
			foreach($tags as $key => $value) $html .= $key .'="'.$value.'"';
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Sub Template
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$sub 	= $this->template->subtemplate('row');
			$sub->add('TAGS', 	$html);
			$sub->add('CELLS', 	$cells);
			return $sub->compile(false);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Tags
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		function tags($header, $field, $value, $data) {
			$tags  = array();
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Tags
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$class = array();
			if ($this->sort == $field->sqlfield) $class[] = 'sorted';
			if (Str::ifset($field->view['custom'],'class') != '') $class[] = Str::ifset($field->view['custom'],'class');
			if (count($class) > 0) $tags[] = 'class="'.implode(' ', $class).'"';
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Width
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if (Str::ifset($field->view['custom'],'width') != '') $tags[] = 'width="'.$field->view['custom']['width'].'"';
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Style
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if (Str::ifset($field->view['custom'],'style') != '') $tags[] = 'style="'.$field->view['custom']['style'].'"';
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Results Only
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if (!$header) {
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| ID
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				if (Str::ifset($field->view['custom'],'id') != '') $tags[] = 'id="'.$field->view['custom']['id'].'"';
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Onclick
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				if (Str::ifset($field->view['custom'],'onclick') != '') $tags[] = 'onclick="'.$field->view['custom']['onclick'].'"';
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| Data Tags
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				foreach($field->view['custom'] as $key => $value) if (substr($key, 0, 5) == 'data-') $tags[] = $key . '="'.$value.'"';
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				//|| All Done
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
				return (count($tags) > 0) ? ' ' . self::replace(implode(' ', $tags), $value, $data, $field->view['custom']) : '';
			}
			return (count($tags) > 0) ? ' ' . implode(' ', $tags) : '';
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Replace
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		private function replace($html, $value, $data, $custom = array()) {
			$html = urldecode($html);
			$html = html_entity_decode($html);
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Custom Data
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$html = Editor::marker($html, 'URL',     Str::ifset($custom, 'url', 'javascript:;'));
			$html = Editor::marker($html, 'ICON', 	 (Str::ifset($custom, 'icon', false) !== false) ? '<i class="fa '.$custom['icon'].'"></i>' : '');
			$html = Editor::marker($html, 'ONCLICK', ' onclick="'.Str::ifset($custom, 'onclick', 'javascript:;').'"');
			$html = Editor::marker($html, 'TITLE',   Str::ifset($custom, 'title', ''));
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Check for Database Fields
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if (Str::exists($html, '{{FIELD(')) {
				$columns = $data->columns;
				foreach($columns as $key => $value) {
					$html = Editor::marker($html, 'FIELD('.strtoupper($key).')', $data->field($key));
				}
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Replace Value
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$html = Editor::marker($html, 'VALUE', $value);
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| All Done
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			return $html;
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Cell
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		private function cell($field, $data) {
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Values
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			$value 		= $data->field($field->sqlfield);
			$type  		= $field->view['type'];
			$sub   		= $this->template->subtemplate('cell-plain');
			$html  		= $value;
			$invalid    = Str::ifset($field->view['custom'], 'invalid', 'N/A');
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Get the Source Value
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			if ($field->has('source')) $value = Str::ifset($field->source['data'], $value, $invalid);
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Handle Types
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			switch($type) {
				case 'plain' 	: $html = '{{VALUE}}'; break;
				case 'action' 	: $html = $this->template->subtemplate('cell-action')->compile(false); break;
				case 'url'  	: $html = $this->template->subtemplate('cell-link')->compile(false); break;
				case 'percent'  : $html = (!Validation::float($value)) 		? $invalid : number_format($value,Str::ifset($field->view['custom'], 'decimal', 0)) . '&#37;'; break;
				case 'date' 	: $html = (strtotime($value) !== false) ? date(Str::ifset($field->view['custom'], 'format', 'm/d/Y g:i:s a'), strtotime($value)) 			: $invalid; break;
				case 'timeago' 	: $html = (strtotime($value) !== false) ? '<span class="timeago" data-time="'.date('D, d M Y G:i:s \U\T\C', strtotime($value)) .'"></span>' 	: $invalid; break;
				case 'boolean' 	: $html = (in_array($value, array(0,1)) ) ? str_replace(array('0','1', '86'), array('False','True', 'Deleted'), $value) : $invalid; break;
				case 'yesno' 	: $html = (in_array($value, array(0,1,86)) ) ? str_replace(array('0','1','86'), array('No','Yes','Deleted'), $value) : $invalid; break;
				case 'function' : if (Str::ifset($field->view['custom'], 'function', false) == false) $html = 'Invalid Function()'; else $html = EJ::call($field->view['custom']['function'], array('value' => $value, 'custom' => $field->view['custom'])); break;
				case 'money'    : $html = (!Validation::float($value)) 		? $invalid : '&#36;' . number_format($value,2); break;
				case 'integer'  : $html = (!Validation::integer($value)) 	? $invalid : number_format($value,0); break;
				case 'float'    : $html = (!Validation::float($value)) 		? $invalid : number_format($value,Str::ifset($field->view['custom'], 'decimal', 4), Str::ifset($field->view['custom'], 'separator', ',')); break;
				case 'left'     : $html = substr($value, 0, Str::ifset($field->view['custom'], 'length', 100)); break;
				case 'right'    : $html = substr($value, -Str::ifset($field->view['custom'], 'length', 100)); break;
				case 'image'    : $html = '<a href="{{VALUE}}" target=_"blank"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" style="border:1px solid #CCC; background-image:url({{VALUE}});background-size:cover;" width="50" height="50"></a>'; break;
				case 'trim'     : if (Str::ifset($field->view['custom'], 'length', false) == false) $html = trim($value); else $html = substr(trim($value), 0, $field->view['custom']['length']); break;
				case 'strip'    : if (Str::ifset($field->view['custom'], 'length', false) == false) $html = strip_tags($value); else $html = substr(strip_tags($value), 0, $field->view['custom']['length']); break;
				case 'toHTML'   : $html = EJ::plain($value); break;
				case 'select'   : $html = Fields::select($field->sqlfield, $value, Str::ifset($field->view['custom'], 'data', array()), $field->view['custom']); break;
				case 'button'   :
					$url    	= (Str::ifset($field->view['custom'], 'url', null) !== null) ? "window.location.href='".$field->view['custom']['url']."'" : '';
					$onclick    = Str::ifset($field->view['custom'], 'onclick', $url);
					if ($onclick != '') $field->view['custom']['onclick'] = $onclick;
					$html 		= Fields::button($field->sqlfield, self::replace(Str::ifset($field->view['custom'], 'label', 'No label set'), $value, $data, $field->view['custom']), $field->view['custom']);
					break;
				case 'template' : if (!is_object(Str::ifset($field->view['custom'], 'template', false))) $html = 'Template not set'; else $html = $field->view['custom']['template']->compile(false); break;
				case 'source'   : $html = Str::ifset($field->source['data'], $value, $value); break;
			}
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			//|| Replace All Data and Return
			//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
			return self::replace($html, $value, $data, $field->view['custom']);
		}

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
		//|| Fetch
		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

		private function fetch($area, $value = null) {
			$count = 0;
			for($i=0;$i<count($this->fields);$i++) {
				switch($area) {
					case 'sort' 			: if ($this->fields[$i]->key == $value && $this->fields[$i]->has('sort')) return $this->fields[$i]; break;
					case 'count.viewable' 	: if ($this->fields[$i]->has('view')) $count++; break;
					case 'order' 			: if ($this->fields[$i]->has('view') && $this->fields[$i]->view['order'] != -1) $count++; break;
				};
			}
			switch($area) {
				case 'sort' 			: return false;
				case 'count.viewable' 	: return ($this->actions['edit'] || $this->actions['delete']) ? $count + 1 : $count;
				case 'order' 			: return ($count > 0);
			};
		}

	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||
	//|| End Class
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=||

	}