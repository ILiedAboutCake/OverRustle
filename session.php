<?php

$LIFETIME = 30 * 24 * 60 * 60; #30 days
session_set_cookie_params($LIFETIME);
session_start();

?>
