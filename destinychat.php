<?php
require_once 'session.php';

$stream = empty($_GET['stream']) ? '' : addslashes(strip_tags(trim($_GET['stream'])));
$s = empty($_GET['s']) ? '' : addslashes(strip_tags(strtolower(trim($_GET['s']))));
$t = empty($_GET['t']) ? '' : addslashes(strip_tags(strtolower(trim($_GET['t']))));

require_once 'blacklist.php';

//set the default stream time to twitch
if($s == "" && $stream == "")
{
  $s = "strims";
}

//if no time is set start from the beginning
if(empty($t))
{
  $t = "0";
}

//support the new way of twitch vod loading
if($s == "twitch-vod")
{
  $httpsearch = strripos($stream, "http://");
  $twitchVOD = explode("/", $stream);
  if($httpsearch === FALSE)
  {
    $channel = $twitchVOD['1'];
    $stream = $twitchVOD['2'] . $twitchVOD['3'];
  }
  else
  {
    $channel = $twitchVOD['3'];
    $stream = $twitchVOD['4'] . $twitchVOD['5'];
  }
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
    <title>OverRustle - <?php echo $stream; ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/overrustle.css" rel="stylesheet">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
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
    <script>
      function twitchAPI() {
        $.ajax({
          url: "https://api.twitch.tv/kraken/streams/<?php echo $stream; ?>?callback=?",
          jsonp: "callback",
          dataType: "jsonp",
          data: {
            format: "json"
          },
         
          success: function(apiData) {
            var output = formatNumber(apiData.stream.viewers) + " Viewers";
            document.getElementById("twitch-ajax").innerHTML = output;
          }
        });
      } 
    </script>
    <?php
    if ($s == "twitch")
    {

      echo '<script>twitchAPI(); window.setInterval(function(){twitchAPI()}, 60000);</script>';
    }
    ?>
  </head>

  <body>

<?php include('navbar.php'); ?>

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
            <object bgcolor='#000000' data='//www-cdn.jtvnw.net/swflibs/TwitchPlayer.swf' height='100%' type='application/x-shockwave-flash' width='100%'> 
              <param name='allowFullScreen' value='true' />
              <param name='allowNetworking' value='all' />
              <param name='allowScriptAccess' value='always' />
              <param name='movie' value='//www-cdn.jtvnw.net/swflibs/TwitchPlayer.swf' /> 
              <param name='flashvars' value='channel=" . $channel . "&start_volume=50&auto_play=true&videoId=" . $stream . "&initial_time=" . $t . "' />
            </object>
            ";
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
          break; //stop fucking asking, I'm not going to add azubu TV. It's shit and so are you for thinking about it     
          
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

        case "livestream":
          echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://cdn.livestream.com/embed/' . $stream . '?layout=4&color=0x000000&autoPlay=true&mute=false&iconColorOver=0xe7e7e7&iconColor=0xcccccc" scrolling="no"></iframe>';
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

        case "riven":
          echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://riven.moe/destiny/videoplayer.php?file=' . $stream . '.mp4" scrolling="no"></iframe>';
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
        <div class="tab-pane fade" id="otherchat" style="height: 100%;">
        <?php
        switch($s)
        {
          case "twitch": 
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.twitch.tv/' . $stream . '/chat?popout=" scrolling="no"></iframe>';
            break;

          case "hitbox":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.hitbox.tv/embedchat/' . $stream . '" scrolling="no"></iframe>';
            break;

          case "ustream":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.ustream.tv/socialstream/' . $stream . '" scrolling="no"></iframe>';
            break;

          case "azubu":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.azubu.tv/' . $stream . '/chatpopup" scrolling="no"></iframe>';
            break;

        }
        ?>
        </div>
      </div>
    </div>
  </div>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/overrustle.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/js/tab.min.js"></script>
  <script src="//api.OverRustle.com/socket.io/socket.io.js"></script>
  <script src="//api.OverRustle.com/strims.js"></script>
  </body>
</html>