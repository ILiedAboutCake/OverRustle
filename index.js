var express = require('express');
var redis = require('redis');
var app = express();

var REDIS_ADDRESS = process.env["REDIS_ADDRESS"] || '172.16.5.254'

var redis_client = redis.createClient('6379',REDIS_ADDRESS); //I was using the production redis server ripperino
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

app.set('views',__dirname + '/views');
app.set('view engine', 'ejs');

//handle static content 
app.use("/css", express.static(__dirname + '/css'));
app.use("/js", express.static(__dirname + '/js'));
app.use("/img", express.static(__dirname + '/img'));
app.use("/html", express.static(__dirname + '/html'));
app.use("/fonts", express.static(__dirname + '/fonts'));

app.get (['/', '/strims'], function(req, res, next) {
	console.log("/, /strims")
	//handle the streams listing here
	res.render("layout", {page: "index"})

});

app.get ('/:channel', function(request, response, next) {
	console.log("/channel")
	//handle the channel code here, look up the channel in redis
	redis_client.hgetall('user:' + request.params.channel.toLowerCase(), function(err, returned) {
		if (returned) {
			res.render("layout", {page: "index", stream: "Live Streams", what: 'best', who: 'me'})

			response.render(returned.service, {stream: returned.stream, service: returned.service})
			//response.send(returned.stream + ' - ' + returned.service);
		} else {
      next();
		}
	});
});

// backwards compatibility:
app.get ('/destinychat', function(req, res, next){
  console.log("/destinychat?s=service&stream=stream")
  res.render("layout", {page: "service", stream: req.query.stream, service: req.query.s})
});

app.get ('/:service/:stream', function(req, res) {
	//handle normal streaming services here
	console.log("/service/channel")
	res.render("layout", {page: "service", stream: req.params.stream, service: req.params.service})
	// res.render('service', {stream: req.params.stream, service: req.params.service});
});

app.get ('/profile', function(request, response) {
	//handle profile stuff
});