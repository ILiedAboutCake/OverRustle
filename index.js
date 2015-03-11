var express = require('express');
var redis = require('redis');
var app = express();

var redis_client = redis.createClient('6379','172.16.5.254'); //I was using the production redis server ripperino
redis_client.select(0); 

//tests redis connection
redis_client.on('connect', function() {
	console.log('Connected to redis!');
});

// test layout of how we should probably format redis users
redis_client.hmset(
	'user:iliedaboutcake', //changable user name
	'stream', '19949118', //stream set from their profile
	'service', 'ustream', //service set from their profile
	'id','30384275', //twitch user ID from OAuth
	'twitchuser', 'iliedaboutcake', //twitch username
	'allowchange', 0, //allows the user to change username if set to 1
	'lastseen',	new Date().toISOString(), //keep track of last seen
	'lastip','127.0.0.1'); //IP address for banning and auditing

app.listen(9001);
app.engine('html', require('ejs').renderFile);
app.set('view engine', 'html');

//handle static content 
app.use("/css", express.static(__dirname + '/css'));
app.use("/js", express.static(__dirname + '/js'));
app.use("/img", express.static(__dirname + '/img'));

app.get ('/strims', function(request, response) {
	//handle the streams listing here
});

app.get ('/:channel', function(request, response) {
	//handle the channel code here, look up the channel in redis
	redis_client.hgetall('user:' + request.params.channel.toLowerCase(), function(err, returned) {
		if (returned) {
			response.render(returned.service, {stream: returned.stream, service: returned.service})
			//response.send(returned.stream + ' - ' + returned.service);
		} else {
			response.send("Channel name was not found.");
		}
	});
});

app.get ('/:service/:stream', function(request, response) {
	//handle normal streaming services here
	response.render('service/' + request.params.service, {stream: request.params.stream, service: request.params.service});
});

app.get ('/profile', function(request, response) {
	//handle profile stuff
});