<?php

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'helpers.php';
require_once 'session.php';

$redis = new Predis\Client($config['redis']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['service']) || empty($_POST['stream'])) {
        header('HTTP/1.1 400 Bad Request');
        die('Invalid input');
    }

    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
        $channel = array( 'service' => sanitize($_POST['service']), 'stream' => sanitize($_POST['stream']) );
        $redis->hmset('channel:'.$user['name'], $channel);
        header('Location: /channel?user='.$user['name']);
        die();
    } else {
        header('HTTP/1.1 401 Unauthorized');
        die('Unauthorized');
    }
} else {
    if (empty($_GET['user'])) {
        header('HTTP/1.1 400 Bad Request');
        die('User must be specified');
    }

    $user = $redis->hgetall('user:'.$_GET['user']);
    $channel = $redis->hgetall('channel:'.$_GET['user']);
    if (empty($user) || empty($channel)) {
        header('HTTP/1.1 404 Not Found');
        die('Channel not found');
    }
}

$s = $channel['service'];
$stream = $channel['stream'];

//handle stream blacklists even in channels
require_once 'blacklist.php';

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Watch streams and videos with destiny.gg!">
    <link rel="icon" href="favicon.ico">
    <title>OverRustle - <?php echo $user['name'] ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/overrustle.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-49711133-1', 'overrustle.com');
      ga('send', 'pageview');
    </script>
  </head>

  <body>
    <?php include 'navbar.php' ?>

    <div class="container-full fill">
        <div class="pull-left stream-box" id="map">
        <?php
        switch(strtolower($s))
        {
          case "twitch":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.twitch.tv/' . $stream . '/embed" scrolling="no"></iframe>';
            break;

          case "twitch-vod":
            echo "
              <object data='http://www.twitch.tv/widgets/archive_embed_player.swf' id='clip_embed_player_flash' type='application/x-shockwave-flash' width='100%' height='100%'>
                <param name='movie' value='http://www.twitch.tv/widgets/archive_embed_player.swf' />
                <param name='allowScriptAccess' value='always' />
                <param name='allowNetworking' value='all' />
                <param name='allowFullScreen' value='true' />
                <param name='flashvars' value='initial_time=" . $t . "&start_volume=25&auto_play=true&archive_id=" . $stream . "' />
              </object>";
            break;

          case "nsfw-chaturbate":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="https://chaturbate.com/embed/' . $stream . '?bgcolor=black" scrolling="no"></iframe>';
            break;

          case "advanced":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="' . $stream . '" scrolling="yes"></iframe>';
            break;

          case "castamp":
            if (strlen(strstr($_SERVER['HTTP_USER_AGENT'], 'Firefox')) > 0)
            {
              echo '<script type="text/javascript"> channel="' . $stream . '"; vwidth="1280"; vheight="720";</script><script type="text/javascript" src="http://castamp.com/embed.js"></script>';
            }
            else
            {
              echo '<script type="text/javascript"> channel="' . $stream . '";</script><script type="text/javascript" src="js/castamp.js"></script>';
            }
            break;

          case "hitbox":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.hitbox.tv/embed/' . $stream . '?autoplay=true" scrolling="no"></iframe>';
            break;

          case "youtube":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.youtube.com/embed/' . $stream . '?autoplay=1&start=' . $t . '" scrolling="no"></iframe>';
            break;

          case "youtube-playlist":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.youtube.com/embed/videoseries?list=' . $stream . '&autoplay=1&start=' . $t . '" scrolling="no"></iframe>';
            break;

          case "mlg":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.majorleaguegaming.com/player/embed/' . $stream . '" scrolling="no"></iframe>';
            break;

          case "ustream":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.ustream.tv/embed/' . $stream . '?v=3&wmode=direct&autoplay=true" scrolling="no"></iframe>';
            break;

          case "dailymotion":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.dailymotion.com/embed/video/' . $stream . '" scrolling="no"></iframe>';
            break;

          case "azubu":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.azubu.tv/azubulink/embed=' . $stream . '" scrolling="no"></iframe>';
            break;

          case "picarto":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="https://www.picarto.tv/live/playerpopout.php?popit=' . $stream . '&off=1&token=undefined" scrolling="no"></iframe>';
            break;

          case "strims":
            include('strims_content.php');

        }
        ?>
        </div>

        <div class="pull-right" id="map" style="width: 390px;">
          <div class="tab-content" style="height: 100%;">
            <div class="tab-pane fade active in" id="destinychat" style="height: 100%;">
              <iframe width="100%" marginheight="0" marginwidth="0" frameborder="0" src="https://destiny.gg/embed/chat" scrolling="no" style="height: 100%;"></iframe>
            </div>
            <?php if ($config['chat_enable']): ?>
            <div class="tab-pane fade" id="otherchat" style="height: 100%;">
            <?php include 'chat.php' ?>
            </div>
            <?php endif ?>
          </div>
        </div>
  </div>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/overrustle.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="//api.OverRustle.com/socket.io/socket.io.js"></script>
  <script src="//api.OverRustle.com/strims.js"></script>
  </body>
</html>