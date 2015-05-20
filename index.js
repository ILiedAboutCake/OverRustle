// load config
var jf = require("jsonfile");
var fs = require("fs");
var CONFIG = fs.existsSync('./config.json') ? jf.readFileSync('config.json') : {};
var SAMPLE_CONFIG = jf.readFileSync('config.sample.json')
// inject data from ENV if it exists
// get ENV variables with corresponding names,
// if they exist, override the files data
for ( var config_key in SAMPLE_CONFIG ) {
  if(!CONFIG.hasOwnProperty(config_key)){
    CONFIG[config_key] = process.env[config_key.toUpperCase()]
  }
}

var express = require('express');
var favicon = require('serve-favicon');
var redis = require('redis');
var request = require('request');
var bodyParser = require('body-parser')
 
var app = express()
// parse application/x-www-form-urlencoded 
app.use(bodyParser.urlencoded({ extended: false }))
// parse application/json 
app.use(bodyParser.json())

var constants = require("./jsx/constants.js");

// redis setup
/////////////////
try{
  // used for the debug/staging/alpha server
  function makeRedisClient (redis_db) {
    var rv = null
    if(process.env.REDISTOGO_URL){
      var rtg   = require("url").parse(process.env.REDISTOGO_URL);
      rv = redis.createClient(rtg.port, rtg.hostname);
      rv.auth(rtg.auth.split(":")[1]);
    }else{
      rv = redis.createClient('6379',CONFIG['redis_address']);
    }
    rv.select(redis_db); 

    rv.on('connect', function() {
      console.log('Connected to redis#'+redis_db.toString());
    });

    return rv
  }

  var redis_client = makeRedisClient(0);
  var legacy_redis_client = makeRedisClient(1);

}catch (e){
  // in case redis doesn't exist
  console.log(e)
}

var session = require('express-session');
var RedisStore = require('connect-redis')(session);

// TODO: change settings if we want to handle secure cookies explicitly
// https://github.com/expressjs/session#cookie-options
// if (app.get('env') === 'production') {
//   app.set('trust proxy', 1) // trust first proxy
//   sess.cookie.secure = true // serve secure cookies
// }
app.use(session({
  store: new RedisStore({
    "client": redis_client
  }),
  // cookie: { maxAge: 60000*60*24*30 }
  secret: CONFIG['session_secret'],
  resave: false,
  saveUninitialized: false
}));


redis_client.exists('twitchuser:iliedaboutcake', function (e, r) {
  if(r == 1){
    console.log("no need to add the default user, redis says ", r)
    return
  }
  console.log("adding the default user to redis")
// test layout of how we should probably format redis users
redis_client.hmset(
  'user:dank_memester', //change able overrustle user name
  'overrustle_username', 'dank_memester',
  'stream', '19949118', //stream set from their profile
  'service', 'ustream', //service set from their profile
  'twitch_user_id','30384275', //twitch user ID from OAuth
  'twitchuser', 'iliedaboutcake', //twitch username
  'admin', 'true',
  'allowchange', 'false', //allows the user to change username if set to 1
  'lastseen', new Date().toISOString(), //keep track of last seen
  'lastip','127.0.0.1'); //IP address for banning and auditing

// maintain in index of twitchuser -> overrustle_username
redis_client.set(
  'twitchuser:iliedaboutcake', 
  'dank_memester' //change able overrustle user name
  ); 
})

/////////////////

app.listen(CONFIG['port']);

app.set('views',__dirname + '/views');
app.set('view engine', 'ejs');

//handle static content 
app.use("/css", express.static(__dirname + '/css'));
app.use("/js", express.static(__dirname + '/js'));
app.use("/img", express.static(__dirname + '/img'));
app.use("/html", express.static(__dirname + '/html'));
app.use("/fonts", express.static(__dirname + '/fonts'));
app.use(favicon(__dirname + '/public/favicon.ico'));

app.use(function (req, res, next) {
  // console.log("middleware setting current user")
  console.log(Date.now(), req.method, req.originalUrl);
  res.locals.current_user = req.session.user;
  next();
});

app.use(function (req, res, next) {
  // console.log("middleware popping notices")
  res.locals.notice = noticePop(req)
  next()
})


global.SERVICES = constants.SERVICES;
global.SERVICE_NAMES = Object.keys(constants.SERVICES);
global.TWITCH_OAUTH_URL = 'https://api.twitch.tv/kraken/oauth2/authorize?response_type=code' +
  '&client_id=' + CONFIG['twitch_client_id'] + 
  '&redirect_uri=' + CONFIG['twitch_redirect_uri'] +
  '&scope=user_read';

