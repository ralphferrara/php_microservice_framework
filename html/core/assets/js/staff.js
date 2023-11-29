
/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Staff
//|| Version 1.0
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Create  Object
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	var Staff = {};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Resize Window
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Staff.Size = function() {
		$('div.row.staff').height($(window).height() - 50);
		$('div#side').height($(window).height() - 50);
		$('div.content').height($(window).height() - 50);
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Assign actions
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Staff.AssignActions = function() {
		$('a.sidebar-minify-btn').off('click').on('click', function() {
			$('div#side').addClass('shrunk');
			$('div#side').removeClass('col-md-2');
			$('div#content').removeClass('col-md-10');
			$('div#side').addClass('col-md-1');
			$('div#content').addClass('col-md-11');
		});
		$('a.sidebar-maximize-btn').off('click').on('click', function() {
			$('div#side').removeClass('shrunk');
			$('div#side').removeClass('col-md-1');
			$('div#content').removeClass('col-md-11');
			$('div#side').addClass('col-md-2');
			$('div#content').addClass('col-md-10');
		});

		$('div#side ul li.has-sub>a').off('click').on('click', function() {
			var isOpen = $(this).parent().hasClass('expand');
			$('div#side ul li.has-sub.expand').removeClass('expand');
			$(this).parent().toggleClass('expand', !isOpen);
		});

	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Assign actions
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Staff.Nav = function() {
		var bodyID = $('body').attr('id');
		console.log('BODYID->'+bodyID);
		if (bodyID == undefined || bodyID == '') return false;
		$('div#side ul li a').removeClass('active');
		$('div#side ul li').removeClass('expand');
		$('div#side ul li').each(function(){
			if ($(this).attr('id') == bodyID) {
				$(this).find('a').addClass('active');
				if ($(this).parent().parent().hasClass('has-sub')) $(this).parent().parent().addClass('expand');
			}
		});
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Load
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	Staff.Load = function() {
		Staff.Size()
		Staff.Nav();
		Staff.AssignActions();
		$(window).on('resize', Staff.Size);
	};

	Staff.Load();