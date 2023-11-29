
/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| AutoFill
//|| Version 1.0
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Create  Object
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	var AutoFill = {};
	AutoFill.AutoAjax = false;
	AutoFill.Timeout  = false;
	AutoFill.AllClass = 'emptyResults selected hasResults noResults tooMany fetching';

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Parse
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoFill.Parse = function(myInput, myData) {
		$('input[data-type="auto"]').each(function() {
			var myValue     = (typeof($(this).attr('data-value')) == 'undefined') ? '' : $(this).attr('data-value');
			var myShow      = (typeof($(this).attr('data-show')) == 'undefined') ? '' : $(this).attr('data-show');
			var myCheck     = (typeof($(this).attr('data-check')) == 'undefined') ? '' : $(this).attr('data-check');
			$(this).val(myShow);
			$(this).after('<input type="hidden" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" class="value" value="'+myValue+'">');
			$(this).after('<input type="hidden" name="'+$(this).attr('name')+'_check" id="'+$(this).attr('id')+'_check" class="check" value="'+myCheck+'">');
			$(this).attr('id',   $(this).attr('id')  + '_auto');
			$(this).attr('name', $(this).attr('name') + '_auto_' + Math.random().toString(36).replace(/[^a-z]+/g, ''));
			if (myValue != '' && myShow != '' && myCheck != '') $(this).parent().removeClass(AutoFill.AllClass).addClass('selected');
		});
		$('input[data-type="auto"]').off('keyup').on('keyup', function(event){ AutoFill.Change($(this), event); });
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Change
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoFill.Change = function(myInput, myData) {
		/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Handle Keyboard with Results
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
		if (myInput.parent().hasClass('hasResults')) {
			switch(myData.keyCode) {
				case 13 :
					if (myInput.parent().find('ul.results.auto li.selected').length == 0) return false;
					var myLi = myInput.parent().find('ul.results.auto li.selected');
					return AutoFill.Choose(myInput, myLi.attr('data-id'), myLi.html(), myLi.attr('data-check'));
					break;
				case 38 :
				case 40 :
					if (myInput.parent().find('ul.results.auto li.selected').length == 0) return myInput.parent().find('ul.results.auto li:first-child').addClass('selected');
					var myDir   = (myData.keyCode == 40) ? true : false;
					var isFound = false;
					myInput.parent().find('ul.results.auto li').each(function() {
						if (isFound) return false;
						if ($(this).hasClass('selected')) {
							$(this).removeClass('selected');
							if(myDir == true) return $(this).next('li').addClass('selected'); else return $(this).prev('li').addClass('selected');
						}
					});
					if (myInput.parent().find('ul.results.auto li.selected').length == 0) if(myDir == 1) return myInput.parent().find('ul.results.auto li:last-child').addClass('selected'); else return myInput.parent().find('ul.results.auto li:first-child').addClass('selected');
					return false;
					break;
			}
		}
		/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Handle Keyboard No Results
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
		if (myInput.parent().hasClass('emptyResults') && myData.keyCode != 8 && myData.keyCode != 46) {
			myInput.val(myInput.attr('data-previous'));
			return false;
		}
		/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Get new Results
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
		myInput.parent().removeClass(AutoFill.AllClass);
		myInput.attr('data-previous', Editor.Junction.StripHTML(myInput.val()));
		myInput.removeAttr('data-selected');
		myInput.parent().find('input.value,input.check').val('');
		var myMin = (typeof(myInput.attr('data-min')) == 'undefined') ? 4 : myInput.attr('data-min');
		try { AutoFill.AutoAjax.abort();            } catch(e){};
		try { window.clearTimeout(AutoFill.Timeout);} catch(e){};
		if (myInput.val().length >= myMin) {
			AutoFill.Timeout = window.setTimeout(function() { myInput.parent().addClass('fetching'); AutoFill.Fetch(myInput) }, 500);
		}

	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Fetch Results
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoFill.Fetch = function(myInput, myData) {
		var postData = { 'area' : myInput.attr('data-area'), 'field' : myInput.parent().find('input.value').attr('name'), 'value' : myInput.val(), 'coreCall' : 'ajax', 'editorCall' : 'auto' };
		AutoFill.AutoAjax = $.ajax({
			url : '?', type : 'POST', data : postData, timeout : 30000,
			success  : function(respData) {AutoFill.Response(myInput, respData); },
			error    : function(request, status, err)   { AutoFill.Fail(myInput, {'request' : request, 'status' : status, 'error' : err}); }
		});
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Fetch Results
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoFill.Response = function(myInput, myData) {
		myInput.parent().find('span.resultCount,ul.results.auto').remove();
		myInput.parent().removeClass(AutoFill.AllClass);
		/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Fetch Results
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
		try { var myJSON = JSON.parse(myData); } catch (e) { return Editor.Junction.Growl('There was an unexpected error processing this'); }
		if (myJSON.status == 'FAIL')   return Editor.Junction.Growl(myJSON.error);
		if (myJSON.status == 'LOGOUT') return Editor.Junction.Growl('It appears you are logged out. Please <a href="/login/">click here</a> to login again');
		/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Too Many Results
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
		if (typeof(myJSON.data) == 'string' || typeof(myJSON.data) == 'number') {
			myInput.parent().find('i.fa').after('<span class="resultCount">'+myJSON.data+' results</span>');
			return myInput.parent().addClass('tooMany');
		}
		/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| 0 Results
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
		if (myJSON.data.length == 0) return myInput.parent().removeClass(AutoFill.AllClass).addClass('emptyResults');
		/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| 1 Result
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
		var count = 0;
		var mySingle = {};
		$.each(myJSON.data, function(key, value) { count++; mySingle = {'id' : key, 'show' : value['show'], 'check' : value['check']}; });
		if (count == 1) return AutoFill.Choose(myInput, mySingle.id, mySingle.show, mySingle.check);
		/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Multiple Results
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
		myInput.parent().addClass('hasResults');
		var myHTML = '';
		$.each(myJSON.data, function(key, value) { myHTML += '<li data-check="'+value['check']+'" data-id="'+key+'">'+value['show']+'</li>';});
		myInput.after('<ul class="results auto">' + myHTML + '</ul>');
		return AutoFill.AssignActions(myInput);
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Choose
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoFill.Choose = function(myInput, myID, myShow, myCheck) {
		myInput.parent().removeClass(AutoFill.AllClass).addClass('selected');
		myInput.attr('data-selected', myID);
		myInput.val(myShow);
		myInput.parent().find('input.value').val(myID);
		myInput.parent().find('input.check').val(myCheck);
		myInput.parent().find('ul.results.auto').remove();
		return false;
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Error in Response
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoFill.Fail = function(myInput, myData) {
		if (typeof(myData['status']) != 'undefined' && myData['status'] == 'abort') return false;
		myInput.parent().removeClass(AutoFill.AllClass);
		myInput.parent().find('ul.results.auto').remove();
		return Editor.Junction.Growl('There was an error retrieving the data');
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Create Selections
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoFill.AssignActions = function(myInput, myData) {
		myInput.parent().find('ul.results.auto li').off('click').on('click', function() {
			AutoFill.Choose(myInput, $(this).attr('data-id'), $(this).html(), $(this).attr('data-check'));
		});
	};

/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Load AutoFill
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoFill.Load = function() {
		AutoFill.Parse();
	};

/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Runtime
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoFill.Load();
	console.log('Loaded AutoFill!');
