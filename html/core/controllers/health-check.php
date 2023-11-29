<?php
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
//|| Copyright :: SocialCO Media, LLC 2017
//|| Health Check Script
//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Core
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	require('/core/load.php');

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Global Variable
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    global $global;

   	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Alerts
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    global $alerts;
    $alerts = array();
    function alert($level, $message) { global $alerts; $alerts[] = array('level' => $level, 'message' => $message); }

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Handle Response
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    $response                   = array();
    $response['status']         = Instance::get('INSTANCE.SETTING.STATUS');
    $response['db']             = 'DWN';
    $response['cache']          = 'DWN';
    $response['node']           = 'DWN';
    $response['backups']        = 'NA';
    $response['cpu']            = 'NA';
    $response['cpu_total']      = -1;
    $response['memory']         = 'NA';
    $response['memory_total']   = -1;
    $response['memory_free']    = -1;
    $response['storage']        = 'NA';
    $response['storage_total']  = -1;
    $response['storage_free']   = -1;
    $response['custom']         = 'NA';
    $response['time']           = -1;
    $response['alert_count']    = 0;
    $response['alerts']         = array();

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Check Database Connection
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    if (!isset($global->connections['main'])) $response['db'] = 'NA';           else $response['db']      = Core::db()->health();
    if ($response['db'] != 'NA' && $response['db'] != 'OK') alert('ERR', 'Database :: Down/Error');

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Check Database Connection
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    if (!isset($global->cache['main'])) $response['cache'] = 'NA';              else $response['cache']   = Core::cache()->health();
    if ($response['cache'] != 'NA' && $response['cache'] != 'OK') alert('ERR', 'Cache :: Down/Error');

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Backups
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    if ($response['backups'] != 'NA' && $response['backups'] != 'OK') alert('ERR', 'Backups :: Down/Error');
    //TO DO TO DO TO DO
    //INTEGRATE BACKUP SYSTEM!!
    //INTEGRATE BACKUP SYSTEM!!

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Node Processes
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    if(Core::config('MICROSERVICE.NODES') == 0) $response['node'] = 'NA';       else {
        $res        = shell_exec('pgrep node');
        $res        = str_replace(array(chr(10),chr(13),' '), '|', $res);
        $res        = str_replace(array('||||','|||','||'), '|', $res);
        $pid        = explode('|', $res);
        $count      = 0;
        for($i=0;$i<count($pid);$i++) if (is_numeric($pid[$i])) $count++;
        if ($count != Core::config('MICROSERVICE.NODES'))   alert('ERR', 'Node :: PID counts do not match ['.Core::config('MICROSERVICE.NODES').' configured /'.$count.' PIDs]');
        if ($count < Core::config('MICROSERVICE.NODES'))    $response['node'] = 'DWN';
        if ($count == Core::config('MICROSERVICE.NODES'))   $response['node'] = 'OK';
        if ($count > Core::config('MICROSERVICE.NODES'))    $response['node'] = 'ERR';
    }

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Server Data
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    $response['cpu_total']      = Server::cpu();
    $response['memory_total']   = Server::memory('total');
    $response['memory_free']    = Server::memory('free');
    $response['storage_total']  = Server::storage('total');
    $response['storage_free']   = Server::storage('free');

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| CPU
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    $response['cpu']            = 'OK';
    if ($response['cpu_total'] > 85) {
        $response['cpu']         = 'WARN';
        alert('WARN', 'CPU :: Usage is > 85% ['.$percent.'%]');
    }
    if ($response['cpu_total'] >= 95) {
        $response['cpu']         = 'ERR';
        alert('ERR', 'CPU :: Usage > 95% ['.$percent.'%]');
    }

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Check Memory
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    $response['memory']         = 'OK';
    $percent                    = floor(($response['memory_free'] / $response['memory_total']) * 100);
    if ($percent > 85 && $percent < 95) {
        $response['memory']         = 'WARN';
        alert('WARN', 'Memory :: Usage is > 85% ['.$percent.'%]');
    }
    if ($percent >= 95) {
        $response['memory']         = 'ERR';
        alert('ERR', 'Memory :: Usage > 95% ['.$percent.'%]');
    }

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Check Storage
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    $response['storage']        = 'OK';
    $percent                    = floor(($response['storage_free'] / $response['storage_total']) * 100);
    if ($percent > 90 && $percent < 95) {
        $response['storage']         = 'WARN';
        alert('WARN', 'Hard Drive usage > 90% ['.$percent.'%]');
    }
    if ($percent >= 95) {
        $response['memory']         = 'ERR';
        alert('ERR', 'Hard Drive usage > 95% ['.$percent.'%]');
    }

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Check Storage Partitions > 90
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    $partitions = Server::storage('partitions');
    if ($partitions == 'ERR' || count($partitions) == 0) alert('Server Partitions unavailable'); else {
        foreach($partitions as $part => $data) {
            if ($data['percent'] > 90 && $data['percent'] < 95) {
                alert('WARN', 'Usage > 90% on partition [' . $part . ']');
                $response['storage'] = 'WARN';
            }
            if ($data['percent'] >= 90) {
                alert('ERR', 'Usage > 90% on partition [' . $part . ']');
                $response['storage'] = 'ERR';
            }
        }
    }

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Custom Check
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

	$custom = new Health;
	$custom->check();
	$custom = $custom->response();
    $response['custom']     = Str::ifset($custom, 'result', 'NA');
	if(Str::ifset($custom, 'result', 'NA') != 'NA') foreach($custom['alerts'] as $message) alert($message['level'], $message['message']);

    //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
    //|| Time/Notes
    //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    $response['time']           = Core::loaded();
    if ($response['time'] > 1000) alert('WARN', 'Response Time :: Server Response Time > 1000ms');
    if ($response['time'] > 5000) alert('ERR', 'Response Time :: Server Response Time > 5000ms');
    $response['alerts']         = $alerts;
    $response['alert_count']    = count($response['alerts']);

	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
	//|| Handle AWS/User Health Check
	//||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    if (MicroService::header() == false) {
        $public = array('internal' => false, 'code' => 200);
        switch(Instance::get('INSTANCE.SETTING.STATUS')) {
            case 'OFF' : $public['code'] = Errors::http(503, array('justheader' => true)); break;
            case 'DWN' : $public['code'] = Errors::http(500, array('justheader' => true));  break;
            default    :
                if ($response['db'] == 'ERR'    || $response['db'] == 'DWN')    $public['code'] = Errors::http(500, array('justheader' => true));
                if ($response['cache'] == 'ERR' || $response['cache'] == 'DWN') $public['code'] = Errors::http(500, array('justheader' => true));
                break;
        }
        die(json_encode($public));
    }

    //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||
    //|| Response
    //||=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=-||

    MicroService::respond(true, 'Evaluation Completed', $response);
