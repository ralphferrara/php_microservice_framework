
/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Mode
//|| Version 1.0
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Create  Object
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	var Mode = {};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Parse
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Mode.Parse = function() {
		$('input[data-type="mode"]').each(function() {
			$(this).after('<ul class="mode"><li></li><li></li></ul>');
			Mode.Change($(this), false);
		});
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Get Label
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Mode.Change = function(myInput, isToggle) {
		var myValOn    = (myInput.attr('data-value-on')  != undefined) ? myInput.attr('data-value-on')  : '1';
		var myValOff   = (myInput.attr('data-value-off') != undefined) ? myInput.attr('data-value-off') : '0';
		var myLabelOn  = (myInput.attr('data-label-on')  != undefined) ? myInput.attr('data-label-on')  : 'ON';
		var myLabelOff = (myInput.attr('data-label-off') != undefined) ? myInput.attr('data-label-off') : 'OFF';
		var myMode     = (myInput.val() == undefined || myInput.val() == myValOff) ? false : true;
		myMode         = (isToggle) ? !myMode : myMode;
		myInput.parent().find('li').html('');
		myInput.parent().removeClass('on off');
		if (myMode == true) myInput.parent().find('ul.mode li:first-child').html(myLabelOn); else myInput.parent().find('ul.mode li:last-child').html(myLabelOff);
		myInput.val((myMode) ? myValOn : myValOff);
		myInput.parent().addClass((myMode) ? 'on' : 'off');
		Mode.AssignActions();
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Function
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Mode.Function = function(myInput) {
		if (myInput.attr('data-function') == undefined) return false;
		var myField    = (myInput.attr('name') != undefined) ? myInput.attr('name') : 'Undefined Function';
		var myFunction = window[myInput.attr('data-function')];
		if (typeof(myFunction)  !== 'function') Editor.Junction.Growl('Mode Function for ('+myField+') is not a function('+typeof(myFunction)+')');
		var myValOff   = (myInput.attr('data-value-off') != undefined) ? myInput.attr('data-value-off') : '0';
		var myMode     = (myInput.val() == undefined || myInput.val() == myValOff) ? false : true;
		return myFunction(myInput.attr('id'), myMode);
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Create Selections
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Mode.AssignActions = function() {
		$('ul.mode li').off('click').on('click', function(){
			console.log('Clicking');
			Mode.Change($(this).parent().parent().find('input'), true);
			Mode.Function($(this).parent().parent().find('input'));
		});
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Load AutoFill
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Mode.Load = function() {
		Mode.Parse();
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Runtime
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Mode.Load();
	console.log('Loaded Mode!');
