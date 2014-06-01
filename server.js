var app = require('http').createServer(handler),
  io = require('socket.io').listen(app),
  parser = new require('xml2json'),
  fs = require('fs');

// creating the server
app.listen(9998);

// on server started we can load our client.html page
function handler(req, res) {
  fs.readFile(__dirname + '/client.html', function(err, data) {
    if (err) {
      console.log(err);
      res.writeHead(500);
      return res.end('Error loading client.html');
    }
    res.writeHead(200);
    res.end(data);
  });
}

// creating a new websocket
io.sockets.on('connection', function(socket) {
  console.log(__dirname);
  fs.watch(__dirname + '/data.xml', function(curr, prev) {
    fs.readFile(__dirname + '/data.xml', function(err, data) {
      if (err) throw err;
      var json = parser.toJson(data);
      json.time = new Date();
      socket.volatile.emit('notification', json);
    });
  });
});
