<?php

$SERVICE_OPTIONS = array(
    "twitch" => "Twitch",
    "twitch-vod" => "Twitch - VOD",
    "riven" => "Riven - Destiny VOD",
    "hitbox" => "Hitbox",
    "castamp" => "CastAmp",
    "nsfw-chaturbate" => "Chaturbate (NSFW)",
    "youtube" => "Youtube",
    "youtube-playlist" => "Youtube (Playlist)",
    "mlg" => "MLG (Beta*)",
    "ustream" => "Ustream (Beta*)",
    "dailymotion" => "Dailymotion",
    "azubu" => "Azubu",
    "picarto" => "Picarto",
    "advanced" => "Advanced"
);

?>
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
        <?php
        if (basename($_SERVER['PHP_SELF'], '.php') == 'channel') {
            if ($config['chat_enable']) {
                $otherchat = 'Channel';
            }
        } elseif (!empty($s) && in_array($s, array('twitch', 'hitbox', 'ustream', 'azubu'))) {
          $otherchat = ucfirst($s);
        }

        if (!empty($otherchat))
        {
          echo '<li class="active hidden-md hidden-sm"><a href="#destinychat" role="tab" data-toggle="tab">Destiny Chat</a></li>';
          echo '<li class="hidden-md hidden-sm"><a href="#otherchat" role="tab" data-toggle="tab"> ' . $otherchat  . ' Chat</a></li>';
        }
        ?>
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
        <li>
        <div class="btn-group">

        <?php

        require_once 'config.php';

        if (isset($_SESSION['user'])) { ?>
            <a href="/profile">
                <button class="btn btn-default navbar-btn" title="Profile">
                    <span class="glyphicon glyphicon-user"></span>
                </button>
            </a>
            <a href="/logout">
                <button class="btn btn-default navbar-btn" title="Log Out">
                    <span class="glyphicon glyphicon-off"></span>
                </button>
            </a>
        <?php
        } else {
            $twitch_url = 'https://api.twitch.tv/kraken/oauth2/authorize?response_type=code'
                . '&client_id='.$config['twitch_client_id']
                . '&redirect_uri='.$config['twitch_redirect_uri']
                . '&scope=user_read';
        ?>
            <a href="<?php echo $twitch_url ?>">
                <button class="btn btn-default navbar-btn" title="Log In with Twitch">
                    <span>Log In</span>
                </button>
            </a>
        <?php
        }

        ?>

        </div>
        </li>
      </ul>

    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
