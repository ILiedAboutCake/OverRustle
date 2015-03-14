var express = require('express');
var redis = require('redis');
var request = require('request');
var app = express();

// server side react js
var browserify = require('browserify'),
    literalify = require('literalify'),
    React = require('react'),
    DOM = React.DOM, body = DOM.body, div = DOM.div, script = DOM.script;
require('react/addons');
    // This is our React component, shared by server and browser thanks to browserify
var App = React.createFactory(require('./js/App'))

var browserified_bundle = browserify()
      .require('./js/App')
      .transform({global: true}, literalify.configure({react: 'window.React'}))
      .bundle()

// end server side react js

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
  'lastseen', new Date().toISOString(), //keep track of last seen
  'lastip','127.0.0.1'); //IP address for banning and auditing

app.listen(8001);

app.set('views',__dirname + '/views');
app.set('view engine', 'ejs');

//handle static content 
app.use("/css", express.static(__dirname + '/css'));
app.use("/js", express.static(__dirname + '/js'));
app.use("/img", express.static(__dirname + '/img'));
app.use("/html", express.static(__dirname + '/html'));
app.use("/fonts", express.static(__dirname + '/fonts'));

var SERVICES = {
  "twitch":{
    display_name: "Twitch",
    chat: true
  },
  "twitch-vod":{
    display_name: "Twitch VOD",
    chat: true
  },
  "ustream":{
    display_name: "Ustream",
    chat: true
  },
  "hitbox":{
    display_name: "hitbox",
    chat: true
  },
  "azubu":{
    display_name: "Azubu",
    chat: true
  },
  "picarto":{
    display_name: "Picarto",
    chat: true
  },
  "castamp":{
    display_name: "CastAMP",
    chat: false
  },
  "nsfw-chaturbate":{
    display_name: "Chaturbate (NSFW)",
    chat: false
  },
  "youtube":{
    display_name: "YouTube",
    chat: false
  },
  "youtube-playlist":{
    display_name: "YouTube (Playlist)",
    chat: false
  },
  "mlg":{
    display_name: "MLG",
    chat: false
  },
  "dailymotion":{
    display_name: "Dailymotion",
    chat: false
  }
}

global.SERVICES = SERVICES
global.SERVICE_NAMES = Object.keys(SERVICES);
// TODO: figure out global variables

app.get('/bundle.js', function (req, res) {
  console.log('getting js bundle')
  res.setHeader('Content-Type', 'text/javascript')
  browserified_bundle.pipe(res)
})

var json_streams = {}

function process_api (api_data) {
  var viewercount = api_data["viewercount"]
  var strims = api_data["streams"]

  var strim_list = []

  for ( var strim in strims ) {
    if ( Object.prototype.hasOwnProperty.call(strims, strim) ) {
      strim_list.push({
        strim: strim,
        viewercount: strims[strim],
        metadata: api_data.metadata[api_data.metaindex[strim]]
      })
    }
  }

  strim_list.sort(function(a,b) {
    // give LIVE streams more weight in sorting higher
    var amulti = 1;
    var bmulti = 1;
    if (amulti*a.viewercount < bmulti*b.viewercount)
       return 1;
    if (amulti*a.viewercount > bmulti*b.viewercount)
      return -1;
    return 0;
  })
  return strim_list
}

request.get({json:true, uri:"http://api.overrustle.com/api"}, function (e, r, resp) {
  if(e)
    return error_callback(e)
  var json = resp
  // api_data.live = r.statusCode < 400 && json.hasOwnProperty('status') && json['status'] !== 404
  //handle the streams listing here
  json_streams = process_api(resp)
})


app.get (['/', '/strims'], function(req, res, next) {
  console.log("/, /strims")
  var props = {
    strim_list: json_streams
  }
  res.render("layout", {page: "streams", streams: json_streams, rendered_streams: React.renderToString(App(props))})
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
  // TODO: redirect to new-style URLS once the API is upgraded
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