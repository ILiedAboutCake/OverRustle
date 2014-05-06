<?php
$stream = strtolower(trim($_GET['stream']));
$site = strtolower(trim($_GET['site']));

if($stream == "destiny")
{
	header('Location: http://destiny.gg/bigscreen');
}

if($stream == "" && $site == "")
{
	$stream = "kaceytron";
}
?>
<!-- 
	Inspired by Dicedlemming's work on http://dl.dropboxusercontent.com/u/2337991/gameon.html 
	This site is run by ILiedAboutCake in destiny.gg chat. Pls bitch at him with all your problems Kappa.
	Copyright >2014 pls no copypasterino source code without credit
-->
<html>
	<head>
		<title>Destiny.gg chat + <?php echo $stream; ?></title>
		<link rel="stylesheet" href="/lib/bootstrap.min.css">
		<link rel="shortcut icon" href="/favicon.ico" />		
		<style>
		.container-full 
		{
			margin: 0 auto;
			padding: 0;
			width: 100%;
		}
		
		.stream-box
		{
			width: -moz-calc(100% - 390x);
			width: -webkit-calc(100% - 390px);
			width: -o-calc(100% - 390px);
			width: calc(100% - 390px);
		}
		
		.give-header
		{
			height: -moz-calc(100% - 20px);
			height: -webkit-calc(100% - 20px);
			height: -o-calc(100% - 20px);
			height: calc(100% - 20px);
		}
		.header
		{
			width: 100%; 
			background-color: #262626;
			color: #F1F1F1;
		}
		a
		{
			color: #F1F1F1;
		}		
	</style>
	</head>
	<body>
		<div class="container-full">
			  <div class="text-center header">
				Watch Twitch while chatting in <a href="http://destiny.gg/">destiny.gg</a>! 
				<?php
				if ($site == "doge")
				{
					echo "password for doge race is dega";
					
				}
				?>
			  </div>			
			  <div class="text-center pull-left stream-box give-header">
				<?php
				if ($site == "doge")
				{
					echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.ustream.tv/embed/17965583?v=3&amp;wmode=direct" scrolling="no"></iframe>';
				}
				elseif ($site == "")
				{
					echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.twitch.tv/embed?channel=' . $stream . '" scrolling="no"></iframe>';
				}
				?>
			  </div>
			  <div class="text-center pull-right give-header" style="width: 390px;">
					<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://destiny.gg/embed/chat" scrolling="no"></iframe>
			  </div>		  
		</div>
	</body>
</html>