/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Core3 :: Copyright Syhora 2016
//|| Growl.js
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Class
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	var Growl = {};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Open
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Growl.Open	= function(myMessage, myOptions) {
		var myTimeout   = (typeof(myOptions) == 'undefined' || typeof(myOptions['timeout']) == 'undefined') 	? 5000 			: myOptions['timeout'];
		var myType      = (typeof(myOptions) == 'undefined' || typeof(myOptions['type']) == 'undefined') 		? 'alert-info' 	: myOptions['type'];
		var myTitle     = (typeof(myOptions) == 'undefined' || typeof(myOptions['title']) == 'undefined') 		? '' 	        : '<b>' + myOptions['title'] + '</b>';
		var myID      	= 'growlMessage' + Math.floor(Math.random() * (9999999999 - 1 + 1)) + 1;
		var myTop 		= 20;
		$('div.growl').each(function() { myTop = myTop + $(this).height() + 20 + 10; });
		var myMessage = '<div class="growl alert '+myType+'" id="'+myID+'"><a onclick="Growl.CloseAction()" class="close" href="javascript:void(0);"><i class="fa fa-close"></i></a><p>'+myTitle+myMessage+'</p></div>';
		$('body').append(myMessage);
		$('#'+myID).animate({'top': myTop});
		$('#'+myID).fadeIn();
		$('div.growl a.close').off('click').on('click', function() { Growl.CloseAction(myID); });
		window.setTimeout(function(){ Growl.CloseAction(myID); }, myTimeout);
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| AutoClose
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Growl.CloseAction = function(myID) {
		$('#'+myID).fadeOut(500, function() {
			$('#'+myID).remove();
			var myTop 		= 20;
			$('div.growl').each(function() {
				$(this).animate({'top' : myTop});
				myTop = myTop + $(this).height() + 20 + 10;
			});
		});
	};


