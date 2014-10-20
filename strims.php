
<META HTTP-EQUIV="refresh" CONTENT="60">
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>OverRustle.com - Viewers</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/overrustle.css" rel="stylesheet">
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="js/ie-emulation-modes-warning.js"></script>
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
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
              <option value="twitch">Twitch</option>
              <option value="twitch-vod">Twitch - VOD</option>
              <option value="hitbox">Hitbox</option>
              <option value="castamp">CastAmp</option>            
              <option value="youtube">Youtube</option>
              <option value="mlg">MLG (Beta*)</option>
              <option value="ustream">Ustream (Beta*)</option>
              <option value="dailymotion">Dailymotion</option>
              <option value="azubu">Azubu</option>
              <option value="picarto">Picarto</option>
              <option value="advanced">Advanced</option>
            </select>
            <input type="text" name="stream" type="text" class="form-control" placeholder="Stream/Video ID"/> 
            <button type="submit" class="btn btn-default hidden-md hidden-sm">Go</button>
          </div>
        </form>
      </ul>

    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<?php include('strims_content.php'); ?>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="../../dist/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
