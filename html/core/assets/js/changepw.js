
/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| ChangePW
//|| Version 1.0
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Create  Object
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	var ChangePW = {};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Parse
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	ChangePW.Parse = function() {
		var myHTML = '<div class="changepw {{CLASSMODE}}">' +
			'   <input type="password" id="{{MYID}}" {{{DISABLEDMODE}} name="{{MYNAME}}" data-type="changepw" data-area="{{MYAREA}}" class="form-control password"><i class="fa"></i>' +
			'   <div class="changepw reenter">' +
			'       <label>Re-enter Password</label>' +
			'       <input type="password" id="{{MYID}}_reenter" class="form-control password" name="{{MYNAME}}_reenter" class="changepw reenter"><i class="fa"></i>' +
			'   </div><input type="{{TYPEMODE}}" id="{{MYID}}_check" name="{{MYNAME}}_check" value="change" data-type="" class="changepw check"><b class="changepw updateText">Update Password</b></div>';
		$('input[data-type="changepw"]').each(function() {
			var myName  = $(this).attr('name');
			var myID    = $(this).attr('id');
			var myArea  = $(this).attr('data-area');
			var myMode  = $(this).attr('data-mode');
			myHTML = Editor.Junction.ReplaceAll(myHTML, '{{MYID}}',   myID);
			myHTML = Editor.Junction.ReplaceAll(myHTML, '{{MYNAME}}', myName);
			myHTML = Editor.Junction.ReplaceAll(myHTML, '{{MYAREA}}', myArea);
			myHTML = Editor.Junction.ReplaceAll(myHTML, '{{TYPEMODE}}', (myMode == 'edit') ? 'checkbox' : 'hidden');
			myHTML = Editor.Junction.ReplaceAll(myHTML, '{{CLASSMODE}}', (myMode == 'edit') ? '' : 'active');
			myHTML = Editor.Junction.ReplaceAll(myHTML, '{{DISABLEDMODE}}', (myMode == 'edit') ? ' disabled="disabled"' : '');
			$(this).parent().find('i.fa').remove();
			$(this).replaceWith(myHTML);
			window.setTimeout(function() {
				$('div.changepw input[type="password"]').val('');
			}, 50);
		});
		ChangePW.AssignActions();
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Create Selections
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	ChangePW.AssignActions = function() {
		$('input.changepw.check').off('change click blur').on('change click blur', function() {
			if ($(this).is(':checked')) {
				$(this).parent().addClass('active');
				$(this).parent().find('[data-type="changepw"]').removeAttr('disabled');
			} else {
				$(this).parent().find('[data-type="changepw"]').attr('disabled', 'disabled');
				$(this).parent().removeClass('active');
			}
		});
	};

	/*||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Runtime
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||*/

	console.log('Loading ChangePW!');
	ChangePW.Parse();
