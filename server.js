//start the server
var moment = require('moment');
var https = require('https');
var app = require('http').createServer(handler),
	io = require('socket.io').listen(app),
	parser = new require('xml2json'),
	fs = require('fs');

//start of the console logging
console.log(moment().toString() + ": Starting OverRustle.com WebSockets Server...");

//function to update pull a Twitch API
function fetchTwitch(streamer) {
	var array = [];

	https.get('https://api.twitch.tv/kraken/streams/' + streamer, function(response) {
		response.on('data', function(data) {
			returned = JSON.parse(data);
		});

		if(returned.stream == null){
			return 0;
			//setTimeout(fetchTwitch, 60000); // Try again in 1 minute
			//console.log(moment().toString() + ": [info] Twitch API check showed " + streamer + " is not live.");
		} else {
			return 1;
			//setTimeout(fetchTwitch, 600000); // Try again in 15 minutes
			//console.log(moment().toString() + ": [info] Twitch API check showed " + streamer + " is live, pushing data out to clients");
			//array.push({text: streamer + " Has just gone live!", link: "/destinychat?stream=" + streamer, user: "Automated", time: moment()});
			//twitchJson = (JSON.stringify(array));
			//socket.volatile.emit('notification', twitchJson);
		}
	})
}

console.log(moment().toString() + fetchTwitch("taketv"));
process.exit();
	
//same port destiny.gg chat uses
app.listen(9998);
count = 0;

//loads the page itself
function handler(req, res) {
	fs.readFile(__dirname + '/client.html', function(err, data) {
		if (err) {
			console.log(moment().toString() + err);
			res.writeHead(500);
		}
    res.writeHead(200);
    res.end(data);
	});
}

//socket.io fun, sends data to open sockets
io.sockets.on('connection', function(socket) {
	count++;
	console.log(moment().toString() + ": Client Connected: " + socket.handshake.headers.referer + " Concurrent Users: " + count);	
	
	//code that handles manual updates
	fs.watchFile(__dirname + '/data.xml', function(curr, prev) {
		fs.readFile(__dirname + '/data.xml', function(err, data) {
			if (err) console.log(moment().toString() + err);
			var json = parser.toJson(data);
			json.time = new Date();
			socket.volatile.emit('notification', json);
		});
	});
	
	//disconnect logging
	socket.on('disconnect', function () {
		count--;
		console.log(moment().toString() + ": Client Disconnected: Concurrent users " + count);
	});
});

//Updates the console the manual update file was updated
fs.watchFile(__dirname + '/data.xml', function(curr, prev) {
	console.log(moment().toString() + ": Pushing manual update to " + count + " connected clients...");
});

//handle the script closing
process.stdin.resume();
process.on('SIGINT', function () {
	console.log(moment().toString() + ": Server caught CTRL+C/SIGINT... Exiting.");
	process.exit();
});

//catch any uncaught errors and dump the stack
process.on('uncaughtException', function () {
	console.log(moment().toString() + ": Server encountered an uncaught exception... Exiting and dumping error stack.");
	console.log(moment().toString() + err.stack);
	process.exit();
});