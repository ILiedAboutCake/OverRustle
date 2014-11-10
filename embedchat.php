<?php

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'session.php';

if (empty($_GET['user'])) {
    header('HTTP/1.1 400 Bad Request');
    die('User must be specified');
}

$redis = new Predis\Client($config['redis']);
$user = $redis->hgetall('user:'.$_GET['user']);
$channel = $redis->hgetall('channel:'.$_GET['user']);
if (empty($user) || empty($channel)) {
    header('HTTP/1.1 404 Not Found');
    die('Channel not found');
}

include 'blacklist.php';

?>

<!DOCTYPE html>
<html>
<head>
<title>OverRustle - <?php echo $user['name'] ?>'s Chat</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" charset="utf-8">
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/overrustle.css" rel="stylesheet">
</head>

<body class="embed">

<?php include 'chat.php' ?>

</body>
</html>
