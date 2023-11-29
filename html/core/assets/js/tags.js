
/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| AutoFill
//|| Version 1.0
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Create  Object
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	var AutoTags = {};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Parse
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoTags.Parse = function() {
		$('input[data-type="tags"]').each(function() {
			$(this).after('<input type="hidden" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" class="tags" value="'+AutoTags.toString($(this).val())+'">');
			$(this).attr('id',   $(this).attr('id')  + '_enter');
			$(this).attr('name', $(this).attr('name') + '_enter');
			AutoTags.MakeTags($(this), AutoTags.toString($(this).val()));
			$(this).val('');
		});
		$('input[data-type="tags"]').off('keyup').on('keyup', function(event){ AutoTags.Change($(this), event); });
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Change
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoTags.Change = function(myInput, myData) {
		/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Handle Keyboard with Results
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
		switch(myData.keyCode) {
			case 13 :
				var myCheck = AutoTags.Check(myInput);
				if(myCheck != 'OK') return Editor.Junction.Growl(myCheck);
				AutoTags.AddTag(myInput, Editor.Junction.StripHTML(myInput.val()));
				myInput.val('');
				break;
		}
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Parse
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoTags.toString = function(myValue) {
		if (typeof(myValue) != 'string' || myValue == 'undefined' || myValue == '') return '';
		if (myValue.indexOf(',') < 0) return myValue;
		var myArray = myValue.split(',');
		var myClean = [];
		for(i=0;i<myArray.length;i++) if ($.trim(myArray[i]) != '') myClean.push($.trim(myArray[i]));
		if (myClean.length == 0) return '';
		return myClean.join(',');
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Make Tags
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoTags.MakeTags = function(myInput, myValue) {
		myInput.parent().find('ul.tags,span.none').remove();
		if (typeof(myValue) != 'string' || myValue == 'undefined' || myValue == '') {
			myInput.parent().find('input.tags').val('');
			return myInput.after('<span class="none">Please enter a tag and press enter.</span>');
		}
		myInput.parent().find('input.tags').val(myValue);
		var myHTML = '';
		if (myValue.indexOf(',') < 0) myHTML = AutoTags.Tag(myValue); else {
			var myArray = myValue.split(',');
			for(i=0;i<myArray.length;i++) if ($.trim(myArray[i]) != '') myHTML += AutoTags.Tag($.trim(myArray[i]));
		}
		myInput.after('<ul class="tags">'+myHTML+'</ul>');
		AutoTags.AssignActions(myInput);
		return true;
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Tag
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoTags.Tag = function(myTag) {
		return '<li>' + myTag + '<a href="javascript:;" class="remove fa fa-times" data-tag="'+myTag+'"></a></li>'
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Make Tags
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoTags.RemoveTag = function(myInput, tagName) {
		console.log('Removing Tab -> ' + tagName);
		var myTags = AutoTags.TagArray(myInput);
		if (typeof(tagName) != 'string' || tagName == 'undefined' || $.trim(tagName) == '') return false;
		var myClean = [];
		for(i=0;i<myTags.length;i++) if ($.trim(myTags[i].toLowerCase()) != tagName.toLowerCase()) myClean.push($.trim(myTags[i]));
		console.log('New List');
		console.log(myClean);
		AutoTags.MakeTags(myInput, myClean.join(','));
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Add Tag
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoTags.AddTag = function(myInput, tagName) {
		var myTags = AutoTags.TagArray(myInput);
		if (typeof(tagName) != 'string' || tagName == 'undefined' || $.trim(tagName) == '') return false;
		var myClean = [];
		for(i=0;i<myTags.length;i++) if ($.trim(myTags[i].toLowerCase()) != $.trim(tagName.toLowerCase())) myClean.push($.trim(myTags[i]));
		myClean.push($.trim(tagName));
		AutoTags.MakeTags(myInput, myClean.join(','));
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Tag Array
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoTags.TagArray = function(myInput) {
		var myValue = myInput.parent().find('input.tags').val();
		var newValue = [];
		if (typeof(myValue) != 'string' || myValue == 'undefined' || myValue == '') return[];
		if (myValue.indexOf(',') < 0) return [myValue];
		var myArray = myValue.split(',');
		var myClean = [];
		for(i=0;i<myArray.length;i++) if ($.trim(myArray[i]) != '') myClean.push($.trim(Editor.Junction.StripHTML(myArray[i])));
		return myClean;
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Parse
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoTags.Check = function(myInput) {
		var myTags    = AutoTags.TagArray(myInput);
		var maxLength = (typeof(myInput.attr('data-max-length')) != 'undefined') ? parseInt(myInput.attr('data-max-length')) : -1;
		var maxTags   = (typeof(myInput.attr('data-max-tags')) != 'undefined') ? parseInt(myInput.attr('data-max-tags')) : -1;
		if (maxLength == -1 && maxTags == -1) return 'OK';
		if (myTags.join(',').length > maxLength) return 'Maximum Length has been reached';
		if (myTags.length > maxTags) return 'Maximum Number of Tags has been reached';
		return 'OK';
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Parse
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoTags.AssignActions = function(myInput) {
		myInput.parent().find('ul.tags li a').off('click').on('click', function() {
			AutoTags.RemoveTag(myInput, $(this).attr('data-tag'));
		});
	};


	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Parse
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoTags.Load = function() {
		AutoTags.Parse();
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Runtime
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	AutoTags.Load();
	console.log('Loaded AutoTags!');
