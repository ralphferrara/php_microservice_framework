<?php
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Core3 :: Copyright Syhora 2016
//|| Copy
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Global Times
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	Copy::set('GLOBAL_TIME_YEAR',				'year');
	Copy::set('GLOBAL_TIME_MONTH',				'month');
	Copy::set('GLOBAL_TIME_WEEK',				'week');
	Copy::set('GLOBAL_TIME_DAY',				'day');
	Copy::set('GLOBAL_TIME_HOUR',				'hour');
	Copy::set('GLOBAL_TIME_MINUTE',				'minute');
	Copy::set('GLOBAL_TIME_SECOND',				'second');
	Copy::set('GLOBAL_TIME_YEARS',				'years');
	Copy::set('GLOBAL_TIME_MONTHS',				'months');
	Copy::set('GLOBAL_TIME_WEEKS',				'weeks');
	Copy::set('GLOBAL_TIME_DAYS',				'days');
	Copy::set('GLOBAL_TIME_HOURS',				'hours');
	Copy::set('GLOBAL_TIME_MINUTES',			'minutes');
	Copy::set('GLOBAL_TIME_SECONDS',			'seconds');
	Copy::set('GLOBAL_TIME_AGO',				'ago');
	Copy::set('GLOBAL_TIME_NOW',				'Just Now');

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Global Paging
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	Copy::set('GLOBAL_DISPLAYING',				'Displaying');
	Copy::set('GLOBAL_OF',				 		'of');
	Copy::set('GLOBAL_RESULTS',				 	'Results');

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| ReCaptcha
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	Copy::set('GLOBAL_RECAPTCHA_FAIL',			'Incorrect please try again');
	Copy::set('GLOBAL_RECAPTCHA_ENTER',			'Enter the words above:');
	Copy::set('GLOBAL_RECAPTCHA_HEAR',			'Enter the numbers you hear:');

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Errors
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	Copy::set('GLOBAL_MUST_LOGIN',				'You must login to access this area');
	Copy::set('GLOBAL_MUST_PAY',				'You must be a paid member to access this area');
	Copy::set('GLOBAL_EMAIL_REGISTERED',		'This email address is already registered');
	Copy::set('GLOBAL_USERNAME_REGISTERED',		'This username is already registered');
	Copy::set('GLOBAL_FACEBOOK_REGISTERED',		'This facebook account is already registered');
	Copy::set('GLOBAL_FACEBOOK_LOGIN',			'There was an error logging in using facebook');
	Copy::set('GLOBAL_EMAIL_NOTFOUND',			'This email address is not found in our system');
	Copy::set('GLOBAL_EMAIL_LOGGEDIN',			'This account has already logged in. Please use our forgotten password tool');
	Copy::set('GLOBAL_ACCOUNT_NOTFOUND',		'Account not found');
	Copy::set('GLOBAL_LOGGEDOUT',				'You have been logged out');
	Copy::set('GLOBAL_LEVEL',					'You are not authorized to access this area');

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Editor
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	Copy::set('GLOBAL_EDITOR_TWICE',			'Form submitted twice, please re-submit this page.');
	Copy::set('GLOBAL_EDITOR_POSTKEY',			'Post Key was not specified');
	Copy::set('GLOBAL_EDITOR_DELETED',			'This item was deleted');
	Copy::set('GLOBAL_EDITOR_INSERTING',		'Insert a new ');
	Copy::set('GLOBAL_EDITOR_INSERTED',			'Successfully Saved!');
	Copy::set('GLOBAL_EDITOR_EDITING',			'Editing ');
	Copy::set('GLOBAL_EDITOR_DELETING',			'Deleting ');
	Copy::set('GLOBAL_EDITOR_RECORD',			'record');

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Validation
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	Copy::set('GLOBAL_VALIDATION_HEADER',		'Please correct the errors below');
	Copy::set('GLOBAL_VALIDATION_INTEGER',		'Please enter a valid number');
	Copy::set('GLOBAL_VALIDATION_BLANK',		'Field can not be blank');
	Copy::set('GLOBAL_VALIDATION_DATE',			'Please enter a valid date');
	Copy::set('GLOBAL_VALIDATION_LENGTH',		'Please enter a longer value');
	Copy::set('GLOBAL_VALIDATION_PASSWORD',		'Please enter a longer password');
	Copy::set('GLOBAL_VALIDATION_CHARACTERS',	'You have entered invalid characters');
	Copy::set('GLOBAL_VALIDATION_SPECIAL',		'Field can not contain special characters');
	Copy::set('GLOBAL_VALIDATION_ARRAY',		'Please select a valid value');
	Copy::set('GLOBAL_VALIDATION_USERNAME',		'Please enter a valid username');
	Copy::set('GLOBAL_VALIDATION_START',		'Username cannot begin with - or _');
	Copy::set('GLOBAL_VALIDATION_WORD',			'Username cannot contain the word');
	Copy::set('GLOBAL_VALIDATION_PHONE',		'Please enter a valid phone number');
	Copy::set('GLOBAL_VALIDATION_EMAIL',		'Please enter a valid email address');
	Copy::set('GLOBAL_VALIDATION_CREDIT',		'Please enter a valid credit card number');
	Copy::set('GLOBAL_VALIDATION_EXPIRATION',	'Please enter a valid expiration date');
	Copy::set('GLOBAL_VALIDATION_URL',			'Please enter a valid URL');
	Copy::set('GLOBAL_VALIDATION_COUNTRY',		'Country is not valid');
	Copy::set('GLOBAL_VALIDATION_EXISTS',		'This value already exists in our system');
	Copy::set('GLOBAL_VALIDATION_EXISTS_EMAIL', 'This email is already associated with an account');
	Copy::set('GLOBAL_VALIDATION_EXISTS_USERNAME','This username is already taken');

	Copy::set('GLOBAL_VALIDATION_GENDER',		'Invalid Gender');
	Copy::set('GLOBAL_VALIDATION_BRUTE',		'There have been too many failed login requests, please try again in 24 hours.');
	Copy::set('GLOBAL_VALIDATION_ENTER_UE',		'Please enter a valid email address or username');
	Copy::set('GLOBAL_VALIDATION_ENTER_PW',		'Please enter a password');
	Copy::set('GLOBAL_VALIDATION_PW_MATCH',		'Passwords entered do not match');
	Copy::set('GLOBAL_VALIDATION_PW_SHORT',		'Password is too short, please enter a password > 6 characters');
	Copy::set('GLOBAL_VALIDATION_CAPTCHA',		'Security code was not entered correctly, please try again');
	Copy::set('GLOBAL_VALIDATION_IP',			'Please enter a valid IP address');
	Copy::set('GLOBAL_VALIDATION_AGE',			'You must be over 18');
	Copy::set('GLOBAL_VALIDATION_EXP',			'Please enter a valid expiration date');
	Copy::set('GLOBAL_VALIDATION_EXPM',			'Please enter a valid expiration month');
	Copy::set('GLOBAL_VALIDATION_EXPY',			'Please enter a valid expiration year');
	Copy::set('GLOBAL_VALIDATION_CVV',			'Please enter a valid CVV');
	Copy::set('GLOBAL_VALIDATION_BANNED',		'This email address or username has been banned');
	Copy::set('GLOBAL_VALIDATION_BANNED_EMAIL',	'This email address has been banned');
	Copy::set('GLOBAL_VALIDATION_BANNED_USER',	'This username has been banned');
	Copy::set('GLOBAL_VALIDATION_USER_USED',	'This username is already in use');
	Copy::set('GLOBAL_VALIDATION_EMAIL_USED',	'This email address is already in use');

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| AJAX
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
