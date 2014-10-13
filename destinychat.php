<?php
$stream = addslashes(strip_tags(trim($_GET['stream'])));
$s = addslashes(strip_tags(strtolower(trim($_GET['s']))));
$t = addslashes(strip_tags(strtolower(trim($_GET['t']))));

//what the fuck, go use destiny's website for this FeedNathan
if(strtolower($stream) == "destiny")
{
  header('Location: http://destiny.gg/bigscreen');
}

//set the default stream time to twitch
if($s == "" && $stream == "")
{
  $s = "strims";
}

//if no time is set start from the beginning
if($t == "")
{
  $t = "0";
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
    <title>OverRustle - Beta</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/overrustle.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
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
   <script>
    var ws = new WebSocket("ws://overrustle.com:9998/ws");

    var sendObj = new Object();
    sendObj.strim = "/destinychat?s=<?php echo $s ?>&stream=<?php echo $stream; ?>";

    //if we get connected :^)
    ws.onopen = function(){
        console.log('Connected to OverRustle.com Websocket Server :^)');
      sendObj.action = "join";
      ws.send(JSON.stringify(sendObj));
    };

    //if we get disconnected >:(
    ws.onclose = function(evt) {
      console.log('Disconnected from OverRustle.com Websocket Server >:(');
    };

    //the only time we ever get a message back will be a server broadcast
    ws.onmessage = function (evt) {
      document.getElementById("server-broadcast").innerHTML = "" + formatNumber(evt.data) + "";
    };

    //function code for grabbing current viewcount via websocket.
    function overRustleAPI() {
      sendObj.action = "viewerCount";
      ws.send(JSON.stringify(sendObj));
    }

    //update the viewer count every 5 seconds
    window.setInterval(function(){overRustleAPI()}, 5000);

    //On Disconnect 
    $(window).on('beforeunload', function() {
      sendObj.action = "unjoin";
      ws.send(JSON.stringify(sendObj));
    });
    </script>
    <?php
    if ($s == "twitch")
    {

      echo '<script>twitchAPI(); window.setInterval(function(){twitchAPI()}, 60000);</script>';
    }
    ?>
  </head>

  <body>

<nav class="navbar navbar-default navbar-inverse" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <a class="navbar-brand" href="/strims">OverRustle</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="#"><div id="twitch-ajax"></div></a></li>
        <li><a target="_blank" href="/strims"><div id="server-broadcast"></div></a></li>
        <li class="donate"><a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6TUMKXJ23YGQG"><span>Donate</span></a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Advanced<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a target="_blank" href="https://github.com/ILiedAboutCake/OverRustle#">Github</a></li>
            <li><a target="_blank" href="http://overrustle.com:9998/api">Streams API</a></li>
            <li class="divider"></li>
            <li><a target="_blank" href="http://destiny.gg">Destiny.gg</a></li>
          </ul>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
      <form action="destinychat" class="navbar-form navbar-left" role="search">
        <div class="form-group">
          <select name="s" class="form-control">
            <option value="twitch">Twitch</option>
            <option value="twitch-vod">Twitch - VOD</option>
            <option value="hitbox">Hitbox</option>
            <option value="castamp">CastAmp</option>            
            <option value="youtube">Youtube</option>
            <option value="mlg">MLG (Beta*)</option>
            <option value="ustream">Ustream (Beta*)</option>
            <option value="dailymotion">Dailymotion</option>
            <option value="advanced">Advanced</option>
          </select>
          <input type="text" name="stream" type="text" class="form-control" placeholder="Stream/Video ID"/> 
          <button type="submit" class="btn btn-default">Go</button>
        </div>
      </form>

      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

    <div class="container-full fill">
        <div class="pull-left stream-box" id="map">
        <?php
        switch($s) 
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
            
          case "mlg":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.teamliquid.net/video/streams/' . $stream . '/popout" scrolling="no"></iframe>';
            break;
            
          case "ustream":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.ustream.tv/embed/' . $stream . '?v=3&wmode=direct&autoplay=true" scrolling="no"></iframe>';
            break;

          case "dailymotion":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.dailymotion.com/embed/video/' . $stream . '" scrolling="no"></iframe>';
            break;

          case "strims":
            echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="/strims"></iframe>';
        }
        ?>
        </div>

        <div class="pull-right" id="map" style="width: 390px;">
          <iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://destiny.gg/embed/chat" scrolling="no"></iframe>
        </div>
    </div>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/overrustle.js"></script>
  </body>
</html>
