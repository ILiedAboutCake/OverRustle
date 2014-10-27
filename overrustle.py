import threading
import tornado.httpserver
import tornado.websocket
import tornado.ioloop
import tornado.web
import tornadoredis
import tornado.gen
import redis
import json
import socket
import time
import datetime
import random
import uuid

#dem variables
# redis
c = tornadoredis.Client()
c.connect()

r = redis.StrictRedis()

strims = {}
clients = {}
ping_every = 15

def strimCounts():
	strims = r.hgetall('strims') or []
	counts = {}
	for strim in strims:
		counts[strim] = strims[strim]
	return counts

def numClients():
	return r.hlen('clients')

#takes care of updating console
def printStatus():
	threading.Timer(240, printStatus).start()
	print 'Currently connected clients: ' + str(numClients())
	sweepClients()
	sweepStreams()
	strim_counts = strimCounts()
	for key, value in strim_counts.items():
		print key, value

@tornado.gen.engine
def sweepClients():
	global ping_every
	clients = yield tornado.gen.Task(c.hkeys, 'clients')
	to_remove = []
	expire_time = (time.time()-(5*ping_every))
	for client_id in clients:
		# client = clients[client_id]
		lpt = yield tornado.gen.Task(c.hget, 'last_pong_time', client_id)
		if (((lpt == '') or (lpt == None)) or (float(lpt) < expire_time)):
			# if(("last_pong_time" in client) and (client["last_pong_time"] < (t_now-(5*ping_every)))):
			to_remove.append(client_id)
	for client_id in to_remove:
		remove_viewer(client_id)

@tornado.gen.engine
def sweepStreams():
	strims = yield tornado.gen.Task(c.hgetall, 'strims')
	if isinstance(strims, int):
		print 'got', strims, 'instead of actual strims'
		return
	to_remove = []
	for strim in strims:
		if(strims(strim) <= 0):
			to_remove.append(strim)
	for strim in to_remove:
		res = yield tornado.gen.Task(c.hdel, 'strims', strim)

@tornado.gen.engine
def remove_viewer(v_id):
	global c
	strim = yield tornado.gen.Task(c.hget, 'clients', v_id)
	if strim != '':
		res = yield tornado.gen.Task(c.hincrby, 'strims', strim, -1)
	else:
		print 'deleting strim-less vid:', v_id
	res = yield tornado.gen.Task(c.hdel, 'clients', v_id)
	res = yield tornado.gen.Task(c.hdel, 'last_pong_time', v_id)
	# global clients
	# global strims
	# if (v_id in clients):
	# 	if ('strim' in clients[v_id]) and (clients[v_id]['strim'] in strims):
	# 		strims[clients[v_id]['strim']].pop(v_id, None)
	# 	clients.pop(v_id, None)
	print str(numClients()) + " remain connected"

#ayy lmao
#if self.is_enlightened_by(self.intelligence):
#	self.is_euphoric = True

