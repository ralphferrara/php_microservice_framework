<?php
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Core3 :: Copyright Syhora 2016
//|| Payments
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Class
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	class Payments {

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Return Method Name
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function statuses($name = null) {
			$data 			= array();
			$data['OK']       = 'OK';
			$data['X']        = 'Deleted';
			$data['F']        = 'Failed';
			if ($name === null) return $data;
			return Str::ifset($data, $name, 'Unknown');
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Return Method Name
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function methods($name = null) {
			$data 			= array();
			$data['CC']       = 'Credit Card';
			$data['CH']       = 'Check';
			$data['CA']       = 'Cash';
			$data['BC']       = 'Bitcoin';
			$data['WI']       = 'Wire';
			$data['PP']       = 'Paypal';
			$data['FR']       = 'Comp/Free';
			if ($name === null) return $data;
			return Str::ifset($data, $name, 'Unknown');
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Return Merchant Name
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function merchants($name = null) {
			$data 			= array();
			$data['PP']      = 'Paypal';
			$data['SQ']      = 'Square';
			$data['ST']      = 'Stripe';
			$data['NA']      = 'None';
			if ($name === null) return $data;
			return Str::ifset($data, $name, 'Unknown');
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Add a Payment
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function add($account, $billing, $shipping, $amount, $merchant, $custom = array()) {
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Create the Addresses
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$idb = self::address($account, $billing);
			$ids = self::address($account, $shipping);
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Create the Payment
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$sql = new SQLWrite;
			$sql->int('fid_account', 			$account);
			$sql->int('fid_address_billing', 	$idb);
			$sql->int('fid_address_shipping', 	$ids);
			$sql->text('payment_status', 		'OK');
			$sql->text('payment_amount', 		$amount);
			$sql->text('payment_merchant',		$merchant);
			$sql->text('payment_method',		Str::ifset($custom, 'method', 'CC'));
			$sql->text('payment_ip',			Str::ifset($custom, 'ip'));
			$sql->text('payment_response',		Str::ifset($custom, 'response'));
			$sql->text('payment_reference',		Str::ifset($custom, 'reference'));
			$sql->text('payment_subscription',	Str::ifset($custom, 'subscription'));
			$sql->text('payment_lastfour',		Str::ifset($custom, 'lastfour'));
			$sql->text('payment_started',		date('Y-m-d G:i:s'));
			$sql->text('payment_completed',		date('Y-m-d G:i:s'));
			$sql->text('payment_source',		Str::ifset($custom, 'source'));
			$sql->text('payment_comment',		Str::ifset($custom, 'comment'));
			if (Core::db()->query($sql->insert('{{PRE}}payments'))) return Core::db()->id; else return false;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Add a Address
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function address($account, $custom = array()) {
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Create the Payment
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$sql = new SQLWrite;
			$sql->int('fid_account', 			$account);
			$sql->text('address_fname', 		Str::ifset($custom, 'fname'));
			$sql->text('address_lname', 		Str::ifset($custom, 'lname'));
			$sql->text('address_address1', 		Str::ifset($custom, 'address1'));
			$sql->text('address_address2', 		Str::ifset($custom, 'address2'));
			$sql->text('address_city', 			Str::ifset($custom, 'city'));
			$sql->text('address_state', 		Str::ifset($custom, 'state'));
			$sql->text('address_zip', 			Str::ifset($custom, 'zip'));
			$sql->text('address_country', 		Str::ifset($custom, 'country'));
			$sql->text('address_phone', 		Str::ifset($custom, 'phone'));
			if (Core::db()->query($sql->insert('{{PRE}}addresses'))) return Core::db()->id; else return false;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| Add a Subscription
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

		static function subscription($account, $package, $amount, $card, $customer, $plan, $subscription, $custom = array()) {
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			//|| Create the Payment
			//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
			$sql = new SQLWrite;
			$sql->int('fid_account', 				$account);
			$sql->text('subscription_package', 		$package);
			$sql->text('subscription_amount', 		$amount);
			$sql->text('subscription_card', 		$card);
			$sql->text('subscription_customer',		$customer);
			$sql->text('subscription_plan',			$plan);
			$sql->text('subscription_subscription', $subscription);
			$sql->text('subscription_last4', 		Str::ifset($custom, 'lastfour'));
			$sql->text('subscription_expm', 		Str::ifset($custom, 'expm'));
			$sql->text('subscription_expy', 		Str::ifset($custom, 'expy'));
			$sql->text('subscription_status', 		'A');
			if (Core::db()->query($sql->insert('{{PRE}}subscriptions'))) return Core::db()->id; else return false;
		}

		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
		//|| EOC
		//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	}

?>