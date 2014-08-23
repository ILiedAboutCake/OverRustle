<!-- 
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
		<title>Rustlemania</title>
		<link rel="stylesheet" href="/lib/bootstrap.min.css">
		<link rel="shortcut icon" href="/favicon.ico" />
		<script src="socket.io/socket.io.js"></script>
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
		body
		{
    		background-color: #000000; /* BasedGod */
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
			url: "https://api.twitch.tv/kraken/streams/rustlemaniaesports?callback=?",
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
	<script>
    var socket = io.connect('http://overrustle.com:9998');
    socket.on('notification', function (returned) {
        var pushData = JSON.parse(returned);
		
		var pushOutput = "Server Broadcast: " + pushData.info.text + " <a href=\"" + pushData.info.link + " \">See it now!</a>";
        document.getElementById("server-broadcast").innerHTML = pushOutput;
    });
    </script>
	</head>
	<body>
		<div class="container-full">
			<div class="text-center header">
				<div class="pull-left">
					<div id="twitch-ajax">Waiting for Twitch API to respond...</a></div><script>twitchAPI(); window.setInterval(function(){twitchAPI()}, 60000);</script>
				</div>
				<div class="pull-right">
					FrankerZ
				</div>
				<div id="server-broadcast">OverRustle</div>
			</div>			
			<div class="text-center pull-left stream-box give-header">
				<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://www.twitch.tv/embed?channel=rustlemaniaesports" scrolling="no"></iframe>
			</div>
			<div class="text-center pull-right give-header" style="width: 390px;">
				<iframe width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" src="http://destiny.gg/embed/chat" scrolling="no"></iframe>
			</div>
		</div>
	</body>
</html>