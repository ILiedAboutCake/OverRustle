//start the server
var moment = require('moment');	
var app = require('http').createServer(handler),
	io = require('socket.io').listen(app),
	parser = new require('xml2json'),
	fs = require('fs');

//start of the console logging
console.log(moment().toString() + ": Starting OverRustle.com WebSockets Server...");
	
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

	fs.watchFile(__dirname + '/data.xml', function(curr, prev) {
		//console.log(moment().toString() + ": Pushing update to " + count + " connected clients...");
		fs.readFile(__dirname + '/data.xml', function(err, data) {
			if (err) console.log(moment().toString() + err);
			var json = parser.toJson(data);
			json.time = new Date();
			socket.volatile.emit('notification', json);
		});
	});
	
	socket.on('disconnect', function () {
		count--;
		console.log(moment().toString() + ": Client Disconnected: Concurrent users " + count);
	});
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