// This is our React component, shared by server and browser thanks to browserify
/////////////////
// server side react js
var React = require('react');
var addons = require('react-addons');
var App = React.createFactory(require('./js/App'))

// cache the stream list from the API
// so that the HTML we serve on first load is fresh
/////////////////

var static_api_data = {}

function getApiData(){
  request.get({json:true, uri:"http://api.overrustle.com/api"}, function (e, r, resp) {
    if(e){
      console.log("error getting new data", JSON.stringify(e))
      return e
    }
    var json = resp
    // api_data.live = r.statusCode < 400 && json.hasOwnProperty('status') && json['status'] !== 404
    //handle the streams listing here
    static_api_data = json
  })
}

getApiData()
var apiRefresher = setInterval(getApiData, 2000)


// For the Future:
// This will require a rewrite on the server side to implement correctly
// var socket = require('socket.io-client')('http://api.overrustle.com/streams');
// socket.on('connect', function(){
//   console.log("Connected SOCKET")
//   // we cannot infer this from the referrer because <------------ IMPORTANT
//   // there is no way to set a referrer with this client <-------- IMPORTANT
//   socket.emit("idle", {"/strims"})
// });
// socket.on('strims', function(api_data){
//   console.log(api_data)
// });
// socket.on('disconnect', function(){
//   console.log("DISCONNECTED SOCKET")
// });

/////////////////

app.get (['/', '/strims', '/streams'], function(req, res, next) {

  // trying out sessions
  // var sess = req.session;
  // if (!sess.views) {
  //   sess.views = 0
  // }
  // sess.views = sess.views + 1;

  var props = {
    api_data: static_api_data
  }
  var page_title = Object.keys(static_api_data.streams).length + " Live Streams viewed by " + static_api_data.viewercount + " rustlers"
  res.render("layout", {
    page: "streams",
    page_title: page_title,
    react_props: props, 
    rendered_streams: React.renderToString(App(props))    
  })
});

app.post("/channel", function(req, res, next){
})

app.get ('/profile', function(req, res, next) {
  if (req.session.user) {
    // clear out notices
    res.render("layout", {
      page: "profile", 
      page_title: "Profile for "+req.session.user.overrustle_username
    })
  }else{
    res.redirect('/')
  }
})

//THIS DOES NOT WORK

app.use('/admin*', function(req, res, next){
  if(req.session.user && req.session.user.admin == "true"){
    next()
  }else{
    console.log("nonadmin user:", req.session.user)
    noticeAdd(req, {"danger": "You are not allowed to access that page"})
    res.redirect('/')
  }
})

var Promise = require('bluebird')

app.get ('/admin', function(req, res, next) {  
   //get all the users
  var site_users = []

  new Promise(function (resolve, reject) {
    redis_client.keys('user:*', function (err, keys) {
      if (err) reject(err);
      resolve(keys);
    });
  })
  .then(function (keys) {
    return Promise.all(keys.map(function (key) {
      return new Promise(function (resolve, reject) {
        redis_client.hgetall(key, function (err, res) {
          if (err) reject(err);
          site_users.push(res);
          resolve();
        });
      });
    }));
  })
  .then(function () {
    // console.log(site_users);
    res.render("layout", {
      page: "admin",
      page_title: "Admin Signed in as " + req.session.user.overrustle_username,
      users: site_users
    })
  });
})

//get all of dat dirty NSA info from their profile
app.get ('/admin/:overrustle_username', function(req, res, next) {
  redis_client.hgetall("user:"+req.params.overrustle_username, function(err, resp) {
    user_info = resp

    res.render("layout", {
      page: "adminuser", 
      page_title: "Editing: " + user_info.overrustle_username,
      user: user_info
    })
  });
})

