/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Core3 :: Copyright Syhora 2016
//|| Confirm.js
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Class
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	var Confirm = {};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Al, Options
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Confirm.Options = {
		'title'         : '',
		'text'          : '',
		'cancelText'    : 'Cancel',
		'confirmText'   : 'Ok',
		'otherText'     : '',
		'canClose'      : true,
		'onConfirm'     : function(){ return(true); },
		'onCancel'      : function(){ return(true); },
		'onOther'       : function(){ return(true); },
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Open
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Confirm.Open	= function(myOptions) {
		/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Close Anything Previous
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
		Confirm.Close();
		myOptions = (typeof(myOptions) == 'string')     ? {'text' : myOptions, 'cancelText' : ''}  : myOptions;
		myOptions = (typeof(myOptions) == 'undefined')  ? {}                    : myOptions;
		/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Define all Options
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
		$.each(Confirm.Options, function(theOption, theValue) {
			myOptions[theOption] = (typeof(myOptions[theOption]) == 'undefined') ? theValue : myOptions[theOption];
		});
		/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Create HTML
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
		$('body').append('<div class="confirmation basket"><h2 class="confirmation"></h2><a class="confirmation killer"><i class="fa fa-times"></i></a><div class="clearfix"></div><p class="confirmation"></p><button class="confirmation btn cancel"></button><button class="confirmation btn confirm"></button><button class="confirmation btn other"></button><div class="clearfix"></div></div><div class="confirmation overlay"></div>');
		/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Assign Data
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/
		Confirm.Populate(myOptions);
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Populate
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Confirm.Populate	= function(myOptions) {
		if (myOptions['title']      == '')  $('div.confirmation h2.confirmation').remove(); else $('div.confirmation h2.confirmation').html(myOptions['title']);
		if (myOptions['text']       == '')  $('div.confirmation p.confirmation').remove(); else $('div.confirmation p.confirmation').html(myOptions['text']);
		if (myOptions['title'] == '' && myOptions['text'] != '')  $('div.confirmation.basket').addClass('alert');
		if (myOptions['cancelText'] == '')  $('div.confirmation button.confirmation.cancel').remove(); else $('div.confirmation button.confirmation.cancel').html(myOptions['cancelText']);
		if (myOptions['confirmText'] == '') $('div.confirmation button.confirmation.confirm').remove(); else $('div.confirmation button.confirmation.confirm').html(myOptions['confirmText']);
		if (myOptions['otherText'] == '')   $('div.confirmation button.confirmation.other').remove(); else $('div.confirmation button.confirmation.other').html(myOptions['otherText']);
		if (myOptions['canClose'] === false)$('div.confirmation a.confirmation.killer').remove();
		$('div.confirmation button.confirmation.confirm').off('click').on('click', function()  { return Confirm.HandleAction('confirm', myOptions) });
		$('div.confirmation button.confirmation.cancel').off('click').on('click', function()   { return Confirm.HandleAction('cancel', myOptions) });
		$('div.confirmation button.confirmation.other').off('click').on('click', function()    { return Confirm.HandleAction('other', myOptions) });
		$('div.confirmation a.confirmation.killer').off('click').on('click', function()        { return Confirm.HandleAction('cancel', myOptions) });
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Populate
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Confirm.HandleAction = function(myAction, myOptions) {
		Confirm.Close();
		if (myAction == 'confirm') return myOptions['onConfirm']();
		if (myAction == 'cancel')  return myOptions['onCancel']();
		if (myAction == 'other')   return myOptions['onOther']();
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Close
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Confirm.Close	= function() {
		$('div.confirmation').remove();
	};
