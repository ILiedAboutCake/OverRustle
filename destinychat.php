<?php
$stream = strip_tags(trim($_GET['stream']));
$s = strip_tags(strtolower(trim($_GET['s'])));
$t = strip_tags(strtolower(trim($_GET['t'])));

//what the fuck, go use destiny's website for this FeedNathan
if(strtolower($stream) == "destiny")
{
	header('Location: http://destiny.gg/bigscreen');
}

//set the default stream time to twitch
if($s == "")
{
	$s = "twitch";
}

//set the page default
if($stream == "" && $s == "twitch" && $t == "")
{
	$stream = "kaceytron";
}

//if no time is set start from the beginning
if($t == "")
{
	$t = "0";
}
?>
<!-- 
	GitHub can be found at https://github.com/ILiedAboutCake/OverRustle
	This site is run by ILiedAboutCake in destiny.gg chat. Pls bitch at him with all your problems Kappa.
	Copyright >2014 pls no copypasterino source code without credit


                                      WellRight                                         
                                  ToBearArmsLOL,hon                                     
                              estly,IthinkImighttalkto                                  
                      Stevenaboutyour           oddrheto                                
                   ric.Thischati                  sdefini                               
                 telyanintegralp                   arttoh                               
                 isstreamandyouar                   esull                               
                 yingitbytel lingme    andmanyothe  rstok                               
                 illthemselves.You'v egotsomemajorpr oble                               
                 msandIworrytheyar  enevergoingtobesolved                               
                ifyou  loiterhere,  lettingotherpeople'ss                               
               adnessandderisioninc ulcateyouro wnbittern                               
              ess.Becausenewsflas   h!:NoonelikesyouWellR                               
             ightToBearArmsLOL,honestly,IthinkImi  ghtta                                
            lktoS          tevenaboutyouroddr     hetori                                
           c.Thi                      schatis     defini                                
          telyan                                 integr                                 
         alpart                                 tohiss                                  
        treama                                  ndyoua                                  
        resul                      lyin        gitbyt                                   
        elli                      ngmea ndm   anyoth                                    
        erst                      okillthems  elves                         .You'vego   
       tsome                      majorprob  lemsa                        ndIworrythey  
       arene                     vergoingto besol                       vedify    oulo  
       iterh                     ere,letti  ngoth                     erpeopl    e'ssa  
       dness                    andderisi  onincu                   lcateyo     urown   
       bitte                    rness.Be   causenewsflash!:Noon   elikesy     ouWel     
        lRig                   htToBear    ArmsLOL,honestly,IthinkImigh      ttalk      
        toSt                   evenabo     utyou   roddr   hetoric.Th      ischat       
        isde                  finitely      ani   ntegralparttohiss      treama         
        ndyou               aresu llyin         gitbytellingmeandm     anyothe          
         rsto             killt  hemselv         es.You'vegotsomemaj   orproble         
         msand            Iworrytheyaren                     evergoin    gtobesolv      
          edify            ouloiterhere              ,let       tingot  herp eople      
          'ssadn              essa                   ndde        rision  inculcat       
           eyouro                                wnb              itter    ness         
            .Because                            news              flash     !:No        
               onelike                          syou              WellRightToBea        
     rAr        msLOL,hone                       stly           ,IthinkImightta         
    lktoSte    venaboutyouroddrh                  eto         ric.Thi    s              
    chatisdefinite lyanintegralparttohis           stre    amandyo                      
    uare sullyingitbyte    llingmeandmanyo therstokillthemselves                        
     .You  'vegotsome         majorproble msandIworrytheyaren                           
      ever   goingt         obesolvedify ouloi terhere,let                              
       tingotherp           eople'ssadn  essa                                           
        ndderis              ionincul   cate                                            
          you                rownbi    tter                                             
                              ness.B  ecau                                              
                               senewsflas                                               
                                 h!:Noon                                                
                                   eli                                                
-->
<html>
	<head>
		<title>Destiny.gg chat + <?php echo $stream; ?></title>
		<link rel="search" href="/opensearch.xml" type="application/opensearchdescription+xml" title="OverRustle.com" />
		<link rel="stylesheet" href="/lib/bootstrap.min.css">
		<link rel="shortcut icon" href="/favicon.ico" />	
		<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
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
			height: -moz-calc(100% - 30px);
			height: -webkit-calc(100% - 30px);
			height: -o-calc(100% -30px);
			height: calc(100% - 30px);
		}
		.header
		{
			width: 100%;
			height: 30px;
			background-color: #262626;
			color: #F1F1F1;
			line-height: 30px;
		}
		a
		{
			color: #F1F1F1;
		}		
		select, input
		{
			color: black;
		}
		input, form
		{
			margin: 0 auto;
			padding: 0;	
		}
		input 
		{
			line-height: 15px;
		}
	</style>
	<!-- Google Analytics, Don't get rustled I don't harvest IPs or anything -->
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-49711133-1', 'overrustle.com');
	  ga('send', 'pageview');
	</script>
	<!-- ajax code to pull down viewer numbers -->
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
				var output = apiData.stream.channel.display_name + " playing " + apiData.stream.game + " with " + apiData.stream.viewers + " viewers";
				document.getElementById("twitch-ajax").innerHTML = output;
			}
		});
	} 
    </script>
	</head>
	<body>
		<div class="container-full">
			<div class="text-center header">
				<div class="pull-left">
				<?php
				if ($s == "twitch")
				{

					echo '<div id="twitch-ajax">Waiting for Twitch API to respond...</a></div><script>twitchAPI(); window.setInterval(function(){twitchAPI()}, 600000);</script>';
				}
				else
				{
					echo 'Watch videos while chatting in <a href="http://destiny.gg/">destiny.gg</a>!';
				}
				?>
				</div>
				<div class="pull-right">
					<form action="destinychat">
					Player:
					<select name="s">
						<option value="twitch">Twitch</option>
						<option value="twitch-hls">Twitch - HTML5</option>
						<option value="twitch-vod">Twitch - VOD</option>
						<option value="justin">Justin.TV</option>
						<option value="hitbox">Hitbox</option>						
						<option value="youtube">Youtube</option>
						<option value="mlg">MLG (Beta*)</option>
						<option value="ustream">Ustream (Beta*)</option>
						<option value="dailymotion">Dailymotion</option>
					</select>
					
					Stream/ID:
					<input type="text" name="stream" /> 
					</form>
				</div>
			</div>			
			<div class="text-center pull-left stream-box give-header">
			<?php
			switch($s) 
			{
				case "twitch":
					echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.twitch.tv/embed?channel=' . $stream . '" scrolling="no"></iframe>';
					break;
					
				case "twitch-hls":
					echo '<iframe id="player" type="text/html" width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.twitch.tv/' . $stream . '/hls" scrolling="no"></iframe>';
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
					
				case "justin":
					echo '<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.justin.tv/swflibs/JustinPlayer.swf?channel=' . $stream . '" scrolling="no"></iframe>';
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
			}
			?>
			</div>
			<div class="text-center pull-right give-header" style="width: 390px;">
				<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://destiny.gg/embed/chat" scrolling="no"></iframe>
			</div>
		</div>
	</body>
</html>