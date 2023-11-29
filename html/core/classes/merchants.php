<?php
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Core3 :: Copyright Syhora 2016
//|| Merchants
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    class Merchants {

        var $method;
        var $setup;
        var $fname, $lname, $name;
        var $email;
        var $address1, $address2;
        var $city;
        var $state;
        var $zip;
        var $country;

        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
        //|| Construct
        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

        function __construct($method, $setup = array()) {
            $this->method = $method;
            $this->setup = $setup;
            $this->fname = '';
            $this->lname = '';
            $this->address1 = '';
            $this->address2 = '';
            $this->city = '';
            $this->state = '';
            $this->zip = '';
            $this->country = '';
            $this->email = '';
        }

        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
        //|| Customer Function
        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

        function customer() {
            $customer = array();
            $customer['fname'] = $this->fname;
            $customer['lname'] = $this->lname;
            $customer['name'] = ($this->name != '') ? $this->name : $this->fname . ' ' . $this->lname;
            $customer['address1'] = $this->address1;
            $customer['address2'] = $this->address2;
            $customer['city'] = $this->city;
            $customer['state'] = $this->state;
            $customer['zip'] = $this->zip;
            $customer['country'] = $this->country;
            $customer['email'] = $this->email;
            return $customer;
        }

        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
        //|| Process a Transaction
        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

        function process($amount, $custom = array()) {
            switch ($this->method) {
                case 'stripe'        :
                    return Stripe::process($this->setup, $this->customer(), $amount, $custom);
                    break;
            }
        }

        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
        //|| Process a Subscription
        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

        function subscription($customer, $amount, $currency, $package, $interval, $custom = array()) {
            switch ($this->method) {
                case 'stripe'        :
                    return Stripe::subscription($this->setup, $customer, $amount, $currency, $package, $interval, $custom);
            }
        }

        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
        //|| Cancel a Subscription
        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

        function cancel($subscription) {
            switch ($this->method) {
                case 'stripe'        :
                    return Stripe::cancel($this->setup, $subscription);
            }
        }

        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
        //|| Return the Plan
        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

        static function plan($merchant, $amount, $currency, $package, $interval) {
            //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
            //|| Read
            //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
            $sql = new SQLRead;
            $sql->field('id_plan');
            $sql->field('plan_token');
            $sql->table('{{PRE}}plans');
            $sql->where("plan_merchant = '" . Safe::text($merchant) . "'");
            $sql->where("plan_amount = '" . Safe::text($amount) . "'");
            $sql->where("plan_package = '" . Safe::text($package) . "'");
            $sql->where("plan_currency = '" . Safe::text($currency) . "'");
            $sql->where("plan_interval = '" . Safe::text($interval) . "'");
            $sql->where("plan_status = 'A'");
            $data = new Recordset($sql->compile());
            if ($data->rowcount() > 0) return array('id' => $data->field('id_plan'), 'token' => $data->field('plan_token'));
            return false;
        }

        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
        //|| Fail
        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

        static function fail($merchant, $reason, $data = array()) {
            if (Core::admin()) $reason = 'Merchants :: ' . ucwords($merchant) . ' :: ' . $reason;
            return array('status' => 'FAIL', 'message' => $reason);
        }

        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
        //|| Success
        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

        static function success($data = array()) {
            return array('status' => 'OK', 'message' => 'Payment Succeeded', 'data' => $data);
        }


        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
        //|| Return the Plan
        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

        static function plan_name($amount, $package, $interval) {
            return 'Subscription-' . $package . '-' . $interval . '-' . number_format($amount, 0, '', '');
        }

        static function plan_id($amount, $package, $interval) {
            return strtolower($package . '-' . $interval . '-' . number_format($amount, 0, '', '') . '-' . date('Ymd') . '-' . Str::random(10));
        }

        static function plan_create($token, $merchant, $amount, $currency, $interval, $package) {
            //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
            //|| Disable Old Ones
            //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
            $sql = new SQLWrite;
            $sql->text("plan_status", 'D');
            $where = array();
            $where[] = "plan_merchant = '" . Safe::text($merchant) . "'";
            $where[] = "plan_amount = '" . Safe::text($amount) . "'";
            $where[] = "plan_package = '" . Safe::text($package) . "'";
            $where[] = "plan_currency = '" . Safe::text($currency) . "'";
            $where[] = "plan_interval = '" . Safe::text($interval) . "'";
            $where[] = "plan_status = 'A'";
            $sql = $sql->update('{{PRE}}plans', implode(' and ', $where));
            Core::db()->query($sql);
            //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
            //|| Create New
            //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
            $sql = new SQLWrite;
            $sql->text('plan_token', $token);
            $sql->text('plan_merchant', $merchant);
            $sql->text('plan_amount', $amount);
            $sql->text('plan_currency', $currency);
            $sql->text('plan_interval', $interval);
            $sql->text('plan_package', $package);
            $sql->text('plan_name', self::plan_name($amount, $package, $interval));
            return Core::db()->query($sql->insert('{{PRE}}plans'));
        }

        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
        //|| End Class
        //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    }