// edit a username -- admin editing, or user editing
app.post('/profile/:original_overrustle_username', function (req, res, next) {
  var current_user = req.session.user;
  var original_username = req.params.original_overrustle_username;
  
  console.log("current_user:", current_user)
  console.log('editing user:', 'original_username', req.params, 'new_data', req.body)

  if(current_user.admin != "true" && current_user.overrustle_username != original_username){
    noticeAdd(req, {"danger":"You're not allowed to edit "+original_username+"\'s channel"})
    return res.redirect('/')
  }

  var new_username = req.body.overrustle_username

  new Promise(function (resolve, reject){
    redis_client.hgetall("user:"+original_username, function(err, resp) {
      if (err) reject(err);
      resolve(resp)
    })
  }).then(function (user){
    // merge in new user settings
    return new Promise(function (resolve, reject){
      user.service = req.body.service
      user.stream = req.body.stream

      // only an admin can grant name changes
      if(current_user.admin === "true"){
        user.allowchange = req.body.allowchange
      }else{
        console.log("NOPE you cant give yourself a name change")
      }

      // figure out if you're allowed to change your name
      var canChange = (current_user.admin === "true" || user.allowchange === "true") && new_username.length > 0
      if (!canChange && original_username != new_username) {
        // trying to change your name despite being unable to
        noticeAdd(req, {"warning": "You can\'t change your overustle.com username more than once. Ask ILiedAboutCake or hephaestus for a name change."})
        return resolve(user)
      }
      // if we're not changing the name, don't bother 
      // checking for duplicates
      if(new_username == original_username){
        return resolve(user)
      }
      redis_client.exists("user:"+new_username, function (err, rsp) {
        var does_exist = rsp == 1
        if(does_exist){
          noticeAdd(req, {"danger":"You\'re Not allowed to change your name to an existing user\'s name: "+new_username})
        }else{
          user.overrustle_username = new_username
        }
        // only allow 1 name change
        if(current_user.admin !== "true"){
          console.log("not an admin, so no more name changes")
          user.allowchange = "false"
        }
        resolve(user)
      })
    })
  }).then(function (user){
    // set the twitch -> overrustle index
    return new Promise(function (resolve, reject){
      redis_client.set(
        "twitchuser:"+user.twitchuser,
        user.overrustle_username,
        function (err, reply) {
          if(err) reject(err);
          resolve(user)
        }
      )
    })
  }).then(function (user){
    return new Promise(function (resolve, reject){
      redis_client.hmset('user:'+user.overrustle_username, user, function(err, result){
        if(err){
          return console.error(err)
        }
        noticeAdd(req, {"success": user.overrustle_username+"\'s profile updated sucessfully!"})
        if(req.session.user.overrustle_username === original_username){
          req.session.user = user
        }else{
          console.log("DERP not setting an admin's session to another persons")
        }
        resolve(user)
      })
    })
  }).then(function (user) {
    // if we made a successful name change
    // clean out the old record
    return new Promise(function (resolve, reject){
      if (user.overrustle_username == original_username) {
        return resolve(user)
      }
      redis_client.del('user:'+original_username, function(err, resp){
        if(err){
          console.error(err)
        }
        resolve(user)
      })
    })
  }).then(function (user) {
    if(req.session.user.admin === 'true'){
      res.redirect('/admin/'+user.overrustle_username)
    }else{
      res.redirect('/profile')      
    }
  })
})

// twitch will send oauth requests 
// that use our client_id to this path
app.get("/oauth/twitch", function(req, res, next){
  if(req.query.error){
    noticeAdd(req, {"danger":"Twitch Login did not work. Twitch says: "+req.query.error+": "+req.query.error_description})
    return res.redirect('/')
  }
  if(!req.query.code){
    return next()
  }
  
  request.post({
    url: constants["TWITCH_TOKEN_URL"],
    form: {
      client_id: CONFIG['twitch_client_id'],
      client_secret: CONFIG['twitch_client_secret'],
      grant_type: "authorization_code",
      redirect_uri: CONFIG['twitch_redirect_uri'],
      code: req.query.code
    },
    json: true
  }, function (e, r, body) {
    if(e){
      console.log("error getting access_token from twitch", JSON.stringify(e))
      return e
    }
    var jt = body;
    console.log('twitch token body', body)
    // {
    //   "access_token": "[user access token]",
    //   "scope":[array of requested scopes]
    // }
    var twitch_user_url = "https://api.twitch.tv/kraken/user?oauth_token="+jt['access_token'];

    request.get({
      json:true, 
      uri:twitch_user_url
    }, function (e, r, resp) {
      if(e){
        console.log("error looking up user data using access_token", JSON.stringify(e))
        return e
      }
      var json = resp
      if(json['status'] >= 300){
        console.log()
      }

      // use the twitch -> overrustle index to find the right username
      redis_client.get("twitchuser:"+json['name'], function(e, reply) {
        if(e){
          console.log("error getting new data", e)
          return e
        }
        // reply is null when the key is missing
        var new_settings = {}
        new_settings['lastseen'] = new Date().toISOString();
        new_settings['lastip'] = req.headers.hasOwnProperty('x-forwarded-for') ? req.headers['x-forwarded-for'] : req.connection.remoteAddress
        var overrustle_username = reply;
        if(overrustle_username == null){
          overrustle_username = json['name'];
          // ensure the index is consistent
          redis_client.set('twitchuser:'+overrustle_username, overrustle_username, redis.print)

          new_settings['overrustle_username'] = overrustle_username
          new_settings['twitchuser'] = json["name"]
          new_settings['stream'] = json["name"]
          new_settings['service'] = "twitch"
          new_settings['twitch_user_id'] = json['_id']
          // TODO: decide if we want to 
          // allow new users to change their overrustle_username
          new_settings['allowchange'] = "true"
          new_settings['admin'] = "false"
        }
        
        console.log(reply, new_settings);
        redis_client.hmset(
          'user:'+overrustle_username,
          new_settings, 
        function(err, result){
          redis_client.hgetall('user:'+overrustle_username, function(err, returned) {
            if(err){
              return next(err)
            }
            req.session.user = returned;
            noticeAdd(req, {"success":"Congrats "+overrustle_username+", You are now logged into OverRustle.com"})
            res.redirect('/profile')
          });
        }); 
      });
    });
  });
});

