<?php

$SESSION_LIFETIME_SECS = 30 * 24 * 60 * 60; #30 days
session_set_cookie_params($SESSION_LIFETIME_SECS);
session_start();

?>
