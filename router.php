<?php

#Router for PHP local dev server
#Run with 'php -S 0.0.0.0 router.php'

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (!preg_match('/\.\w+$/', $path)) {
    include_once __DIR__ . '/' . $path. '.php';
} else {
    return false;
}

?>
