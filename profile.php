<?php

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'helpers.php';
require_once 'session.php';

$SERVICE_OPTIONS = array(
    "twitch" => "Twitch",
    "twitch-vod" => "Twitch - VOD",
    "hitbox" => "Hitbox",
    "castamp" => "CastAmp",
    "youtube" => "Youtube",
    "mlg" => "MLG (Beta*)",
    "ustream" => "Ustream (Beta*)",
    "dailymotion" => "Dailymotion",
    "azubu" => "Azubu",
    "picarto" => "Picarto",
    "advanced" => "Advanced"
);

$redis = new Predis\Client(array('database' => 1));

if (!empty($_GET['code'])) {
    $login_params = array(
        'client_id'     => $config['twitch_client_id'],
        'client_secret' => $config['twitch_client_secret'],
        'grant_type'    => 'authorization_code',
        'redirect_uri'  => $config['twitch_redirect_uri'],
        'code'          => $_GET['code']
    );
    $result = post_url_contents("https://api.twitch.tv/kraken/oauth2/token", $login_params);
    $access_result = json_decode($result);
    if (isset($access_result->access_token)) {
        $token = $access_result->access_token;
        $user_url = 'https://api.twitch.tv/kraken/user?oauth_token='.$token;
        $result = get_url_contents($user_url);
        $user_result = json_decode($result);
        if (isset($user_result->name) && isset($user_result->_id)) {
            $user = array('id' => $user_result->_id, 'name' => $user_result->name);
            $redis->hmset('user:'.$user_result->name, $user);
            $_SESSION['user'] = $user;
        }
    }

    # Redirect after login
    header('Location: /profile');
    die();
}

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $channel = array( 'service' => NULL, 'stream' => NULL );
    $channel = array_merge($channel, $redis->hgetall('channel:'.$user['name']));
} else {
  header('Location: /destinychat');
  die();
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
    <title>OverRustle - Profile for <?php echo $user['name'] ?></title>
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
  </head>

  <body>

  <nav class="navbar navbar-default navbar-inverse" role="navigation">
    <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <a class="navbar-brand hidden-md hidden-sm" href="/strims">OverRustle</a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li><a href="#"><div id="twitch-ajax"></div></a></li>
          <li><a target="_blank" href="/strims"><div id="server-broadcast"></div></a></li>
          <li class="donate"><a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6TUMKXJ23YGQG"><span>Donate</span></a></li>
        </ul>

        <ul class="nav navbar-nav navbar-right">
          <form action="destinychat" class="navbar-form navbar-left" role="search">
            <div class="form-group">
              <select name="s" class="form-control">
                <?php
                    foreach ($SERVICE_OPTIONS as $key => $value) {
                        echo '<option value="'.$key.'">'.$value.'</option>';
                    }
                ?>
              </select>
              <input type="text" name="stream" type="text" class="form-control" placeholder="Stream/Video ID"/>
              <button type="submit" class="btn btn-default hidden-md hidden-sm">Go</button>
            </div>
          </form>
          <?php include 'user_buttons.php' ?>
        </ul>

      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <h1 align="center" style="color: white;">Profile for: <?php echo $user['name'] ?></h1>
                <h3 style="color: white;">Set Channel: &nbsp;
                    <a href="/channel?user=<?php echo $user['name'] ?>">
                        <span class="label label-default">Visit Channel</span>
                    </a>
                </h3>
                <form action="channel" method="post" role="form">
                    <div class="form-group">
                        <label for="channelService" style="color: white;">Service</label>
                        <select id="channelService" name="service" class="form-control">
                        <?php
                            foreach ($SERVICE_OPTIONS as $key => $value) {
                                echo '<option value="'.$key.'"'.($channel['service'] == $key ? ' selected' : '').'>'.$value.'</option>';
                            }
                        ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="channelStream" style="color: white;">Stream</label>
                        <input id="channelStream" type="text" name="stream" class="form-control" placeholder="Stream/Video ID"
                            value="<?php echo $channel['stream'] ?>" />
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
        </div>
        <div class="col-sm-3">/div>
      </div>
    </div>

  <script src="js/bootstrap.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/js/tab.min.js"></script>
  <script src="js/overrustle.js"></script>
  </body>
</html>