app.get('/logout', function (req, res, next) {
  req.session.user = undefined
  noticeAdd(req, {"success":"You logged out sucessfully!"})
  res.redirect('/')
})


// WARNING: if you get an ADVANCED stream with 
// hashbangs in the URL they won't get past the form
app.get (['/destinychat', '/:service/:stream'], function(req, res, next) {
  //handle normal streaming services here  
  // backwards compatibility:
  // LEGACY SUPPORT
  // DELETE WHEN DESIRED
  if(req.query.hasOwnProperty("s")){
    req.params.service = req.query.s
  }
  if(req.query.hasOwnProperty("stream")){
    req.params.stream = req.query.stream
  }

  if (req.params.service && global.SERVICE_NAMES.indexOf(req.params.service) !== -1) {
    validateBanned(req.params.stream, req, res, function (err) {
      // console.log("Good Validation!")
      res.render("layout", {
        page: "service", 
        page_title: req.params.stream + ' on ' + req.params.service,
        stream: req.params.stream, 
        service: req.params.service
      })
    })
  }else if (req.params.service || req.params.stream){
    console.log('bad channel')
    console.log(req.params.service, 'not in:')
    console.log(global.SERVICE_NAMES)

    noticeAdd(req, {"danger": "You visited /destinychat without specifying a stream or service"})
    return res.redirect('/')
  }else{  
    var props = {
      api_data: static_api_data
    }
    var page_title = "Destiny.gg chat with " + Object.keys(static_api_data.streams).length + " Live Streams viewed by " + static_api_data.viewercount + " rustlers"
    res.render("layout", {
      page: "destinychat",
      page_title: page_title,
      react_props: props, 
      rendered_streams: React.renderToString(App(props))    
    })
  }
});

app.get (['/channel', '/:channel'], function(req, res, next) {
  var channel = null

  // NOTE: channel will be undefined when on /channel specifically
  // req.params still contains a key for 'channel'
  // but it points to undefined
  if(req.params.channel){
    channel = req.params.channel.toLowerCase()
  }

  // LEGACY SUPPORT DELETE THIS EVENTUALLY
  if(channel == null && req.query.hasOwnProperty('user')){
    channel = req.query.user.toLowerCase()
  }

  if(channel == null){
    console.log('no channel specified')
    noticeAdd(req, {"danger": "You visited /channel without specifying a channel"})
    return res.redirect('/')
  }

  //handle the channel code here, look up the channel in redis
  redis_client.hgetall('user:' + channel, function(err, returned) {
    if (returned) {
      validateBanned(returned.stream, req, res, function (err) {
        res.render("layout", {
          page: "service", 
          page_title: channel + " via " + returned.stream + " on " + returned.service,
          stream: returned.stream, 
          service: returned.service
        })
      })
    } else {
      // DELETE THIS
      // support legacy channels as long as we feel like
      legacy_redis_client.hgetall('channel:' + channel, function(lerr, lreturned){
        if(lreturned){
          res.render("layout", {
            page: "service", 
            page_title: channel + " via " + lreturned.stream + " on " + lreturned.service,
            stream: lreturned.stream, 
            service: lreturned.service
          })
        }else{
          console.log('no channel found for', channel)
          next();          
        }
      })
    }
  })
})

function validateBanned (stream, req, res, cb) {
  redis_client.hmget('banlist', stream, function (berr, breturnedarr) {
    if(berr){
      return next(berr)
    }
    var breturned = breturnedarr[0] 
    if (breturned) {
      console.log('got isBanned', breturned)
      noticeAdd(req, {"danger": stream+" is banned. "+breturned})
      res.redirect('/banned')
    }else{
      cb(berr)
    }
  })
}


// move to notice.js
function noticeAdd(req, obj){
  req.session.notice = req.session.notice ? req.session.notice : []
  req.session.notice.push(obj)
}

function noticePop(req){
  // console.log("flushing notices!")
  var tmpnotice = req.session.notice
  req.session.notice = undefined
  return tmpnotice
}