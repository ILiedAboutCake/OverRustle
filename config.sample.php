<?php
    # Copy this file to config.php and replace with your credentials

    # Twitch API
    # https://github.com/justintv/Twitch-API/blob/master/authentication.md
    $config['twitch_client_id'] = 'client id goes here';
    $config['twitch_client_secret'] = 'client secret goes here';
    $config['twitch_redirect_uri'] = 'redirect uri goes here e.g. http://overrustle.com/profile';
    # Redis
    $config['redis'] = array(
        'host' => 'localhost',
        'port' => 6379,
        'database' => 1); # python uses database 0

    # Chat websocket config
    $config['chat_host'] = 'localhost'; # overrustle.com
    $config['chat_port'] = 9999; # python uses 9998
?>
