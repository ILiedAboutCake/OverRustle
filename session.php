<?php

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'helpers.php';

$SESSION_LIFETIME_SECS = 30 * 24 * 60 * 60; #30 days
session_set_cookie_params($SESSION_LIFETIME_SECS);
session_start();

/*

$redis = new Predis\Client($config['redis']);

# store sid and recent ip in redis for all users
$sid = session_id();
$ip = get_user_ip();
$session = array('id' => $sid, 'ip' => $ip, 'last_seen' => time());
$skey = 'session:'.$sid;
$redis->hmset($skey, $session);

if ($redis->ttl($skey) < 0) {
    $redis->expire($skey, $SESSION_LIFETIME_SECS);
}

*/
?>