#Stat tracking websocket server
#Hiring PHP developers does not contribute to the quota of employees with disabilities.
class WSHandler(tornado.websocket.WebSocketHandler):

	def __init__(self, application, request, **kwargs):
		tornado.websocket.WebSocketHandler.__init__(self, application, request, **kwargs)
		self.client = tornadoredis.Client()
		self.client.connect()
		self.io_loop = tornado.ioloop.IOLoop.instance()

	def check_origin(self, origin):
		return True

	@tornado.gen.engine
	def open(self):
		global clients
		self.id = str(uuid.uuid4())
		print 'Opened Websocket connection: (' + self.request.remote_ip + ') ' + socket.getfqdn(self.request.remote_ip) + " id: " + self.id
		clients[self.id] = {'id': self.id}
		res = yield tornado.gen.Task(self.client.hset, 'clients', self.id, '')
		len_clients = yield tornado.gen.Task(self.client.hlen, 'clients')
		print len_clients
		res = yield tornado.gen.Task(self.client.hset, 'last_pong_time', self.id, time.time())
		# Ping to make sure the agent is alive.
		self.io_loop.add_timeout(datetime.timedelta(seconds=(ping_every/3)), self.send_ping)
	
	@tornado.gen.engine
	def on_connection_timeout(self):
		print "-- Client timed out after 1 minute"
		# this might be redundant and redundant
		self.on_close()
		self.close()

	@tornado.gen.engine
	def send_ping(self):

		print("<- [PING] " + self.id)
		try:
			self.ping(self.id)
			# global ping_every
			self.ping_timeout = self.io_loop.add_timeout(datetime.timedelta(seconds=ping_every), self.on_connection_timeout)
		except Exception as ex:
			print("-- Failed to send ping! to: "+ self.id + " because of " + repr(ex))
			self.on_close()
		
	@tornado.gen.engine
	def on_pong(self, data):
		# We received a pong, remove the timeout so that we don't
		# kill the connection.
		print("-> [PONG] %s" % data)

		if hasattr(self, "ping_timeout"):
			self.io_loop.remove_timeout(self.ping_timeout)

		in_clients = yield tornado.gen.Task(c.hexists, 'clients', self.id)
		if in_clients:
			res = yield tornado.gen.Task(c.hset, 'last_pong_time', self.id, time.time())
			# Wait some seconds before pinging again.
			global ping_every
			self.io_loop.add_timeout(datetime.timedelta(seconds=ping_every), self.send_ping)

	@tornado.gen.engine
	def on_message(self, message):
		global strims
		global numClients
		global clients
		fromClient = json.loads(message)

		if fromClient[u'strim'] == "/destinychat?s=strims&stream=":
			fromClient[u'strim'] = "/destinychat"

		#handle session counting - This is a fucking mess :^(
		if fromClient[u'action'] == "join":
			res = yield tornado.gen.Task(self.client.hset, 'clients', self.id, fromClient[u'strim'])
			strim_count = yield tornado.gen.Task(self.client.hincrby, 'strims', fromClient[u'strim'], 1)
			self.write_message(str(strim_count) + " OverRustle.com Viewers")
			print 'User Connected: Watching %s' % (fromClient[u'strim'])

		elif fromClient[u'action'] == "unjoin":
			print 'User Disconnected: Was Watching %s' % (fromClient[u'strim'])
			self.on_close()

		elif fromClient[u'action'] == "viewerCount":
			strim_count = yield tornado.gen.Task(self.client.hget, 'strims', fromClient[u'strim'])
			self.write_message(str(strim_count) + " OverRustle.com Viewers")

		elif fromClient[u'action'] == "api":
			self.write_message(json.dumps({"streams":strimCounts(), "totalviewers":numClients}))

		else:
			print 'WTF: Client sent unknown command >:( %s' % (fromClient[u'action'])


		#remove the dict key if nobody is watching DaFeels
		strim_count = yield tornado.gen.Task(self.client.hget, 'strims', fromClient[u'strim'])
		if strim_count <= 0:
			res = yield tornado.gen.Task(c.hdel, 'strims', fromClient[u'strim'])

	@tornado.gen.engine
	def on_close(self):
		print 'Closed Websocket connection: (' + self.request.remote_ip + ') ' + socket.getfqdn(self.request.remote_ip)+ " id: "+self.id
		global remove_viewer
		remove_viewer(self.id)

#print console updates
printStatus()

#JSON api server
class APIHandler(tornado.web.RequestHandler):
		def get(self):
				self.write(json.dumps({"streams":strimCounts(), "totalviewers":numClients()}))

#GET address handlers
application = tornado.web.Application([
		(r'/ws', WSHandler),
		(r'/api', APIHandler)
])
 
#starts the server on port 9998
if __name__ == "__main__":
	http_server = tornado.httpserver.HTTPServer(application)
	http_server.listen(9998)
	tornado.ioloop.IOLoop.instance().start()
